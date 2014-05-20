<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 2.0-0
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

class eZFlowKeywordsFetch implements eZFlowFetchInterface
{
    public function fetch( $parameters, $publishedAfter, $publishedBeforeOrAt )
    {
        $ini = eZINI::instance( 'block.ini' );

        $limit = 5;
        if ( $ini->hasVariable( 'Keywords', 'NumberOfValidItems' ) )
            $limit = $ini->variable( 'Keywords', 'NumberOfValidItems' );

        if ( isset( $parameters['Source'] ) && $parameters['Source'] != '' )
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
            $nodeID = false;
        }

        $sortBy = array( 'published', false );

        $classIDs =  array();
        if ( isset( $parameters['Classes'] ) && $parameters['Classes'] != '' )
        {
            $classIdentifiers = explode( ',', $parameters['Classes'] );
            foreach( $classIdentifiers as $classIdentifier )
            {
                $class = eZContentClass::fetchByIdentifier( $classIdentifier, false ); // not as an object
                if( $class )
                {
                    $classIDs[] = $class['id'];
                }
            }
        }

        if ( isset( $parameters['Keywords'] ) )
        {
            $keywords = $parameters['Keywords'];
        }

        $result = eZFunctionHandler::execute( 'content','keyword', array( 'alphabet' => $keywords,
                                                                          'classid' => $classIDs,
                                                                          'offset' => 0,
                                                                          'limit' => $limit,
                                                                          'parent_node_id' => $nodeID,
                                                                          'include_duplicates' => false,
                                                                          'sort_by' => $sortBy,
                                                                          'strict_matching' => false ) );

        if ( $result === null )
            return array();

        $fetchResult = array();
        foreach( $result as $item )
        {
            $fetchResult[] = array( 'object_id' => $item['link_object']->attribute( 'contentobject_id' ),
                                    'node_id' => $item['link_object']->attribute( 'node_id' ),
                                    'ts_publication' => $item['link_object']->object()->attribute( 'published' ) );
        }

        return $fetchResult;
    }
}

?>
