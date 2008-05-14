<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.1.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2008 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

include_once( 'extension/ezflow/classes/ezflowpool.php' );
include_once( 'extension/ezflow/classes/ezpageblockitem.php' );

$contentObjectAttributeID = $_POST['ContentObjectAttributeID'];
$version = $_POST['Version'];

$blockParams = createParamsFromStr( $_POST['Block'] );

$zoneID = $blockParams['z'];
$blockID = $blockParams['b'];

$contentObjectAttribute = eZContentObjectAttribute::fetch( $contentObjectAttributeID, $version );
$page = $contentObjectAttribute->content();
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