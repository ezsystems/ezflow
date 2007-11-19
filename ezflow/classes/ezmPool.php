<?php

class ezmPool
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