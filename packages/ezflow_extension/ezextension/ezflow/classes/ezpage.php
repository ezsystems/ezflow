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

class eZPage
{
    private $attributes = array();

    function __construct( $name = null )
    {
        if ( isset( $name ) )
            $this->attributes['name'] = $name;
    }

    public function toXML()
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->formatOutput = true;
        $success = $dom->loadXML('<page />');

        $pageNode = $dom->documentElement;

        foreach ( $this->attributes as $attrName => $attrValue )
        {
            switch ( $attrName )
            {
                case 'id':
                    $pageNode->setAttribute( 'id', $attrValue );
                    break;

                case 'zones':
                    foreach ( $this->attributes['zones'] as $zone )
                    {
                        $zoneNode = $zone->toXML( $dom );
                        $pageNode->appendChild( $zoneNode );
                    }
                    break;

                default:
                    $node = $dom->createElement( $attrName );
                    $nodeValue = $dom->createTextNode( $attrValue );
                    $node->appendChild( $nodeValue );
                    $pageNode->appendChild( $node );
                    break;
            }
        }

        return $dom->saveXML();
    }

    public static function createFromXML( $source )
    {
        $newObj = new eZPage();

        if ( $source )
        {
            $dom = new DOMDocument( '1.0', 'utf-8' );
            $success = $dom->loadXML( $source );
            $root = $dom->documentElement;

            foreach ( $root->childNodes as $node )
            {
                if ( $node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'zone' )
                {
                    $zoneNode = eZPageZone::createFromXML( $node );
                    $newObj->attributes['zones'][] = $zoneNode;
                }
                elseif ( $node->nodeType == XML_ELEMENT_NODE )
                {               
                    $newObj->attributes[$node->nodeName] = $node->nodeValue;
                }
            }

            if ( $root->hasAttributes() )
            {
                foreach ( $root->attributes as $attr )
                {
                    $newObj->attributes[$attr->name] = $attr->value;
                }
            }
        }

        return $newObj;
    }

    public function addZone( $zone )
    {
        $this->attributes['zones'][] = $zone;
        return $zone;
    }

    public function getZone( $id )
    {
        $zone = null;
        
        if( isset( $this->attributes['zones'][$id] ) )
            $zone = $this->attributes['zones'][$id];
        
        return $zone;
    }

    public function getName()
    {
        return isset( $this->attributes['name'] ) ? $this->attributes['name'] : null;
    }

    public function removeZone( $id )
    {
       unset( $this->attributes['zones'][$id] );
    }

    public function removeZones()
    {
        foreach( $this->attributes['zones'] as $index => $zone )
        {
            if ( $zone->toBeAdded() )
                $this->removeZone( $index );
            else
                $zone->setAttribute( 'action', 'remove' );
        }
    }

    public function getZoneCount()
    {
        return isset( $this->attributes['zones'] ) ? count( $this->attributes['zones'] ) : 0;
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
        
        if ( $this->getZoneCount() > 0 )
        {
            foreach ( $this->attributes['zones'] as $index => $zone )
            {
                if ( $zone->toBeRemoved() )
                    $this->removeZone($index);
                else
                    $zone->removeProcessed();
            }
        }
    }
}

?>