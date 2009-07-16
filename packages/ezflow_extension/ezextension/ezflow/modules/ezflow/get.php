<?php

$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'content' ) )
{
    $res = array();
    $page = false;
    $zoneName = array();
    $classIdentifier = false;
    
    if ( $http->hasPostVariable( 'node_id' ) )
    {
        $node = eZContentObjectTreeNode::fetch( $http->postVariable( 'node_id' ) );
        
        if ( $node instanceof eZContentObjectTreeNode )
            $object = $node->object();
        else
            $object = false;
        
        if ( $object instanceof eZContentObject )
        {
            $classID = $object->attribute( 'contentclass_id' );
            $classIdentifier = eZContentClass::classIdentifierByID( $classID );
        }

        $dataMap = $object->dataMap();

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
                                    if ( $blockINI->hasVariable( $block->attribute('type'), 'AllowedClasses' ) )
                                    {
                                        $allowedClasses = $blockINI->variable( $block->attribute('type'), 'AllowedClasses' );
                                        
                                        if ( in_array( $classIdentifier, $allowedClasses ) )
                                        {
                                            $res[] = array( 'index' => $index,
                                                            'id' => $block->attribute( 'id' ),
                                                            'name' => $block->attribute( 'name' ) );
                                        }
                                    }
                                    else
                                    {
                                        $res[] = array( 'index' => $index,
                                                        'id' => $block->attribute( 'id' ),
                                                        'name' => $block->attribute( 'name' ) );
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