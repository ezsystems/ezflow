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

class eZPageBlockItem
{
    var $attributes = array();
    var $XMLStorable;

    function eZPageBlockItem( $row = false, $xmlStorable = true )
    {
        $this->XMLStorable = $xmlStorable;
        if ( $row && is_array( $row ) )
        {
            $this->attributes = $row;
        }
    }

    function toXML( $dom )
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

    static function &createFromXML( $node )
    {
        $newObj = new eZPageBlockItem();

        if ( $node->hasAttributes() )
        {
            foreach ( $node->attributes as $attr )
            {
                $newObj->attributes[$attr->name] = $attr->value;
            }
        }

        foreach ( $node->childNodes as $node )
        {
            if ( $node->nodeType == XML_ELEMENT_NODE )
            $newObj->attributes[$node->nodeName] = $node->nodeValue;
        }

        return $newObj;
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