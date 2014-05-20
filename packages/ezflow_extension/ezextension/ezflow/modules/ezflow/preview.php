<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.1-0
// COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

define( 'ROTATION_NONE', 0 );
define( 'ROTATION_SIMPLE', 1 );
define( 'ROTATION_RANDOM', 2 );

$db = eZDB::instance();
$ini = eZINI::instance( 'block.ini' );

$blockTableDef = eZFlowBlock::definition();
$poolTableDef = eZFlowPoolItem::definition();
$blockTMPTable = $db->generateUniqueTempTableName( 'ezm_block_tmp_%' );
$poolTMPTable = $db->generateUniqueTempTableName( 'ezm_pool_tmp_%' );

$nodeID = 2;
$time = time() - 5;

if( isset( $Params['NodeID'] ) )
    $nodeID = (int)$Params['NodeID'];
if( isset( $Params['Time'] ) )
    $time = (int)$Params['Time'];

$db->createTempTable( 'CREATE TEMPORARY TABLE ' . $blockTMPTable .
                        ' AS SELECT * FROM ' . $blockTableDef['name'] .
                            ' WHERE node_id=\'' . $nodeID . '\'' );

$tmpBlocks = $db->arrayQuery( 'SELECT id FROM ' . $blockTMPTable );

if (!$tmpBlocks )
    eZExecution::cleanExit();

$tmpBlockIDArray = array();
foreach ( $tmpBlocks as $tmpBlock )
{
    $tmpBlockIDArray[] = "'" . $tmpBlock['id'] . "'";
}

$db->createTempTable( 'CREATE TEMPORARY TABLE ' . $poolTMPTable .
                        ' AS SELECT * FROM ' . $poolTableDef['name'] .
                            ' WHERE ' . $db->generateSQLINStatement( $tmpBlockIDArray, 'block_id' ) );

/* OPERATIONS CODE: START */

$nodeChanged = false;

