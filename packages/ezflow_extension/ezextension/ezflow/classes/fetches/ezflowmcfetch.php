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
        $subTreeParameters['SortBy'] = array( 'published', true ); // first the oldest
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