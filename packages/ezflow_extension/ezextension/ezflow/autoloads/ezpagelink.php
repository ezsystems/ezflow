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

/**
 * eZPageLink class implements pagelink tpl operator methods
 * 
 */
class eZPageLink
{
    /**
     * Constructor
     * 
     */
    public function __construct()
    {
    }

    /**
     * Return an array with the template operator name.
     * 
     * @return array
     */
    public function operatorList()
    {
        return array( 'pagelink' );
    }

    /**
     * Return true to tell the template engine that the parameter list exists per operator type,
     * this is needed for operator classes that have multiple operators.
     * 
     * @return bool
     */
    public function namedParameterPerOperator()
    {
        return true;
    }

    /**
     * Returns an array of named parameters, this allows for easier retrieval
     * of operator parameters. This also requires the function modify() has an extra
     * parameter called $namedParameters.
     * 
     * @return array
     */
    public function namedParameterList()
    {
        return array( 'pagelink' => array( 'object_id' => array( 'integer',
                                                                 'required' => true,
                                                                 'default' => '' ) ) );
    }

    /**
     * Executes the PHP function for the operator cleanup and modifies $operatorValue.
     * 
     * @param eZTemplate $tpl
     * @param string $operatorName
     * @param array $operatorParameters
     * @param string $rootNamespace
     * @param string $currentNamespace
     * @param mixed $operatorValue
     * @param array $namedParameters
     */
    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $objectId = $namedParameters['object_id'];

        switch ( $operatorName )
        {
            case 'pagelink':
            {
                $links = array();

                $ini = eZINI::instance( 'block.ini' );
                $items = eZFlowPoolItem::fetchListByContentObjectId( $objectId );

                foreach ( $items as $item )
                {
                    $block = $item->attribute( 'block' );
                    $node = $block->attribute( 'node' );

                    if ( !$node->attribute( 'can_read' ) )
                        continue;

                    $nodeId = $node->attribute( 'node_id' );

                    $links[$nodeId]['node'] = $node;

                    if ( $block->attribute( 'name' ) == '' )
                        $block->setAttribute( 'name', $ini->variable( $block->attribute( 'block_type' ), 'Name' ) );

                    $links[$nodeId]['blocks'][] = $block;
                }

                $operatorValue = $links;
            } break;
        }
    }
}

?>
