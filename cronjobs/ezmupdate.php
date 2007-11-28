<?php

include_once( 'extension/ezflow/classes/ezflowoperations.php' );

if ( !$isQuiet )
{
    $cli->output( "Updating ezm_pool" );
}

eZFlowOperations::update();

?>