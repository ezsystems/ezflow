<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.1.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2008 eZ Systems AS
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

include_once( 'extension/ezflow/classes/ezpageblock.php' );
include_once( 'kernel/common/template.php' );

define( 'ROTATION_NONE', 0 );
define( 'ROTATION_SIMPLE', 1 );
define( 'ROTATION_RANDOM', 2 );

$db = eZDB::instance();
$ini = eZINI::instance( 'block.ini' );

$salt = md5( microtime() . rand( 0, 100 ) );
$salt = substr( $salt, 0, 14 );
$blockTable = 'ezm_block';
$blockTMPTable = 'ezm_block_tmp_' . $salt;
$poolTable = 'ezm_pool';
$poolTMPTable = 'ezm_pool_tmp_' . $salt;
$nodeID = 2;
$time = time() - 5;

$nodeID = $Params['NodeID'];
$time = $Params['Time'];

if ( createTemporaryTable( $blockTable, $blockTMPTable ) )
{
    $insBlockSQL = 'INSERT INTO ' . $blockTMPTable . ' SELECT * FROM ' . $blockTable . ' WHERE node_id=\'' . $nodeID . '\'';
    $insBlockRes = $db->query( $insBlockSQL );

    if ( $insBlockRes )
    {
        if ( createTemporaryTable( $poolTable, $poolTMPTable ) )
        {
            $blocks = $db->arrayQuery( 'SELECT * FROM ' . $blockTMPTable );

            foreach ( $blocks as $block )
            {
                $insPoolSQL = 'INSERT INTO ' . $poolTMPTable . ' SELECT * FROM ' . $poolTable . ' WHERE block_id=\'' . $block['id'] . '\'';
                $db->query( $insPoolSQL );
            }

            /* CRONJOB CODE: START */

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
                        eZDebug::writeWarning( "Overflow for $currentID is $nextID, but no such block was found for the given node", 'eZ Flow Update Cronjob' );
                        break;
                    }
                    if ( in_array( $nextID, $subCorrectOrder, true ) )
                    {
                        eZDebug::writeWarning( "Loop detected, ignoring ($nextID should be after $currentID and vice versa)", 'eZ Flow Update Cronjob' );
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
                    ' is not set; using the default value (' . $numberOfValidItems . ')', 'eZ Flow Update Cronjob' );
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
                                            eZDebug::writeNotice( "Object $objectID is already available in the block $overflowID.", 'eZ Flow Update Cronjob' );
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
                                           AND object_id IN ( " . implode( ',', $itemArray ) . " )" );
                                }
                                else
                                {
                                    $db->query( "UPDATE $poolTMPTable
                                         SET ts_hidden=$time,
                                             priority=0
                                         WHERE block_id='$blockID'
                                           AND object_id IN ( " . implode( ',', $itemArray ) . " )" );
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
                    ' is not set; using the default value (' . $numberOfArchivedItems . ')', 'eZ Flow Update Cronjob' );
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
                                   AND object_id IN ( " . implode( ',', $itemArray ) . ")" );
                        }
                    }
                }

                // If the block changed, we need to update whole node
                if ( $blockChanged )
                {
                    $nodeChanged = true;
                }

                $db->commit();
            }

            if ( $nodeChanged )
            {
            }

            /* CRONJOB CODE: END */
        }
        else
        {
            //error
        }
    }
    else
    {
        //error
    }
}
else
{
    //error
}

$tpl = templateInit();
$httpCharset = eZTextCodec::httpCharset();
$node = eZContentObjectTreeNode::fetch( $nodeID );
$dataMap = $node->dataMap();
$page = $dataMap['page']->attribute('content');

$zones = $page->attribute('zones');
$output = '[';
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
        
        $output .= '{ \'objectid\':\'' . $block->attribute('zone_id') . '-' . $block->attribute('id') . '\', \'xhtml\':\'';
        $tpl->setVariable( 'block', $block );
        $output .= htmlentities( $tpl->fetch( 'design:page/preview.tpl' ), ENT_QUOTES, $httpCharset );
        $output .= '\'},';
    }
}
$output .= ']';
$output = str_replace( "\n", "", $output );

header( 'Content-Type: application/json; charset=' . $httpCharset );

echo $output;
eZExecution::cleanExit();

function createTemporaryTable( $srcTable, $destTable )
{
    $db = eZDB::instance();
    $showTableRes = $db->arrayQuery( 'SHOW CREATE TABLE ' . $srcTable );

    $createTableSQL = $showTableRes[0]['Create Table'];

    $createTMPTableSQL = str_replace( 'CREATE', 'CREATE TEMPORARY', $createTableSQL );
    $createTMPTableSQL = str_replace( $srcTable, $destTable, $createTMPTableSQL );

    $createTMPTableRes = $db->query( $createTMPTableSQL );

    if ( $createTMPTableRes )
    {
        return 1;
    }
    else
    {
        return 0;
    }

}

?>