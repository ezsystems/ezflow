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

class eZFlowPool
{
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

    static function validNodes( $blockID, $asObject = true )
    {
        include_once( 'kernel/classes/ezcontentobjecttreenode.php' );

        $db = eZDB::instance();
        $validNodes = $db->arrayQuery( "SELECT *
                                        FROM ezm_pool, ezcontentobject_tree
                                        WHERE ezm_pool.block_id='$blockID'
                                          AND ezm_pool.ts_visible>0
                                          AND ezm_pool.ts_hidden=0
                                          AND ezcontentobject_tree.node_id = ezm_pool.node_id
                                        ORDER BY ezm_pool.priority DESC" );
        if ( $asObject )
        {
            $validNodesObjects = array();
            foreach( $validNodes as $node )
            {
                $nodeID = $node['node_id'];
                $validNodesObjects[] = eZContentObjectTreeNode::fetch( $nodeID );
            }
            return $validNodesObjects;
        }
        else
        {
            return $validNodes;
        }
    }

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
}

?>