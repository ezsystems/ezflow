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

include_once( 'extension/ezflow/classes/ezflowfetchinterface.php' );
//include_once( 'kernel/classes/ezcontentobjecttreenode.php' );

class eZFlowLatestObjects extends eZFlowFetchInterface
{
    function fetch( $parameters, $publishedAfter, $publishedBeforeOrAt )
    {
        if ( isset( $parameters['Source'] ) )
        {
            $nodeID = $parameters['Source'];
            $node = eZContentObjectTreeNode::fetch( $nodeID, false, false ); // not as an object
            if ( $node && $node['modified_subnode'] <= $publishedAfter )
            {
                return array();
            }
        }
        else
        {
            $nodeID = 0;
        }

        $subTreeParameters = array();
        $subTreeParameters['AsObject'] = false;
        $subTreeParameters['SortBy'] = array( 'published', true ); // first the oldest
        $subTreeParameters['AttributeFilter'] = array(
            'and',
            array( 'published', '>', $publishedAfter ),
            array( 'published', '<=', $publishedBeforeOrAt )
        );

        if ( isset( $parameters['Class'] ) )
        {
            $subTreeParameters['ClassFilterType'] = 'include';
            $subTreeParameters['ClassFilterArray'] = explode( ';', $parameters['Class'] );
        }

        $result = eZContentObjectTreeNode::subTreeByNodeID( $subTreeParameters, $nodeID );
        $fetchResult = array();
        foreach( $result as $item )
        {
            $fetchResult[] = array( 'object_id' => $item['contentobject_id'],
                                    'node_id' => $item['node_id'],
                                    'ts_publication' => $item['published'] );
        }

        return $fetchResult;
    }
}

?>