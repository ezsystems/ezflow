<?php

/**
 * Implements methods called remotely by sending XHR calls
 * 
 */
class eZFlowServerCallFunctions
{
    /**
     * Returns statistics about users which are currently online 
     * 
     * @param mixed $args
     * @return array
     */
    public static function onlineUsers( $args )
    {
        $result = array();
        
        $result['logged_in_count'] = eZFunctionHandler::execute( 'user', 'logged_in_count', array() );
        $result['anonymous_count'] = eZFunctionHandler::execute( 'user', 'anonymous_count', array() );
        
        return $result;
    }

    /**
     * Returns search results based on given params 
     * 
     * @param mixed $args
     * @return array
     */
    public static function search( $args )
    {
        $http = eZHTTPTool::instance();

        if ( $http->hasPostVariable( 'SearchStr' ) )
            $searchStr = trim( $http->postVariable( 'SearchStr' ) );

        $searchOffset = 0;
        if ( $http->hasPostVariable( 'SearchOffset' ))
            $searchOffset = (int) $http->postVariable( 'SearchOffset' );

        $searchLimit = 10;
        if ( $http->hasPostVariable( 'SearchLimit' ))
            $searchLimit = (int) $http->postVariable( 'SearchLimit' );

        if ( $searchLimit > 30 ) $searchLimit = 30;

        //Preper the search params
        $param = array( 'SearchOffset' => $searchOffset,
                        'SearchLimit' => $searchLimit+1,
                        'SortArray' => array('published', 0)
                      );

        // if no checkbox select class_attr first if valid
        if ( $http->hasPostVariable( 'SearchContentClassAttributeID' ) && $http->postVariable( 'SearchContentClassAttributeID' ) )
        {
            $param['SearchContentClassAttributeID'] = explode( ',', $http->postVariable( 'SearchContentClassAttributeID' ) );
        }
        elseif ( $http->hasPostVariable( 'SearchContentClassID' ) && $http->postVariable( 'SearchContentClassID' ) )
        {
            $param['SearchContentClassID'] = explode( ',', $http->postVariable( 'SearchContentClassID' ) );
        }

        if ( $http->hasPostVariable( 'SearchSubTreeArray' ) && $http->postVariable( 'SearchSubTreeArray' ) )
        {
            $param['SearchSubTreeArray'] = explode( ',', $http->postVariable( 'SearchSubTreeArray' ) );
        }

        if ( $http->hasPostVariable( 'SearchSectionID' ) && $http->postVariable( 'SearchSectionID' ) )
        {
            $param['SearchSectionID'] = explode( ',', $http->postVariable( 'SearchSectionID' ) );
        }

        if ( $http->hasPostVariable( 'SearchDate' ) && $http->postVariable( 'SearchDate' ) )
        {
            $param['SearchDate'] = (int) $http->postVariable( 'SearchDate' );
        }    
        else if ( $http->hasPostVariable( 'SearchTimestamp' ) && $http->postVariable( 'SearchTimestamp' ) )
        {
            $param['SearchTimestamp'] = explode( ',', $http->postVariable( 'SearchTimestamp' ) );
            if ( isset( $param['SearchTimestamp'][0] ) && !isset( $param['SearchTimestamp'][1] ) )
                $param['SearchTimestamp'] = $param['SearchTimestamp'][0];
        }

        $searchList = eZSearch::search( $searchStr, $param );

        $result = array();
        $result['SearchResult'] = eZFlowAjaxContent::nodeEncode( $searchList['SearchResult'], array(), false );
        $result['SearchCount'] = $searchList['SearchCount'];
        $result['SearchOffset'] = $searchOffset;
        $result['SearchLimit'] = $searchLimit;

        return $result;
    }

    /**
     * Returns block item XHTML 
     * 
     * @param mixed $args
     * @return array
     */
    public static function getValidItems( $args )
    {
        include_once( 'kernel/common/template.php' );
        
        $http = eZHTTPTool::instance();
        $tpl = templateInit();

        $result = array();
        
        $blockID = $http->postVariable('block_id');
        $offset = $http->postVariable('offset');
        $limit = $http->postVariable('limit');

        $validNodes = eZFlowPool::validNodes( $blockID );
        $counter = 0;
        foreach( $validNodes as $validNode )
        {
            $counter++;
            
            if ( $counter <= $offset )
                continue;
            
            $tpl->setVariable('node', $validNode);
            $tpl->setVariable('view', 'block_item');
            $tpl->setVariable('image_class', 'blockgallery1');
            $content = $tpl->fetch('design:node/view/view.tpl');
            
            $result[] = $content;
            
            if ( $counter === $limit )
                break;
        }

        return $result;
    }

    /**
     * Update blocks order based on AJAX data send after D&D operation is finished
     * 
     * @param mixed $args
     * @return array
     */
    public static function updateblockorder( $args )
    {
        $http = eZHTTPTool::instance();

        if ( $http->hasPostVariable( 'contentobject_attribute_id' ) )
            $contentObjectAttributeID = $http->postVariable( 'contentobject_attribute_id' );

        if ( $http->hasPostVariable( 'version' ) )
            $version = $http->postVariable( 'version' );

        if ( $http->hasPostVariable( 'zone' ) )
            $zoneID = $http->postVariable( 'zone' );

        if ( $http->hasPostVariable( 'block_order' ) )
            $blockOrder = $http->postVariable( 'block_order' );

        $contentObjectAttribute = eZContentObjectAttribute::fetch( $contentObjectAttributeID, $version );
        $sortArray = array();
        foreach ( $blockOrder as $blockID )
        {
            $idArray = explode('_', $blockID);

            if ( isset( $idArray[1]) )
                $sortArray[] = $idArray[1];
        }

        if ( $contentObjectAttribute )
            $page = $contentObjectAttribute->content();
        if ( $page )
            $zone = $page->getZone( $zoneID );
        if ( $zone )
            $zone->sortBlocks( $sortArray );

        $contentObjectAttribute->setContent( $page );
        $contentObjectAttribute->store();

        return array();
    }
}

?>