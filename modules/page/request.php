<?php

/*
 * TODO: Drag and drop itmes from AJAX search results.
 * TODO: Re-arrange items in online section.
 * TODO: Move items from archived to queue.
 */
include_once( 'extension/ezflow/classes/ezmPool.php' );
include_once( 'extension/ezflow/classes/ezpageblockitem.php' );

$contentObjectAttributeID = $_POST['ContentObjectAttributeID'];
$version = $_POST['Version'];

$blockParams = createParamsFromStr( $_POST['Block'] );

$zoneID = $blockParams['z'];
$blockID = $blockParams['b'];

$contentObjectAttribute = eZContentObjectAttribute::fetch( $contentObjectAttributeID, $version );
$page =& $contentObjectAttribute->content();
$zone =& $page->getZone( $zoneID );
$block =& $zone->getBlock( $blockID );

$items = array();

foreach( $_POST['Items'] as $key => $item )
{
    $itemParams = createParamsFromStr( $item );

    foreach( $block->getWaitingItems() as $blockItem )
    {
        if( $blockItem->attribute( 'object_id' ) == $itemParams['i'] )
        {
            if( $blockItem->toBeAdded() )
            {
                $blockItem->setAttribute( 'priority', $key + 1 );
                $items[] = $blockItem;
            }
            else
            {
                $tmpItem =& $block->addItem( new eZPageBlockItem() );
                $tmpItem->setAttribute( 'priority', $key + 1 );
                $tmpItem->setAttribute( 'object_id', $blockItem->attribute( 'object_id' ) );
                $tmpItem->setAttribute( 'ts_publication', $blockItem->attribute( 'ts_publication' ) );
                $tmpItem->setAttribute( 'action', 'modify' );
                $items[] = $tmpItem;
            }
        }
    }
}

$block->setAttribute( 'items', $items );

$contentObjectAttribute->setContent( $page );
$contentObjectAttribute->store();

eZExecution::cleanExit();

function createParamsFromStr( $str )
{
    $params = array();

    foreach( explode( '_', $str ) as $id )
    {
        $elem = split( ':', $id );

        $key = isset( $elem[0] ) ? $elem[0] : null;
        $value = isset( $elem[1] ) ? $elem[1] : null;

        $params[$key] = $value;
    }

    return $params;
}

?>