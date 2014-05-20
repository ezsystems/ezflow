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

class eZFlowPool
{
    /**
     * Return waiting items for block with given $blockID
     * 
     * @static
     * @param string $blockID
     * @return array
     */
    static function waitingItems( $blockID )
    {
        $db = eZDB::instance();
        $queue = $db->arrayQuery( "SELECT *
                                   FROM ezm_pool
                                   WHERE block_id='$blockID'
                                     AND ts_visible=0
                                     AND ts_hidden=0
                                   ORDER BY ts_publication ASC, priority ASC" );
        return $queue;
    }

    /**
     * Return valid items for block with given $blockID
     * 
     * @static
     * @param string $blockID
     * @return array
     */
    static function validItems( $blockID )
    {
        $db = eZDB::instance();
        $valid = $db->arrayQuery( "SELECT *
                                   FROM ezm_pool
                                   WHERE block_id='$blockID'
                                     AND ts_visible>0
                                     AND ts_hidden=0
                                   ORDER BY priority DESC" );
        return $valid;
    }

    /**
     * Return valid items for block with given $blockID
     * 
     * @static
     * @param string $blockID
     * @param bool $asObject
     * @return array(eZContentObjectTreeNode)
     */
    static function validNodes( $blockID, $asObject = true )
    {
        if ( isset( $GLOBALS['eZFlowPool'] ) === false )
            $GLOBALS['eZFlowPool'] = array();

        if ( isset( $GLOBALS['eZFlowPool'][$blockID] ) )
            return $GLOBALS['eZFlowPool'][$blockID];

        $visibilitySQL = "";

        if ( eZINI::instance( 'site.ini' )->variable( 'SiteAccessSettings', 'ShowHiddenNodes' ) !== 'true' )
        {
            $visibilitySQL = "AND ezcontentobject_tree.is_invisible = 0 ";
        }

        $db = eZDB::instance();
        $validNodes = $db->arrayQuery( "SELECT ezm_pool.node_id
                                        FROM ezm_pool, ezcontentobject_tree, ezcontentobject
                                        WHERE ezm_pool.block_id='$blockID'
                                          AND ezm_pool.ts_visible>0
                                          AND ezm_pool.ts_hidden=0
                                          AND ezcontentobject_tree.node_id = ezm_pool.node_id
                                          AND ezcontentobject.id = ezm_pool.object_id
                                          AND " . eZContentLanguage::languagesSQLFilter( 'ezcontentobject' ) . "
                                          $visibilitySQL
                                        ORDER BY ezm_pool.priority DESC" );

        if ( $asObject && !empty( $validNodes ) )
        {
            $validNodesObjects = array();

            foreach( $validNodes as $node )
            {
                $validNodeObject = eZContentObjectTreeNode::fetch( $node['node_id'] );
                if ( $validNodeObject instanceof eZContentObjectTreeNode && $validNodeObject->canRead() )
                    $validNodesObjects[] = $validNodeObject;
            }

            $GLOBALS['eZFlowPool'][$blockID] = $validNodesObjects;

            return $validNodesObjects;
        }
        else
        {
            return $validNodes;
        }
    }

    /**
     * Return archived items for block with given $blockID
     * 
     * @static
     * @param string $blockID
     * @return array
     */
    static function archivedItems( $blockID )
    {
        $db = eZDB::instance();
        $archived = $db->arrayQuery( "SELECT *
                                      FROM ezm_pool
                                      WHERE block_id='$blockID'
                                        AND ts_hidden>0
                                      ORDER BY ts_hidden ASC" );
        return $archived;
    }

    /**
     * Insert items in the pool
     *
     * @param array $items Array of items to insert in the pool.
     *                     The following information must appear for every item:
     *                     blockID, objectID, nodeID, priority and timestamp.
     *
     * @return bool Returns true if the operation suceeded, false otherwise.
     */
    static function insertItems( array $items )
    {
        // Checking the validity of items.
        foreach ( $items as $item )
        {
            if ( !isset( $item['blockID'], $item['objectID'], $item['nodeID'], $item['priority'], $item['timestamp'] ) )
            {
                eZDebug::writeError( "Pool item is missing one of the following information: blockID, objectID, nodeID, priority or timestamp", __METHOD__ );
                return false;
            }
        }

        $db = eZDB::instance();

        if ( $db->databaseName() === 'mysql' )
        {
            // MySQL permits inserting elements without complaining about duplicates thanks to "INSERT IGNORE".
            // additionally, it support multiple inserts which may improve performance a lot.
            // @see #017120
            $values = array();

            foreach ( $items as $item )
            {
                $values[] = "( '" . $db->escapeString( $item['blockID'] ) . "', " .
                    (int)$item['objectID'] . ", " .
                    (int)$item['nodeID'] . ", " .
                    (int)$item['priority'] . ", " .
                    (int)$item['timestamp'] . " )";
            }

            if ( !empty( $values ) )
            {
                $db->query( "INSERT IGNORE INTO ezm_pool ( block_id, object_id, node_id, priority, ts_publication ) VALUES " .
                    implode( ',', $values ) );
            }
        }
        else
        {
            $db->lock( 'ezm_pool' );

            foreach ( $items as $item )
            {
                $escapedBlockID = $db->escapeString( $item['blockID'] );

                $itemCount = $db->arrayQuery(
                    "SELECT COUNT( * ) as count " .
                    "FROM ezm_pool " .
                    "WHERE block_id='$escapedBlockID' AND object_id=" . (int)$item['objectID'] );

                if ( $itemCount[0]['count'] == 0 )
                {
                    $db->query( "INSERT INTO ezm_pool ( block_id, object_id, node_id, priority, ts_publication ) " .
                                "VALUES ( '$escapedBlockID', " .
                                    (int)$item['objectID'] . ", " .
                                    (int)$item['nodeID'] . ", " .
                                    (int)$item['priority'] . ", " .
                                    (int)$item['timestamp'] .
                                " )" );
                }
            }

            $db->unlock();
        }

        return true;
    }
}

?>