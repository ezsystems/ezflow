<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.1-0
// COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
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

$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'ContentObjectAttributeID' ) )
    $contentObjectAttributeID = $http->postVariable( 'ContentObjectAttributeID' );

if ( $http->hasPostVariable( 'Version' ) )
    $version = $http->postVariable( 'Version' );

if ( $http->hasPostVariable( 'Items' ) )
    $items = $http->postVariable( 'Items' );

if ( $http->hasPostVariable( 'Block' ) )
    $blockParams = createParamsFromStr( $http->postVariable( 'Block' ) );

if ( isset( $blockParams['z'] ) )
    $zoneID = $blockParams['z'];
if ( isset( $blockParams['b'] ) )
    $blockID = $blockParams['b'];

$contentObjectAttribute = eZContentObjectAttribute::fetch( $contentObjectAttributeID, $version );

if ( $contentObjectAttribute )
    $page = $contentObjectAttribute->content();
if ( $page )
    $zone = $page->getZone( $zoneID );
if ( $zone )
    $block = $zone->getBlock( $blockID );

$validCount = count( $block->attribute('valid') );
$waitingCount = count( $block->attribute('waiting') );

foreach( $items as $key => $item )
{
    $itemParams = createParamsFromStr( $item );

    if( array_key_exists( 'o', $blockParams ) )
    {
        foreach( $block->attribute('valid') as $blockItem )
        {
            if( $blockItem->attribute( 'object_id' ) == $itemParams['i'] )
            {
                $validCount -= 1;
                $create = true;
                
                if( $block->getItemCount() > 0 )
                {
                    foreach( $block->attribute( 'items' ) as $modItem )
                    {
                        if( $modItem->toBeModified() && 
                            $blockItem->attribute( 'object_id' ) == $modItem->attribute( 'object_id' ) )
                        {
                            $modItem->setAttribute( 'priority', $validCount );
                            $create = false;
                        }
                    }
                }
                
                if( $create )
                {
                    $tmpItem = $block->addItem( new eZPageBlockItem() );
                    $tmpItem->setAttribute( 'priority', $validCount );
                    $tmpItem->setAttribute( 'ts_visible', $blockItem->attribute( 'ts_visible' ) );
                    $tmpItem->setAttribute( 'ts_hidden', $blockItem->attribute( 'ts_hidden' ) );
                    $tmpItem->setAttribute( 'object_id', $blockItem->attribute( 'object_id' ) );
                    $tmpItem->setAttribute( 'action', 'modify' );
                }
            }
        }
    }
    else
    {
       foreach( $block->attribute('waiting') as $blockItem )
        {
            if( $blockItem->attribute( 'object_id' ) == $itemParams['i'] )
            {
                $waitingCount -= 1;

                if( $blockItem->toBeAdded() )
                {
                    $blockItem->setAttribute( 'priority', $waitingCount );
                }
                else
                {
                    $tmpItem = $block->addItem( new eZPageBlockItem() );
                    $tmpItem->setAttribute( 'priority', $waitingCount );
                    $tmpItem->setAttribute( 'object_id', $blockItem->attribute( 'object_id' ) );
                    $tmpItem->setAttribute( 'ts_visible', $blockItem->attribute( 'ts_visible' ) );
                    $tmpItem->setAttribute( 'ts_hidden', $blockItem->attribute( 'ts_hidden' ) );
                    $tmpItem->setAttribute( 'ts_publication', $blockItem->attribute( 'ts_publication' ) );
                    $tmpItem->setAttribute( 'action', 'modify' );
                }
            }
        }
    }
}

$contentObjectAttribute->setContent( $page );
$contentObjectAttribute->store();

eZExecution::cleanExit();

function createParamsFromStr( $str )
{
    $params = array();

    foreach( explode( '_', $str ) as $id )
    {
        $elem = explode( ':', $id );

        $key = isset( $elem[0] ) ? $elem[0] : null;
        $value = isset( $elem[1] ) ? $elem[1] : null;

        $params[$key] = $value;
    }

    return $params;
}

?>