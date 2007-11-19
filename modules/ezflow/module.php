<?php

$Module = array( 'name' => 'eZ Flow' );

$ViewList['timeline'] = array( 'script' => 'timeline.php',
                               'params' => array( 'NodeID', 'LanguageCode' ) );

$ViewList['preview'] = array( 'script' => 'preview.php',
                              'params' => array( 'Time', 'NodeID' ) );
?>
