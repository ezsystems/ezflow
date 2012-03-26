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
     * Returns block item XHTML
     *
     * @param mixed $args
     * @return array
     */
    public static function getValidItems( $args )
    {
        $http = eZHTTPTool::instance();
        $tpl = eZTemplate::factory();

        $result = array();

        $blockID = $http->postVariable('block_id');
        $offset = $http->postVariable('offset');
        $limit = $http->postVariable('limit');

        $validNodes = eZFlowPool::validNodes( $blockID );
        $counter = 0;
        foreach( $validNodes as $validNode )
        {
            if ( !$validNode->attribute( 'can_read' ) )
                continue;
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

        $contentObjectAttributeID = (int)$http->postVariable( 'contentobject_attribute_id', 0 );
        $version =(int)$http->postVariable( 'version', 0 );
        $zoneID = $http->postVariable( 'zone', '' );
        $blockOrder = $http->postVariable( 'block_order', array() );

        $contentObjectAttribute = eZContentObjectAttribute::fetch( $contentObjectAttributeID, $version );
        if ( !$contentObjectAttribute instanceof eZContentObjectAttribute )
        {
            return array();
        }
        $contentObject = $contentObjectAttribute->attribute( 'object' );
        if ( !$contentObject->attribute( 'can_edit' ) )
        {
            return array();
        }

        // checking that the version is a draft and belongs to the current user
        $contentVersion = $contentObjectAttribute->attribute( 'object_version' );
        if ( $contentVersion->attribute( 'status' ) != eZContentObjectVersion::STATUS_DRAFT &&
                $contentVersion->attribute( 'status' ) != eZContentObjectVersion::STATUS_INTERNAL_DRAFT
           )
        {
            return array();
        }
        if ( $contentVersion->attribute( 'creator_id' ) != eZUser::currentUserID() )
        {
            return array();
        }

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
