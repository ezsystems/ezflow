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

class eZPageZone
{
    private $attributes = array();

    function __construct( $name = null )
    {
        if ( isset( $name ) )
            $this->attributes['name'] = $name;
    }

    public function toXML( $dom )
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

    public static function createFromXML( $node )
    {
        $newObj = new eZPageZone();

        if ( $node->hasAttributes() )
        {
            foreach ( $node->attributes as $attr )
            {
                if ( $attr->name == 'id' )
                {
                    $value = explode( '_', $attr->value );
                    $newObj->setAttribute( $attr->name, $value[1] );
                }
                else
                {
                    $newObj->setAttribute( $attr->name, $attr->value );
                }
            }
        }

        foreach ( $node->childNodes as $node )
        {
            if ( $node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'block' )
            {
                $blockNode = eZPageBlock::createFromXML( $node );
                $newObj->addBlock( $blockNode );
            }
            elseif ( $node->nodeType == XML_ELEMENT_NODE )
            {
                $newObj->setAttribute( $node->nodeName, $node->nodeValue );
            }
        }

        return $newObj;
    }

    public function addBlock( eZPageBlock $block )
    {
        $this->attributes['blocks'][] = $block;
        return $block;
    }

    public function moveBlockUp( $currentIndex )
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

    public function moveBlockDown( $currentIndex )
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

    public function removeBlock( $index )
    {
        $blocks =& $this->attributes['blocks'];
        $blocks = array_splice( $blocks, $index, 1 );
    }

    public function getName()
    {
        return isset( $this->attributes['name'] ) ? $this->attributes['name'] : null;
    }

    public function getBlockCount()
    {
        return isset( $this->attributes['blocks'] ) ? count( $this->attributes['blocks'] ) : 0;
    }

    public function getBlock( $index )
    {
        $block = null;

        if ( isset( $this->attributes['blocks'][$index] ) )
            $block = $this->attributes['blocks'][$index];

        return $block;
    }

    public function attributes()
    {
        return array_keys( $this->attributes );
    }

    public function hasAttribute( $name )
    {
        return in_array( $name, array_keys( $this->attributes ) );
    }

    public function setAttribute( $name, $value )
    {
        $this->attributes[$name] = $value;
    }

    public function attribute( $name )
    {
        if ( $this->hasAttribute( $name ) )
        {
            return $this->attributes[$name];
        }
        else
        {
            $value = null;
            return $value;
        }
    }

    public function removeProcessed()
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
                    $block->removeProcessed();
                }
            }
        }

        return $this;
    }

    public function toBeRemoved()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'remove';
    }

    public function toBeModified()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'modify';
    }

    public function toBeAdded()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'add';
    }
}

?>