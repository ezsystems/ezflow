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
class eZFlowOperations
{
    const ROTATION_NONE = 0;
    const ROTATION_SIMPLE = 1;
    const ROTATION_RANDOM = 2;

    static function updateBlockPoolByBlockID( $blockID, $publishedBeforeOrAt = false )
    {
        $db = eZDB::instance();
        $blockINI = eZINI::instance( 'block.ini' );

        if ( !$publishedBeforeOrAt )
        {
            $publishedBeforeOrAt = time();
        }

        $result = $db->arrayQuery( "SELECT * FROM ezm_block WHERE id='$blockID'" );

        if ( !$result )
        {
            eZDebug::writeWarning( "Block does not exist", "eZFlowOperations::updateBlockPoolByBlockID('$blockID')" );
            return false;
        }

        $result = $result[0];

        $blockType = $result['block_type'];

        if ( !$blockINI->hasVariable( $blockType, 'FetchClass' ) )
        {
            // Pure manual block, nothing is going to be fetched, but we need to update last_update as it is used for rotations
            $db->query( "UPDATE ezm_block SET last_update=$publishedBeforeOrAt WHERE id='$blockID'" );
            return 0;
        }

        $fetchClass = $blockINI->variable( $blockType, 'FetchClass' );
        @include_once( "extension/ezflow/classes/fetches/$fetchClass.php" );
        $fetchInstance = new $fetchClass();

        if ( !is_subclass_of( $fetchInstance, 'eZFlowFetchInterface' ) )
        {
            eZDebug::writeWarning( "Can't create an instance of the $fetchClass class", "eZFlowOperations::updateBlockPoolByBlockID('$blockID')" );
            return false;
        }

        $fetchFixedParameters = array();
        if ( $blockINI->hasVariable( $blockType, 'FetchFixedParameters' ) )
        {
            $fetchFixedParameters = $blockINI->variable( $blockType, 'FetchFixedParameters' );
        }
        $fetchParameters = unserialize( $result['fetch_params'] );
        $parameters = array_merge( $fetchFixedParameters, $fetchParameters );

        $newItems = $fetchInstance->fetch( $parameters, $result['last_update'], $publishedBeforeOrAt );

        // Update pool
        $db->begin();

        foreach( $newItems as $item )
        {
            $objectID = $item['object_id'];
            $nodeID = $item['node_id'];
            $publicationTS = $item['ts_publication'];

            $duplicityCheck = $db->arrayQuery( "SELECT object_id
                                                FROM ezm_pool
                                                WHERE block_id='$blockID'
                                                  AND object_id=$objectID
                                                LIMIT 1" );
            if ( $duplicityCheck )
            {
                eZDebug::writeNotice( "Object $objectID is already available in the block $blockID.", 'eZFlowOperations' );
            }
            else
            {
                $db->query( "INSERT INTO ezm_pool(block_id,object_id,node_id,ts_publication) VALUES ('$blockID',$objectID,$nodeID,$publicationTS)" );
            }
        }

        $db->query( "UPDATE ezm_block SET last_update=$publishedBeforeOrAt WHERE id='$blockID'" );

        $db->commit();

        return count( $newItems );
    }

    static function update()
    {
        include_once( 'kernel/classes/ezcontentcache.php' );

        $ini = eZINI::instance( 'block.ini' );
        $db = eZDB::instance();

        // Remove the blocks and items for the block if marked for removal
        $res = $db->arrayQuery( "SELECT id
                         FROM ezm_block
                         WHERE is_removed=1" );
        foreach ( $res as $row )
        {
            $blockID = $row['id'];
            $db->begin();
            $db->query( "DELETE FROM ezm_pool
                 WHERE block_id='$blockID'" );
            $db->query( "DELETE FROM ezm_block
                 WHERE id='$blockID'" );
            $db->commit();
        }

        // Update pool and pages for all nodes
        $res = $db->arrayQuery( "SELECT DISTINCT node_id
                         FROM ezm_block" );

        foreach ( $res as $row )
        {
            $time = time() - 5; // a safety margin

            $nodeChanged = false;
            $nodeID = $row['node_id'];

            $blocks = $db->arrayQuery( "SELECT *
                                FROM ezm_block
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
                if ( $block['rotation_type'] != self::ROTATION_NONE &&
                $block['last_update'] + $block['rotation_interval'] >= $time )
                {
                    continue;
                }

                $blockChanged = false;

                // Fetch new objects and add them to the queue of the current block
                eZFlowOperations::updateBlockPoolByBlockID( $block['id'], $time );

                $db->begin();

                // We need to find out if there are any items to move from the queue
                $movingFromQueue = $db->arrayQuery( "SELECT object_id
                                             FROM ezm_pool
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
                                                       FROM ezm_pool
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
                        $db->query( "UPDATE ezm_pool
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
                                           FROM ezm_pool
                                           WHERE block_id='$blockID'
                                             AND ts_visible>0
                                             AND ts_hidden=0
                                           ORDER BY priority ASC
                                           LIMIT $countToRemove" );

                        if ( $items )
                        {
                            $itemArray = array();
                            $priority = 0;
                            foreach( $items as $item )
                            {
                                $objectID = $item['object_id'];
                                if ( $block['rotation_type'] != self::ROTATION_NONE &&
                                ( $item['rotation_until'] > $time ||
                                $item['rotation_until'] == 0 ) )
                                {
                                    if ( $block['rotation_type'] == self::ROTATION_SIMPLE )
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
                                    $db->query( "UPDATE ezm_pool
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
                                        $itemObjectID = $item['object_id'];
                                        $itemNodeID = $item['node_id'];
                                        // Check if the object_id is not already in the new block
                                        $duplicityCheck = $db->arrayQuery( "SELECT object_id
                                                                    FROM ezm_pool
                                                                    WHERE block_id='$overflowID'
                                                                      AND object_id=$itemObjectID
                                                                    LIMIT 1" );
                                        if ( $duplicityCheck )
                                        {
                                            eZDebug::writeNotice( "Object $itemObjectID is already available in the block $overflowID.", 'eZ Flow Update Cronjob' );
                                        }
                                        else
                                        {
                                            $db->query( "INSERT INTO ezm_pool(block_id,object_id,node_id,ts_publication,priority)
                                                 VALUES ('$overflowID',$itemObjectID,$itemNodeID,$time,$priority)" );
                                            $priority++;
                                        }
                                    }

                                    $db->query( "UPDATE ezm_pool
                                         SET ts_hidden=$time,
                                             moved_to='$overflowID',
                                             priority=0
                                         WHERE block_id='$blockID'
                                           AND object_id IN ( " . implode( ',', $itemArray ) . " )" );
                                }
                                else
                                {
                                    $db->query( "UPDATE ezm_pool
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
                                               FROM ezm_pool
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
                                           FROM ezm_pool
                                           WHERE block_id='$blockID'
                                             AND ts_hidden>0
                                           ORDER BY ts_hidden ASC
                                           LIMIT $countToRemove" );

                        if ( $items )
                        {
                            $itemArray = array();
                            foreach( $items as $item )
                            {
                                $itemArray[] = $item['object_id'];
                            }
                            $db->query( "DELETE FROM ezm_pool
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
                eZContentCache::cleanup( array( $nodeID ) );
            }
        }

    }
}

?>