$blocks = $db->arrayQuery( "SELECT *
                            FROM $blockTMPTable
                            WHERE node_id=$nodeID" );
$blockByID = array();

// Determine the order of updating
$correctOrder = array();
$next = array();

foreach ( $blocks as $block )
{
    $next[$block['id']] = $block['overflow_id'];
    $blockByID[$block['id']] = $block;
}

$nextIDs = array_keys( $next );
foreach ( $nextIDs as $id )
{
    if ( in_array( $id, $correctOrder, true ) )
    {
        continue;
    }

    if ( !$next[$id] )
    {
        $correctOrder[] = $id;
        continue;
    }

    $subCorrectOrder = array( $id );
    $currentID = $id;
    while ( ( $nextID = $next[$currentID] ) )
    {
        if ( !in_array( $nextID, $nextIDs, true ) )
        {
            eZDebug::writeWarning( "Overflow for $currentID is $nextID, but no such block was found for the given node", 'eZ Flow Preview view' );
            break;
        }
        if ( in_array( $nextID, $subCorrectOrder, true ) )
        {
            eZDebug::writeWarning( "Loop detected, ignoring ($nextID should be after $currentID and vice versa)", 'eZ Flow Preview view' );
            break;
        }
        if ( in_array( $nextID, $correctOrder, true ) )
        {
            break;
        }

        $subCorrectOrder[] = $nextID;
        $currentID = $nextID;
    }

    if ( !$nextID || !in_array( $nextID, $correctOrder, true ) )
    {
        foreach( $subCorrectOrder as $element )
        {
            $correctOrder[] = $element;
        }
    }
    else
    {
        $newCorrectOrder = array();
        foreach( $correctOrder as $element )
        {
            if ( $element === $nextID )
            {
                foreach( $subCorrectOrder as $element2 )
                {
                    $newCorrectOrder[] = $element2;
                }
            }
            $newCorrectOrder[] = $element;
        }
        $correctOrder = $newCorrectOrder;
    }
}

// Loop through all block in determined order
foreach ( $correctOrder as $blockID )
{
    if ( $blockByID[$blockID] )
    {
        $block = $blockByID[$blockID];
    }
    else
    {
        continue;
    }

    // Do we need to update block? No, continue to process next block
    $ttl = 0;
    if ( $ini->hasVariable( $block['block_type'], 'TTL' ) )
    {
        $ttl = $ini->variable( $block['block_type'], 'TTL' );
    }
    if ( $ttl + $block['last_update'] >= $time )
    {
        continue;
    }

    // For "rotating blocks", does the rotation_interval has passed from the last update?
    if ( $block['rotation_type'] != ROTATION_NONE &&
        $block['last_update'] + $block['rotation_interval'] >= $time )
    {
        continue;
    }

    $blockChanged = false;

    // Fetch new objects and add them to the queue of the current block
    //eZFlowOperations::updateBlockPoolByBlockID( $block['id'], $time );

    $db->begin();

    // We need to find out if there are any items to move from the queue
    $movingFromQueue = $db->arrayQuery( "SELECT object_id
                                         FROM $poolTMPTable
                                         WHERE block_id='$blockID'
                                           AND ts_visible=0
                                           AND ts_hidden=0
                                           AND ts_publication<=$time
                                         ORDER BY ts_publication ASC, priority ASC" );

    if ( $movingFromQueue )
    {
        $blockChanged = true;

        // Find out a number of items in "valid" state and the max. priority used
        $countMaxPriorityValid = $db->arrayQuery( "SELECT count(*) AS count, max(priority) AS priority
                                                   FROM $poolTMPTable
                                                   WHERE block_id='$blockID'
                                                       AND ts_visible>0
                                                       AND ts_hidden=0" );
        $countValid = $countMaxPriorityValid[0]['count'];
        $maxPriorityValid = $countMaxPriorityValid[0]['priority'];
        if ( $countValid == 0 )
        {
            $maxPriorityValid = 0;
        }

        $priority = $maxPriorityValid + 1;
        // Move objects waiting in queue to the "valid ones"
        foreach ( $movingFromQueue as $itemToMove )
        {
            $objectID = $itemToMove['object_id'];
            $db->query( "UPDATE $poolTMPTable
                             SET ts_visible=$time, priority=$priority
                             WHERE block_id='$blockID'
                               AND object_id=$objectID" );
            $priority++;
        }

        $countValid += count( $movingFromQueue );

        // Compare this number to the given and archive the oldest (order by ts_visible)
        $numberOfValidItems = $ini->variable( $block['block_type'], 'NumberOfValidItems' );
        if ( !$numberOfValidItems )
        {
            $numberOfValidItems = 20;
            eZDebug::writeWarning( 'Number of valid items for ' . $block['block_type'] .
                                   ' is not set; using the default value (' . $numberOfValidItems . ')', 'eZ Flow Preview view' );
        }

        $countToRemove = $countValid - $numberOfValidItems;
        if ( $countToRemove > 0 )
        {
            $overflowID = $block['overflow_id'];
            $items = $db->arrayQuery( "SELECT node_id, object_id, rotation_until
                                       FROM $poolTMPTable
                                       WHERE block_id='$blockID'
                                         AND ts_visible>0
                                         AND ts_hidden=0
                                       ORDER BY priority ASC", array( 'limit' => $countToRemove ) );

            if ( $items )
            {
                $itemArray = array();
                $priority = 0;
                foreach( $items as $item )
                {
                    $objectID = $item['object_id'];
                    if ( $block['rotation_type'] != ROTATION_NONE &&
                        ( $item['rotation_until'] > $time ||
                                $item['rotation_until'] == 0 ) )
                    {
                        if ( $block['rotation_type'] == ROTATION_SIMPLE )
                        {
                            // Simple rotation
                            $newPublicationTS = -$time;
                            $priority++;
                        }
                        else
                        {
                            // Random rotation/Shuffle
                            $newPublicationTS = 0;
                            $priority = mt_rand();
                        }
                        // Move item back to queue
                        $db->query( "UPDATE $poolTMPTable
                                     SET ts_visible=0,
                                         ts_publication=-$time,
                                         priority=$priority
                                     WHERE block_id='$blockID'
                                       AND object_id=$objectID" );

                    }
                    else
                    {
                        $itemArray[] = $objectID;
                    }
                }

                if ( $itemArray )
                {
                    if ( $overflowID )
                    {
                        // Put $itemArray items into pool of different block
                        $priority = 0;
                        foreach( $items as $item )
                        {
                            $objectID = $item['object_id'];
                            $itemNodeID = $item['node_id'];
                            // Check if the object_id is not already in the new block
                            $duplicityCheck = $db->arrayQuery( "SELECT object_id
                                                                FROM $poolTMPTable
                                                                WHERE block_id='$overflowID'
                                                                  AND object_id=$objectID", array( 'limit' => 1 ) );
                            if ( $duplicityCheck )
                            {
                                eZDebug::writeNotice( "Object $objectID is already available in the block $overflowID.", 'eZ Flow Preview view' );
                            }
                            else
                            {
                                $db->query( "INSERT INTO $poolTMPTable(block_id,object_id,node_id,ts_publication,priority)
                                                 VALUES ('$overflowID',$objectID,$itemNodeID,$time,$priority)" );
                                $priority++;
                            }
                        }

                        $db->query( "UPDATE $poolTMPTable
                                     SET ts_hidden=$time,
                                         moved_to='$overflowID',
                                         priority=0
                                     WHERE block_id='$blockID'
                                     AND " . $db->generateSQLINStatement( $itemArray, 'object_id' ) );
                    }
                    else
                    {
                        $db->query( "UPDATE $poolTMPTable
                                     SET ts_hidden=$time,
                                         priority=0
                                     WHERE block_id='$blockID'
                                       AND " . $db->generateSQLINStatement( $itemArray, 'object_id' ) );
                    }
                }
            }
        }

        // Cleanup in archived items
        $countArchived = $db->arrayQuery( "SELECT count(*) AS count
                                           FROM $poolTMPTable
                                           WHERE block_id='$blockID'
                                             AND ts_hidden>0" );
        $countArchived = $countArchived[0]['count'];

        // Compare this number to the given and remove the oldest ones
        $numberOfArchivedItems = $ini->variable( $block['block_type'], 'NumberOfArchivedItems' );
        if ( !$numberOfArchivedItems )
        {
            $numberOfArchivedItems = 50;
            eZDebug::writeWarning( 'Number of archived items for ' . $block['block_type'] .
                                    ' is not set; using the default value (' . $numberOfArchivedItems . ')', 'eZ Flow Preview view' );
        }
        $countToRemove = $countArchived - $numberOfArchivedItems;

        if ( $countToRemove > 0 )
        {
            $items = $db->arrayQuery( "SELECT object_id
                                       FROM $poolTMPTable
                                       WHERE block_id='$blockID'
                                         AND ts_hidden>0
                                       ORDER BY ts_hidden ASC", array( 'limit' => $countToRemove ) );

            if ( $items )
            {
                $itemArray = array();
                foreach( $items as $item )
                {
                    $itemArray[] = $item['object_id'];
                }
                $db->query( "DELETE FROM $poolTMPTable
                             WHERE block_id='$blockID'
                               AND " . $db->generateSQLINStatement( $itemArray, 'object_id' ) );
            }
        }
    }

    $db->commit();
}

/* OPERATIONS CODE: END */
$timelineINI = eZINI::instance('timeline.ini');
$tpl = eZTemplate::factory();
$httpCharset = eZTextCodec::httpCharset();
$node = eZContentObjectTreeNode::fetch( $nodeID );

$previewSettingsGroup = 'PreviewSettings_';
if( $node )
{
    $previewSettingsGroup = $previewSettingsGroup . $node->object()->attribute('class_identifier');
    $dataMap = $node->dataMap();
}

if ( $timelineINI->hasGroup( $previewSettingsGroup ) );
    $previewClassAttribute = $timelineINI->variable( $previewSettingsGroup, 'PreviewClassAttribute' );

if( isset( $dataMap[$previewClassAttribute] ) )
    $page = $dataMap[$previewClassAttribute]->attribute('content');

$zones = array();
if ( isset( $page ) && ( $page instanceof eZPage ) )
    $zones = $page->attribute('zones');

$output = array();
foreach ( $zones as $zone )
{
    $blocks = $zone->attribute('blocks');
    if( !$blocks )
        continue;

    foreach ( $blocks as $block )
    {
        $validNodes = $db->arrayQuery( "SELECT *
                                        FROM $poolTMPTable, ezcontentobject_tree
                                        WHERE $poolTMPTable.block_id='" . $block->attribute('id') . "'
                                          AND $poolTMPTable.ts_visible>0
                                          AND $poolTMPTable.ts_hidden=0
                                          AND ezcontentobject_tree.node_id = $poolTMPTable.node_id
                                        ORDER BY $poolTMPTable.priority DESC" );

        if( count( $validNodes ) )
        {
            $validNodesObjects = array();
            foreach( $validNodes as $validNode )
            {
                $validNodeID = $validNode['node_id'];
                $validNodesObjects[] = eZContentObjectTreeNode::fetch( $validNodeID );
            }
            $block->setAttribute( 'valid_nodes', $validNodesObjects );
        }

        $outputBlock = array( 'objectid' => $block->attribute('zone_id') . '-' . $block->attribute('id') );
        $tpl->setVariable( 'block', $block );
        $outputBlock['xhtml'] = htmlentities( str_replace( array( "\r\n", "\r", "\n" ), array(""), $tpl->fetch( 'design:page/preview.tpl' ) ), ENT_QUOTES, $httpCharset );
        $output[] = $outputBlock;
    }
}

header( 'Content-Type: application/json; charset=' . $httpCharset );

echo eZFlowAjaxContent::jsonEncode( $output );
eZExecution::cleanExit();

?>