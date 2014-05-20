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
        
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( eZPageSwapType::WORKFLOW_TYPE_STRING, 'eZPageSwapType' );

?>
