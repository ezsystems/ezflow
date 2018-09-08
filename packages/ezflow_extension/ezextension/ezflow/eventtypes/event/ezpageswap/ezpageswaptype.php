<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 2.4-0
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

class eZPageSwapType extends eZWorkflowEventType
{

    const WORKFLOW_TYPE_STRING = 'ezpageswap';

    function __construct()
    {
        parent::__construct(
            self::WORKFLOW_TYPE_STRING,
            ezpI18n::tr( 'kernel/workflow/event', 'eZ Page swap workflow event' )
        );
        $this->setTriggerTypes( array( 'content' => array( 'swap' => array ( 'after' ) ) ) );
    }

    function execute( $process, $event )
    {
        $db = eZDB::instance();
        $db->begin();
        $parameters = $process->attribute( 'parameter_list' );
        
        $oldNodeID = $parameters['node_id'];
        $newNodeID = $parameters['selected_node_id'];
        
        // Fetch blocks which require node_id update
        $oldNodeBlocks = eZFlowBlock::fetchObjectList( eZFlowBlock::definition(), null, array( 'node_id' => $oldNodeID ) );
        $newNodeBlocks = eZFlowBlock::fetchObjectList( eZFlowBlock::definition(), null, array( 'node_id' => $newNodeID ) );
        
        // Loop over fetched blocks for old node and update node_id with new value
        foreach( $oldNodeBlocks as $oldNodeBlock )
        {
            $oldNodeBlock->setAttribute( 'node_id', $newNodeID );
            $oldNodeBlock->store();
        }

        // Loop over fetched blocks for new node and update node_id with new value
        foreach( $newNodeBlocks as $newNodeBlock )
        {
            $newNodeBlock->setAttribute( 'node_id', $oldNodeID );
            $newNodeBlock->store();
        }

        // Get the object IDs to update the pool items
        $oldNode = eZContentObjectTreeNode::fetch( $oldNodeID );
        $oldNodeObjectID = $oldNode->attribute( 'contentobject_id' );
        $newNode = eZContentObjectTreeNode::fetch( $newNodeID ); 
        $newNodeObjectID = $newNode->attribute( 'contentobject_id' );
        
        // Update with the new object IDs
        // Object IDs are keys, so we have to watch out for the case where the swapped nodes are part of the same block, in which case we need to swap the node IDs instead
        $swappedBlockIDs = array();
        $oldNodePoolItems = eZFlowPoolItem::fetchObjectList( eZFlowPoolItem::definition(), null, array( 'node_id' => $oldNodeID ) );
        $newNodePoolItems = eZFlowPoolItem::fetchObjectList( eZFlowPoolItem::definition(), null, array( 'node_id' => $newNodeID ) );

        // Loop over fetched pool items for old node
        foreach( $oldNodePoolItems as $oldNodePoolItem )
        {
            // Check if the swapped node is part of the same block
            $swappedNodePoolItem = eZFlowPoolItem::fetchObjectList( eZFlowPoolItem::definition(), null, array( 'node_id' => $newNodeID, 'block_id' => $oldNodePoolItem->attribute( 'block_id' ) ) );
            if( empty( $swappedNodePoolItem ) )
            {
                $updateParameters = array(
                                          'definition' => eZFlowPoolItem::definition(),
                                          'update_fields' => array( 'object_id' => $oldNodeObjectID ),
                                          'conditions' => array(
                                                                'object_id' => $oldNodePoolItem->attribute( 'object_id' ),
                                                                'block_id' => $oldNodePoolItem->attribute( 'block_id' )
                                                               )
                                         );
                eZFlowPoolItem::updateObjectList( $updateParameters );
            }
            else
            {
                // Swap node IDs
                $oldNodePoolItem->setAttribute( 'node_id', $newNodeID );
                $oldNodePoolItem->store();
                $swappedNodePoolItem[0]->setAttribute( 'node_id', $oldNodeID );
                $swappedNodePoolItem[0]->store();
                // Do not process this block ID again
                $swappedBlockIDs[] = $oldNodePoolItem->attribute( 'block_id' );
            }
        }

        // Loop over fetched pool items for new node
        foreach( $newNodePoolItems as $newNodePoolItem )
        {
            // Don't process this block if both nodes IDs were already updated
            if( in_array( $newNodePoolItem->attribute( 'block_id' ), $swappedBlockIDs ) )
            {
                continue;
            }
            $updateParameters = array(
                                      'definition' => eZFlowPoolItem::definition(),
                                      'update_fields' => array( 'object_id' => $newNodeObjectID ),
                                      'conditions' => array(
                                                            'object_id' => $newNodePoolItem->attribute( 'object_id' ),
                                                            'block_id' => $newNodePoolItem->attribute( 'block_id' )
                                                           )
                                     );
            eZFlowPoolItem::updateObjectList( $updateParameters );
        }

        $db->commit();
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( eZPageSwapType::WORKFLOW_TYPE_STRING, 'eZPageSwapType' );

?>
