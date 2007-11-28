<?php

include_once( 'extension/ezflow/classes/ezmPool.php' );

class ezmMediaFunctionCollection
{
    function fetchWaiting( $blockID )
    {
        $result = array( 'result' => ezmPool::waitingItems( $blockID ) );
        return $result;
    }

    function fetchValid( $blockID )
    {
        $result = array( 'result' => ezmPool::validItems( $blockID ) );
        return $result;
    }

    function fetchArchived( $blockID )
    {
        $result = array( 'result' => ezmPool::archivedItems( $blockID ) );
        return $result;
    }

    function fetchValidNodes( $blockID )
    {
        $result = array( 'result' => ezmPool::validNodes( $blockID ) );
        return $result;
    }
    
    function fetchBlock( $blockID )
    {
        $result = array( 'result' => eZPageBlock::fetch( $blockID ) );
        return $result;
    }
}

?>