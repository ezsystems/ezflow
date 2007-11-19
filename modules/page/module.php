<?php

$Module = array( 'name' => 'eZ Page',
                 'variable_params' => true );

$ViewList = array();
$ViewList['zone'] = array(
                            'script' => 'zone.php',
                            'params' => array( 'ContentObjectAttributeID', 'Version', 'ZoneID' )
                         );

$ViewList['request'] = array(
                            'script' => 'request.php',
                            'unordered_params' => array( 'items' => 'Items',
                                                         'block' => 'Block' )
                         );

$ViewList['block'] = array(
                            'script' => 'block.php',
                            'params' => array( 'BlockID', 'Output' )
                         );
?>