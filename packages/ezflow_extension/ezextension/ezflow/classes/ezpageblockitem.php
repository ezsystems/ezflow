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

class eZPageBlockItem
{
    private $attributes = array();
    private $XMLStorable;

    /**
     * Constructor
     *
     * @param array $row
     * @param bool $xmlStorable
     */
    function __construct( $row = false, $xmlStorable = true )
    {
        $this->XMLStorable = $xmlStorable;
        if ( $row && is_array( $row ) )
        {
            $this->attributes = $row;
        }
    }

    /**
     * Creates DOMElement with item data
     *
     * @param DOMDocument $dom
     * @return DOMElement
     */
    public function toXML( DOMDocument $dom )
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

    /**
     * Creates and return eZPageBlockItem object from given XML
     *
     * @static
     * @param DOMElement $node
     * @return eZPageBlockItem
     */
    public static function createFromXML( DOMElement $node )
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

    /**
     * Return attributes names
     * 
     * @return array(string)
     */
    public function attributes()
    {
        return array_keys( $this->attributes );
    }

    /**
     * Checks if attribute with given $name exists
     *  
     * @param string $name
     * @return bool
     */
    public function hasAttribute( $name )
    {
        return in_array( $name, array_keys( $this->attributes ) );
    }

    /**
     * Set attribute with given $name to $value
     * 
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute( $name, $value )
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Return value of attribute with given $name
     * 
     * @return mixed
     * @param string $name
     */
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

    /**
     * Checks if current item is to be removed
     * 
     * @return bool
     */
    public function toBeRemoved()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'remove';
    }

    /**
     * Checks if current item is to be modified
     * 
     * @return bool
     */
    public function toBeModified()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'modify';
    }

    /**
     * Checks if current item is to be added
     * 
     * @return bool
     */
    public function toBeAdded()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'add';
    }
    
    /**
     * set the block item to be storable in the content object
     * @param bool $xmlStorable
     * @return void
     */
    public function setXMLStorable( $xmlStorable )
    {
        $this->XMLStorable = $xmlStorable;
    }
}
?>