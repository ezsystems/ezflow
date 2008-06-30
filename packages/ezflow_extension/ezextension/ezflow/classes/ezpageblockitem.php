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

class eZPageBlockItem
{
    private $attributes = array();
    private $XMLStorable;

    function __construct( $row = false, $xmlStorable = true )
    {
        $this->XMLStorable = $xmlStorable;
        if ( $row && is_array( $row ) )
        {
            $this->attributes = $row;
        }
    }

    public function toXML( $dom )
    {
        if ( !$this->XMLStorable )
        {
            return false;
        }

        $itemNode = $dom->createElement( 'item' );

        foreach ( $this->attributes as $attrName => $attrValue )
        {
            switch ( $attrName )
            {
                case 'id':
                    $itemNode->setAttribute( 'id', $attrValue );
                    break;

                case 'action':
                    $itemNode->setAttribute( 'action', $attrValue );
                    break;

                default:
                    $node = $dom->createElement( $attrName );
                    $nodeValue = $dom->createTextNode( $attrValue );
                    $node->appendChild( $nodeValue );
                    $itemNode->appendChild( $node );
                    break;
            }
        }

        return $itemNode;
    }

    public static function createFromXML( $node )
    {
        $newObj = new eZPageBlockItem();

        if ( $node->hasAttributes() )
        {
            foreach ( $node->attributes as $attr )
            {
                $newObj->setAttribute( $attr->name, $attr->value );
            }
        }

        foreach ( $node->childNodes as $node )
        {
            if ( $node->nodeType == XML_ELEMENT_NODE )
                $newObj->setAttribute( $node->nodeName, $node->nodeValue );
        }

        return $newObj;
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

    public public function attribute( $name )
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