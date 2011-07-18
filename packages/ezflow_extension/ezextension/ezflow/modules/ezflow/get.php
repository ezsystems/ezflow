<?php

$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'content' ) )
{
    $res = array();
    $page = false;
    $zoneName = array();
    $classIdentifier = false;
    
    if ( $http->hasPostVariable( 'frontpage_node_id' ) )
    {
        $frontPageNode = eZContentObjectTreeNode::fetch( $http->postVariable( 'frontpage_node_id' ) );
        
        if ( $frontPageNode instanceof eZContentObjectTreeNode )
            $frontPageObject = $frontPageNode->object();

        $dataMap = array();
        if ( $frontPageObject instanceof eZContentObject )
            $dataMap = $frontPageObject->dataMap();

        foreach( $dataMap as $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' )  === 'ezpage' )
            {
                $page = $attribute->content();
                $ini = eZINI::instance('zone.ini');
                $zoneName = $ini->variable( $page->attribute( 'zone_layout' ), 'ZoneName' );

                break;
            }
        }
    }

    if ( $http->hasPostVariable( 'node_id' ) )
    {
        $nodeToAdd = eZContentObjectTreeNode::fetch( $http->postVariable( 'node_id' ) );

        $objectToAdd = null;
        if ( $nodeToAdd instanceof eZContentObjectTreeNode )
            $objectToAdd = $nodeToAdd->object();

        if ( $objectToAdd instanceof eZContentObject )
        {
            $classIdentifier = eZContentClass::classIdentifierByID( $objectToAdd->attribute( 'contentclass_id' ) );
        }
    }

    switch ( $http->postVariable( 'content' ) ) 
    {
        case 'frontpage':

            if ( $page instanceof eZPage )
            {
                foreach ( $page->attribute( 'zones' ) as $index => $zone )
                {
                    $identifier = $zone->attribute( 'zone_identifier' );

                    $res[] = array( 'index' => $index,
                                    'id' => $zone->attribute( 'id' ),
                                    'name' => $zoneName[$identifier],
                                    'zone_identifier' => $identifier );
                }
            }
            
            break;

        case 'zone':
            $blockINI = eZINI::instance( 'block.ini' );
            
            if ( $http->hasPostVariable( 'zone' ) ) 
            {
                $zoneID = $http->postVariable( 'zone' );
                
                if ( $page instanceof eZPage )
                {
                    foreach ( $page->attribute( 'zones' ) as $zone )
                    {
                        if ( $zone->attribute('id') === $zoneID )
                        {
                            foreach ( $zone->attribute( 'blocks' ) as $index => $block )
                            {
                                if ( $blockINI->hasVariable( $block->attribute('type'), 'ManualAddingOfItems' )
                                        && $blockINI->variable( $block->attribute('type'), 'ManualAddingOfItems' ) === 'enabled' )
                                {
                                    $blockName = ( $block->attribute( 'name' ) == '' ) ? $blockINI->variable( $block->attribute('type'), 'Name' ) : $block->attribute( 'name' );
                                    if ( $blockINI->hasVariable( $block->attribute('type'), 'AllowedClasses' ) )
                                    {
                                        $allowedClasses = $blockINI->variable( $block->attribute('type'), 'AllowedClasses' );
                                        if ( in_array( $classIdentifier, $allowedClasses ) )
                                        {
                                            $res[] = array( 'index' => $index,
                                                            'id' => $block->attribute( 'id' ),
                                                            'name' => $blockName );
                                        }
                                    }
                                    else
                                    {
                                        $res[] = array( 'index' => $index,
                                                        'id' => $block->attribute( 'id' ),
                                                        'name' => $blockName );
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
            }
            
            break;
    }
    
    echo eZFlowAjaxContent::jsonEncode( $res );
}

eZExecution::cleanExit();

?>
