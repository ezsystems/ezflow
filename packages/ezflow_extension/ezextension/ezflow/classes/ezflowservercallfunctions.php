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