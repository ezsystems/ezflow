<?php

$Module = array( 'name' => 'eZ Flow' );

$ViewList = array();
$ViewList['timeline'] = array( 'script' => 'timeline.php',
                               'params' => array( 'NodeID', 'LanguageCode' ) );

$ViewList['preview'] = array( 'script' => 'preview.php',
                              'params' => array( 'Time', 'NodeID' ) );

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
