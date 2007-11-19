<?php

include_once( 'extension/ezmedia/classes/ezmFetchInterface.php' );
//include_once( 'kernel/classes/ezcontentobjecttreenode.php' );

class ezmLatestObjects extends ezmFetchInterface
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

        $result = eZContentObjectTreeNode::subTree( $subTreeParameters, $nodeID );
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