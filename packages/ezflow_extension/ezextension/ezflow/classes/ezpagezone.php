<?php
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.0.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2007 eZ Systems AS
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

class eZPageZone
{
    var $attributes = array();

    function eZPageZone( $name = null )
    {
        if ( isset( $name ) )
            $this->attributes['name'] = $name;
    }

    function toXML( $dom )
    {
        $zoneNode = $dom->createElement( 'zone' );
        foreach ( $this->attributes as $attrName => $attrValue )
        {
            switch ( $attrName )
            {
                case 'id':
                    $zoneNode->setAttribute( 'id', 'id_' . $attrValue );
                    break;

                case 'action':
                    $zoneNode->setAttribute( 'action', $attrValue );
                    break;

                case 'blocks':
                    foreach ( $this->attributes['blocks'] as $block )
                    {
                        $blockNode = $block->toXML( $dom );
                        $zoneNode->appendChild( $blockNode );
                    }
                    break;

                default:
                    $node = $dom->createElement( $attrName );
                    $nodeValue = $dom->createTextNode( $attrValue );
                    $node->appendChild( $nodeValue );
                    $zoneNode->appendChild( $node );
                    break;
            }
        }

        return $zoneNode;
    }

    static function &createFromXML( $node )
    {
        $newObj = new eZPageZone();

        if ( $node->hasAttributes() )
        {
            foreach ( $node->attributes as $attr )
            {
                if ( $attr->name == 'id' )
                {
                    $value = explode( '_', $attr->value );
                    $newObj->attributes[$attr->name] = $value[1];
                }
                else
                {
                    $newObj->attributes[$attr->name] = $attr->value;
                }
            }
        }

        foreach ( $node->childNodes as $node )
        {
            if ( $node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'block' )
            {
                $blockNode =& eZPageBlock::createFromXML( $node );
                $newObj->attributes['blocks'][] =& $blockNode;
            }
            elseif ( $node->nodeType == XML_ELEMENT_NODE )
            {
                $newObj->attributes[$node->nodeName] = $node->nodeValue;
            }
        }

        return $newObj;
    }

    function &addBlock( $block )
    {
        $this->attributes['blocks'][] =& $block;
        return $block;
    }

    function moveBlockUp( $currentIndex )
    {
        $array =& $this->attributes['blocks'];

        $newIndex = $currentIndex - 1;

        if ( $newIndex < 0 || $newIndex >= count( $array ) )
            return false;

        $tmpItem = $array[$newIndex];

        $array[$newIndex] =& $array[$currentIndex];
        $array[$currentIndex] =& $tmpItem;

        if ( $tmpItem->toBeRemoved() )
            $this->moveBlockUp( $newIndex );

        return true;
    }

    function moveBlockDown( $currentIndex )
    {
        $array =& $this->attributes['blocks'];

        $newIndex = $currentIndex + 1;

        if ( $newIndex < 0 || $newIndex >= count( $array ) )
            return false;

        $tmpItem = $array[$newIndex];

        $array[$newIndex] =& $array[$currentIndex];
        $array[$currentIndex] =& $tmpItem;

        if ( $tmpItem->toBeRemoved() )
            $this->moveBlockDown( $newIndex );

        return true;
    }

    function removeBlock( $id )
    {
        unset( $this->attributes['blocks'][$id] );
    }

    function getName()
    {
        return isset( $this->attributes['name'] ) ? $this->attributes['name'] : null;
    }

    function getBlockCount()
    {
        return isset( $this->attributes['blocks'] ) ? count( $this->attributes['blocks'] ) : 0;
    }

    function &getBlock( $id )
    {
        $block =& $this->attributes['blocks'][$id];
        return $block;
    }

    function attributes()
    {

        return array_keys( $this->attributes );
    }

    function hasAttribute( $name )
    {
        return in_array( $name, array_keys( $this->attributes ) );
    }

    function setAttribute( $name, $value )
    {
        $this->attributes[$name] = $value;
    }

    function &attribute( $name )
    {
        return $this->attributes[$name];
    }

    function removeProcessed()
    {
        if ( $this->hasAttribute( 'action' ) )
        {
            unset( $this->attributes['action'] );
        }

        if ( $this->getBlockCount() > 0 )
        {
            foreach ( $this->attributes['blocks'] as $index => $block )
            {
                if ( $block->toBeRemoved() )
                {
                    $this->removeBlock( $index );
                }
                else
                {
                    $this->attributes['blocks'][$index] = $block->removeProcessed();
                }
            }
        }

        return $this;
    }

    function toBeRemoved()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'remove';
    }

    function toBeModified()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'modify';
    }

    function toBeAdded()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'add';
    }
}

?>