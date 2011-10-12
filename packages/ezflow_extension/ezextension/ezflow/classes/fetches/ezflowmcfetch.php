<?php

class eZFlowMCFetch implements eZFlowFetchInterface
{
    public function fetch( $parameters, $publishedAfter, $publishedBeforeOrAt )
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
        $subTreeParameters['SortBy'] = array( 'published', false ); // first the latest
        $subTreeParameters['AttributeFilter'] = array(
            'and',
            array( 'published', '>', $publishedAfter ),
            array( 'published', '<=', $publishedBeforeOrAt )
        );

        if ( isset( $parameters['Classes'] ) )
        {
            $subTreeParameters['ClassFilterType'] = 'include';
            $subTreeParameters['ClassFilterArray'] = explode( ',', $parameters['Classes'] );
        }
        
        // Do not fetch hidden nodes even when ShowHiddenNodes=true
        $subTreeParameters['AttributeFilter'] = array( 'and', array( 'visibility', '=', true ) );

        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $subTreeParameters, $nodeID );
        
        if ( $nodes === null )
            return array();
        
        $fetchResult = array();
        foreach( $nodes as $node )
        {
            $fetchResult[] = array( 'object_id' => $node['contentobject_id'],
                                    'node_id' => $node['node_id'],
                                    'ts_publication' => $node['published'] );
        }

        return $fetchResult;
    }
}

?>
