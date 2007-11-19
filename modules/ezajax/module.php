<?php

$Module = array( "name" => "eZajax Module and Views" );

$ViewList = array();

$ViewList["search"] = array(
    "script" => "search.php",
    'params' => array( 'SearchStr', 'SearchOffset', 'SearchLimit', 'VarName')
    );

?>