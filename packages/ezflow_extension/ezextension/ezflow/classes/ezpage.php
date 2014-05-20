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

class eZPage
{
    private $attributes = array();

    /**
     * Constructor
     *
     * @param string $name
     */
    function __construct( $name = null )
    {
        if ( isset( $name ) )
            $this->attributes['name'] = $name;
    }

    /**
     * Dumps object structure into XML type string
     *
     * @return string
     */
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

    /**
     * Creates object structure from given XML type string
     *
     * @static
     * @param string $source
     * @return eZPage
     */
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
                    $newObj->addZone( $zoneNode );
                }
                elseif ( $node->nodeType == XML_ELEMENT_NODE )
                {
                    $newObj->setAttribute( $node->nodeName, $node->nodeValue );
                }
            }

            if ( $root->hasAttributes() )
            {
                foreach ( $root->attributes as $attr )
                {
                    $newObj->setAttribute( $attr->name, $attr->value );
                }
            }
        }

        $zoneINI = eZINI::instance( 'zone.ini' );
        $layoutName = $newObj->attribute( 'zone_layout' );
        if ( $zoneINI->hasVariable( $layoutName, 'Zones' ) )
        {
            foreach ( $zoneINI->variable( $layoutName, 'Zones' ) as $zoneIdentifier )
            {
                foreach ( $newObj->attribute( 'zones' ) as $inObjectZone )
                {
                    if ( $inObjectZone->attribute( 'zone_identifier' ) === $zoneIdentifier )
                        continue 2;
                }

                $newZone = $newObj->addZone( new eZPageZone() );
                $newZone->setAttribute( 'id', md5( mt_rand() . microtime() . $newObj->getZoneCount() ) );
                $newZone->setAttribute( 'zone_identifier', $zoneIdentifier );
                $newZone->setAttribute( 'action', 'add' );
            }
        }

        return $newObj;
    }

    /**
     * Adds new $zone to the eZPage object
     *
     * @param eZPageZone $zone
     * @return eZPageZone
     */
    public function addZone( eZPageZone $zone )
    {
        $this->attributes['zones'][] = $zone;
        return $zone;
    }

    /**
     * Return zone object by given index
     *
     * @param integer $index
     * @return eZPageZone
     */
    public function getZone( $index )
    {
        $zone = null;

        if( isset( $this->attributes['zones'][$index] ) )
            $zone = $this->attributes['zones'][$index];

        return $zone;
    }

    /**
     * Return eZPage name attribute
     *
     * @return string
     */
    public function getName()
    {
        return isset( $this->attributes['name'] ) ? $this->attributes['name'] : null;
    }

    /**
     * Remove zone from eZPage object by given index
     *
     * @param integer $index
     */
    public function removeZone( $index )
    {
        unset( $this->attributes['zones'][$index] );
    }

    /**
     * Remove zones from eZPage object with action "add" or set action to
     * "remove" for zones which already presists
     *
     */
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

    /**
     * Return total zone count
     *
     * @return integer
     */
    public function getZoneCount()
    {
        return isset( $this->attributes['zones'] ) ? count( $this->attributes['zones'] ) : 0;
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
     * Cleanup processed objects, removes action attribute
     * removes all zones marked with "remove" action
     *
     */
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

    /**
     * Sorting zones array according to the INI file configuration.
     *  
     */
    public function sortZones()
    {
        $ini = eZINI::instance('zone.ini');
        $zones = $ini->variable( $this->attribute('zone_layout'), 'Zones' );

        $sortedZones = array();
        foreach( $zones as $zone )
        {
            foreach( $this->attribute('zones') as $zoneObj )
            {
                if( !( $zoneObj instanceof eZPageZone ) )
                    continue;

                if ( $zone == $zoneObj->attribute('zone_identifier') )
                    $sortedZones[] = $zoneObj;
            }
        }

        $this->setAttribute( 'zones', $sortedZones );
    }

    /**
     * Method executed when an object copy is created 
     * by using the clone keyword
     *
     */
    public function __clone()
    {
        if ( isset( $this->attributes['zones'] ) )
        {
            foreach ( $this->attributes['zones'] as $i => $zone )
            {
                $this->attributes['zones'][$i] = clone $zone;
            }
        }
    }
}

?>
