<?php
//
// Created by: oh
//
// Copyright (C) 1999-2007 eZ Systems as. All rights reserved.

include_once( 'lib/ezutils/classes/ezhttptool.php' );
include_once( 'kernel/classes/ezcontentobject.php' );
include_once( 'kernel/common/template.php' );

$http =& eZHTTPTool::instance();

$content = file_get_contents( "http://localhost:91/streams/streams/list2.php" );

$tpl =& templateInit();
$template = "design:flash_demo.tpl";
$tpl->setVariable( 'list', explode( "\n", $content ) );
$tpl->setVariable( 'view_parameters', $Params['UserParameters'] );
$body = $tpl->fetch( $template );

$Result = array();
$Result['content'] = $body;

?>
