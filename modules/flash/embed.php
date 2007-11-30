<?php
//
// Created by: oh
//
// Copyright (C) 1999-2007 eZ Systems as. All rights reserved.

include_once( 'lib/ezutils/classes/ezhttptool.php' );
include_once( 'kernel/classes/ezcontentobject.php' );
include_once( 'kernel/common/template.php' );

$http =& eZHTTPTool::instance();

if ( $Params['ObjectID'] )
    $objectID = $Params['ObjectID'];
    
if ( $http->hasPostVariable( "ObjectID" ) )
    $objectID = $http->postVariable( "ObjectID" );

if ( is_numeric( $objectID ) )
{
    $object = eZContentObject::fetch( $objectID );
    if ( $object )
    {
        $tpl =& templateInit();
        $template = "design:parts/flash_player_embed_code.tpl";
        $tpl->setVariable( 'object', $object );
        $body = $tpl->fetch( $template );
        print( "&result=" . urlencode( trim( $body ) ) );
    }
}

eZExecution::cleanup();
eZExecution::setCleanExit();
exit;


?>
