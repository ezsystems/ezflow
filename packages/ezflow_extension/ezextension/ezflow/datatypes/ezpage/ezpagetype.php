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

class eZPageType extends eZDataType
{
    const DATA_TYPE_STRING = 'ezpage';

    const DEFAULT_ZONE_LAYOUT_FIELD = 'data_text1';

    /**
     * Constructor
     *
     */
    function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, "Layout" );
    }

    /**
     * Sets the default values in class attribute
     *
     * @param eZContentClassAttribute $classAttribute
     */
    function initializeClassAttribute( $classAttribute )
    {
        if ( $classAttribute->attribute( self::DEFAULT_ZONE_LAYOUT_FIELD ) === null )
            $classAttribute->setAttribute( self::DEFAULT_ZONE_LAYOUT_FIELD, '' );

        $classAttribute->store();
    }

    /**
     * Serialize contentclass attribute
     *
     * @param eZContentClassAttribute $classAttribute
     * @param DOMNode $attributeNode
     * @param DOMNode $attributeParametersNode
     */
    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $defaultZoneLayout = $classAttribute->attribute( self::DEFAULT_ZONE_LAYOUT_FIELD );
        $dom = $attributeParametersNode->ownerDocument;

        $defaultLayoutNode = $dom->createElement( 'default-layout' );
        $defaultLayoutNode->appendChild( $dom->createTextNode( $defaultZoneLayout ) );
        $attributeParametersNode->appendChild( $defaultLayoutNode );
    }

    /**
     * Unserialize contentclass attribute
     *
     * @param eZContentClassAttribute $classAttribute
     * @param DOMElement $attributeNode
     * @param DOMElement $attributeParametersNode
     */
    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $defaultZoneLayoutItem = $attributeParametersNode->getElementsByTagName( 'default-layout' )->item( 0 );
        if ( $defaultZoneLayoutItem !== null && $defaultZoneLayoutItem->textContent !== false )
            $classAttribute->setAttribute( self::DEFAULT_ZONE_LAYOUT_FIELD, $defaultZoneLayoutItem->textContent );
    }

    /**
     * Initialize contentobject attribute content
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param integer $currentVersion
     * @param eZContentObjectAttribute $originalContentObjectAttribute
     */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $contentObjectID = $contentObjectAttribute->attribute( 'contentobject_id' );
            $originalContentObjectID = $originalContentObjectAttribute->attribute( 'contentobject_id' );
            $languageMask = $contentObjectAttribute->attribute( 'language_id' ) & ~1;
            $originalLanguageMask = $originalContentObjectAttribute->attribute( 'language_id' ) & ~1;

            // Case when content object was copied or when new translation has been added to the existing one
            if ( ( $contentObjectID != $originalContentObjectID )
                    || ( ( $contentObjectID == $originalContentObjectID )
                       && ( $languageMask != $originalLanguageMask )
                            && ( $contentObjectAttribute->attribute( 'can_translate' ) ) ) )
            {
                $page = $originalContentObjectAttribute->content();
                $clonedPage = clone $page;
                $contentObjectAttribute->setContent( $clonedPage );
                $contentObjectAttribute->store();
            }
            else
            {
                $dataText = $originalContentObjectAttribute->attribute( 'data_text' );
                $contentObjectAttribute->setAttribute( 'data_text', $dataText );
            }
        }
        else
        {
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $defaultLayout = $contentClassAttribute->attribute( self::DEFAULT_ZONE_LAYOUT_FIELD );
            $zoneINI = eZINI::instance( 'zone.ini' );
            $page = new eZPage();
            $zones = array();
            if ( $defaultLayout !== '' )
            {
                if ( $zoneINI->hasVariable( $defaultLayout, 'Zones' ) )
                    $zones = $zoneINI->variable( $defaultLayout, 'Zones' );

                $page->setAttribute( 'zone_layout', $defaultLayout );
                foreach ( $zones as $zoneIdentifier )
                {
                    $newZone = $page->addZone( new eZPageZone() );
                    $newZone->setAttribute( 'id', md5( mt_rand() . microtime() . $page->getZoneCount() ) );
                    $newZone->setAttribute( 'zone_identifier', $zoneIdentifier );
                    $newZone->setAttribute( 'action', 'add' );
                }
            }
            else
            {
                $allowedZones = array();
                if ( $zoneINI->hasGroup( 'General' ) && $zoneINI->hasVariable( 'General', 'AllowedTypes' ) )
                    $allowedZones = $zoneINI->variable( 'General', 'AllowedTypes' );

                $class = eZContentClass::fetch( $contentClassAttribute->attribute( 'contentclass_id' ) );

                foreach ( $allowedZones as $allowedZone )
                {
                    $availableForClasses = array();
                    if ( $zoneINI->hasVariable( $allowedZone, 'AvailableForClasses' ) )
                        $availableForClasses = $zoneINI->variable( $allowedZone, 'AvailableForClasses' );

                    if ( in_array( $class->attribute( 'identifier' ), $availableForClasses ) )
                    {
                        if ( $zoneINI->hasVariable( $allowedZone, 'Zones' ) )
                            $zones = $zoneINI->variable( $allowedZone, 'Zones' );

                        $page->setAttribute( 'zone_layout', $allowedZone );
                        foreach ( $zones as $zoneIdentifier )
                        {
                            $newZone = $page->addZone( new eZPageZone() );
                            $newZone->setAttribute( 'id', md5( mt_rand() . microtime() . $page->getZoneCount() ) );
                            $newZone->setAttribute( 'zone_identifier', $zoneIdentifier );
                            $newZone->setAttribute( 'action', 'add' );
                        }

                        break;
                    }
                    else
                        continue;
                }
            }
            $contentObjectAttribute->setContent( $page );
        }
    }

    /**
     * Checks if contentobject attribute has content
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return bool
     */
    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $page = $contentObjectAttribute->content();
        $zones = $page->attribute( 'zones' );
        if ( !is_array( $zones ) )
            return false;

        foreach ( $zones as $zone )
        {
            if ( $zone->getBlockCount() > 0 )
                return true;
        }

        return false;
    }

    /**
     * Validates all variables given on content class level
     * return eZInputValidator::STATE_ACCEPTED or eZInputValidator::STATE_INVALID if
     * the values are accepted or not
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentClassAttribute $classAttribute
     * @return int
     */
    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Fetches all variables inputed on content class level
     * return true if fetching of class attributes are successfull, false if not
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentClassAttribute $classAttribute
     * @return bool
     */
    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezpage_default_layout_' . $classAttribute->attribute( 'id' ) ) )
        {
            $defaultLayout = $http->postVariable( $base . '_ezpage_default_layout_' . $classAttribute->attribute( 'id' ) );
            $classAttribute->setAttribute( self::DEFAULT_ZONE_LAYOUT_FIELD, $defaultLayout );
        }
        return true;
    }

    /**
     * Validates input on content object level
     * return eZInputValidator::STATE_ACCEPTED or eZInputValidator::STATE_INVALID if
     * the values are accepted or not
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return int
     */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Fetches all variables from the object
     * return true if fetching of object attributes are successfull, false if not
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return bool
     */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $page = $contentObjectAttribute->content();
        $blockINI = eZINI::instance( 'block.ini' );

        if ( $http->hasPostVariable( $base . '_ezpage_block_fetch_param_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $blockFetchParams = $http->postVariable( $base . '_ezpage_block_fetch_param_' . $contentObjectAttribute->attribute( 'id' ) );

            foreach ( $blockFetchParams as $zoneID => $blocks )
            {
                $zone = $page->getZone( $zoneID );

                foreach ( $blocks as $blockID => $params )
                {
                    $block = $zone->getBlock( $blockID );
                    $fetchParams = array();

                    $fetchParams = unserialize( $block->attribute( 'fetch_params' ) );
                    $tmpFetchParams = $fetchParams;

                    foreach ( $params as $param => $value )
                    {
                        $fetchParams[$param] = $value;
                    }

                    $block->setAttribute( 'fetch_params', serialize( $fetchParams ) );

                    if ( $fetchParams !== $tmpFetchParams )
                    {
                        $persBlockObject = eZFlowBlock::fetch( $block->attribute( 'id' ) );

                        if ( $persBlockObject instanceof eZFlowBlock )
                        {
                            $persBlockObject->setAttribute( 'last_update', 0 );
                            $persBlockObject->store();
                        }
                    }
                }
            }
        }

        if ( $http->hasPostVariable( $base . '_ezpage_block_custom_attribute_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $blockCustomAttributes = $http->postVariable( $base . '_ezpage_block_custom_attribute_' . $contentObjectAttribute->attribute( 'id' ) );

            foreach ( $blockCustomAttributes as $zoneID => $blocks )
            {
                $zone = $page->getZone( $zoneID );

                foreach ( $blocks as $blockID => $params )
                {
                    $block = $zone->getBlock( $blockID );

                    $customAttributes = $block->attribute( 'custom_attributes' );

                    foreach ( $params as $param => $value )
                    {
                        $customAttributes[$param] = $value;
                    }

                    $block->setAttribute( 'custom_attributes', $customAttributes );
                }
            }
        }

        if ( $http->hasPostVariable( $base . '_ezpage_block_view_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {

            $blockViewArray = $http->postVariable( $base . '_ezpage_block_view_' . $contentObjectAttribute->attribute( 'id' ) );

            foreach ( $blockViewArray as $zoneID => $blocks )
            {
                $zone = $page->getZone( $zoneID );

                foreach ( $blocks as $blockID => $view )
                {
                    $block = $zone->getBlock( $blockID );
                    $block->setAttribute( 'view', $view );

                }
            }
        }

        if ( $http->hasPostVariable( $base . '_ezpage_block_overflow_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $blockOverflowArray = $http->postVariable( $base . '_ezpage_block_overflow_' . $contentObjectAttribute->attribute( 'id' ) );

            foreach ( $blockOverflowArray as $zoneID => $blocks )
            {
                $zone = $page->getZone( $zoneID );

                foreach ( $blocks as $blockID => $overflowBlockID )
                {
                    $block = $zone->getBlock( $blockID );
                    $block->setAttribute( 'overflow_id', $overflowBlockID );
                }
            }

        }

        if ( $http->hasPostVariable( $base . '_ezpage_block_name_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $blockNameArray = $http->postVariable( $base . '_ezpage_block_name_array_' . $contentObjectAttribute->attribute( 'id' ) );

            foreach ( $blockNameArray as $zoneID => $blocks )
            {
                $zone = $page->getZone( $zoneID );

                foreach ( $blocks as $blockID => $blockName )
                {
                    $block = $zone->getBlock( $blockID );
                    $block->setAttribute( 'name', $blockName );
                }
            }

        }

        if ( $http->hasPostVariable( $base . '_ezpage_item_ts_published_value_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $itemTSPublishedValueArray = $http->postVariable( $base . '_ezpage_item_ts_published_value_' . $contentObjectAttribute->attribute( 'id' ) );

            foreach ( $itemTSPublishedValueArray as $zoneID => $blocks )
            {
                $zone = $page->getZone( $zoneID );

                foreach ( $blocks as $blockID => $itemTSPublishedValueIDs )
                {
                    $block = $zone->getBlock( $blockID );

                    if ( $block->getItemCount() > 0 )
                    {
                        $items = $block->attribute( 'items' );

                        foreach ( $items as $index => $item )
                        {
                            foreach ( $itemTSPublishedValueIDs as $objectID => $value )
                            {
                                if ( $value != '' )
                                {
                                    if ( $item->attribute( 'object_id' ) == $objectID )
                                    {
                                        $item->setAttribute( 'ts_publication', $value );
                                        unset( $itemTSPublishedValueIDs[$objectID] );
                                    }
                                }
                            }
                        }
                    }

                    foreach ( $itemTSPublishedValueIDs as $objectID => $value )
                    {
                        if ( $value != '' )
                        {
                            $item = $block->addItem( new eZPageBlockItem() );
                            $item->setAttribute( 'action', 'modify' );
                            $item->setAttribute( 'object_id', $objectID );
                            $item->setAttribute( 'ts_publication', $value );
                        }
                    }
                }

            }
        }

        $contentObjectAttribute->setContent( $page );

        return true;
    }

    /**
     * Stores the datatype data to the database which is related to the object attribute.
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return bool
     */
    function storeObjectAttribute( $contentObjectAttribute )
    {
        $page = $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( 'data_text', $page->toXML() );
        return true;
    }

    /**
     * Returns the content data for the given content object attribute.
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return eZPage
     */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $source = $contentObjectAttribute->attribute( 'data_text' );
        $page = eZPage::createFromXML( $source );

        return $page;
    }

    /**
     * Returns the value as it will be shown if this attribute is used in the object name pattern.
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param string $name
     * @return string
     */
    function title( $contentObjectAttribute, $name = null  )
    {
        return '';
    }

    /**
     * Executes a custom action for an object attribute which was defined on the web page.
     *
     * @param eZHTTPTool $http
     * @param string $action
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param array $parameters
     */
    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        $params = explode( '-', $action );

        switch ( $params[0] )
        {
            case 'new_zone_layout':
                if ( $http->hasPostVariable( 'ContentObjectAttribute_ezpage_zone_allowed_type_' . $contentObjectAttribute->attribute( 'id' ) ) )
                {
                    $zoneMap = array();
                    if ( $http->hasPostVariable( 'ContentObjectAttribute_ezpage_zone_map' ) )
                        $zoneMap = $http->postVariable( 'ContentObjectAttribute_ezpage_zone_map' );

                    $zoneINI = eZINI::instance( 'zone.ini' );
                    $page = $contentObjectAttribute->content();
                    $zoneAllowedType = $http->postVariable( 'ContentObjectAttribute_ezpage_zone_allowed_type_' . $contentObjectAttribute->attribute( 'id' ) );

                    if ( $zoneAllowedType == $page->attribute('zone_layout') )
                        return false;

                    $allowedZones = $zoneINI->variable( $zoneAllowedType, 'Zones' );
                    $allowedZonesCount = count( $allowedZones );

                    $page->setAttribute( 'zone_layout', $zoneAllowedType );
                    $existingZoneCount = $page->getZoneCount();

                    $zoneCountDiff = 0;
                    if ( $allowedZonesCount < $existingZoneCount )
                        $zoneCountDiff = $existingZoneCount - $allowedZonesCount;

                    if ( !empty( $zoneMap ) )
                    {
                        foreach( $page->attribute( 'zones' ) as $zoneIndex => $zone )
                        {
                            $zoneMapKey = array_search( $zone->attribute( 'zone_identifier' ), $zoneMap );

                            if ( $zoneMapKey )
                            {
                                $zone->setAttribute( 'action', 'modify' );
                                $zone->setAttribute( 'zone_identifier', $zoneMapKey );
                            }
                            else
                            {
                                if ( $zone->toBeAdded() )
                                    $page->removeZone( $zoneIndex );
                                else
                                    $zone->setAttribute( 'action', 'remove' );
                            }
                        }
                    }
                    else
                    {
                        foreach ( $allowedZones as $index => $zoneIdentifier )
                        {
                            $existingZone = $page->getZone($index);

                            if ( $existingZone instanceof eZPageZone )
                            {
                                $existingZone->setAttribute( 'action', 'modify' );
                                $existingZone->setAttribute( 'zone_identifier', $zoneIdentifier );
                            }
                            else
                            {
                                $newZone = $page->addZone( new eZPageZone() );
                                $newZone->setAttribute( 'id', md5( mt_rand() . microtime() . $page->getZoneCount() ) );
                                $newZone->setAttribute( 'zone_identifier', $zoneIdentifier );
                                $newZone->setAttribute( 'action', 'add' );
                            }
                        }

                        if ( $zoneCountDiff > 0 )
                        {
                            while ( $zoneCountDiff != 0 )
                            {
                                $existingZoneIndex = $existingZoneCount - $zoneCountDiff;
                                $existingZone = $page->getZone( $existingZoneIndex );

                                if ( $existingZone->toBeAdded() )
                                    $page->removeZone( $existingZoneIndex );
                                else
                                    $existingZone->setAttribute( 'action', 'remove' );

                                $zoneCountDiff -= 1;
                            }
                        }
                    }

                    $page->sortZones();
                }
                break;
            case 'set_rotation':
                $page = $contentObjectAttribute->content();
                $zone = $page->getZone( $params[1] );
                $block = $zone->getBlock( $params[2] );

                $rotationValue = $http->postVariable( 'RotationValue_' . $params[2] );
                $rotationUnit = $http->postVariable( 'RotationUnit_' . $params[2] );
                $rotationSuffle = $http->postVariable( 'RotationShuffle_' . $params[2] );

                if ( trim( $rotationValue ) == '' || $rotationValue == 0 )
                {
                    $block->setAttribute( 'rotation', array( 'interval' => 0,
                                                             'type' => 0,
                                                             'value' => '',
                                                             'unit' => '' ) );
                    $waitingItems = $block->attribute( 'waiting' );
                    foreach ( $waitingItems as $item )
                    {
                        $item->setAttribute( 'ts_publication', time() );
                        $item->setAttribute( 'ts_visible', time() );
                        $item->setAttribute( 'ts_hidden', '0' );
                        $item->setAttribute( 'action', 'add' );
                        $item->setXMLStorable( true );
                        $block->addItem( $item );
                    }
                }
                else
                {
                    switch ( $rotationUnit )
                    {
                        case '2':
                            $rotationInterval = $rotationValue * 60;
                            break;

                        case '3':
                            $rotationInterval = $rotationValue * 3600;
                            break;

                        case '4':
                            $rotationInterval = $rotationValue * 86400;

                        default:
                            break;
                    }

                    $rotationType = 1;

                    if ( $rotationSuffle )
                    $rotationType = 2;

                    $block->setAttribute( 'rotation', array( 'interval' => $rotationInterval,
                                                             'type' => $rotationType,
                                                             'value' => $rotationValue,
                                                             'unit' => $rotationUnit ) );
                }
                break;
            case 'remove_block':
                $page = $contentObjectAttribute->content();
                $zone = $page->getZone( $params[1] );
                $block = $zone->getBlock( $params[2] );

                if ( $block->toBeAdded() )
                {
                    $zone->removeBlock( $params[2] );
                }
                else
                {
                    $block->setAttribute( 'action', 'remove' );
                }
                break;
            case 'new_block':
                $page = $contentObjectAttribute->content();
                $zone = $page->getZone( $params[1] );

                $blockType = $http->hasPostVariable( 'ContentObjectAttribute_ezpage_block_type_' . $contentObjectAttribute->attribute( 'id' ) . '_' . $params[1] ) ? $http->postVariable( 'ContentObjectAttribute_ezpage_block_type_' . $contentObjectAttribute->attribute( 'id' ) . '_' . $params[1] ) : '';
                $blockName = $http->hasPostVariable( 'ContentObjectAttribute_ezpage_block_name_' . $contentObjectAttribute->attribute( 'id' ) . '_' . $params[1] ) ? $http->postVariable( 'ContentObjectAttribute_ezpage_block_name_' . $contentObjectAttribute->attribute( 'id' ) . '_' . $params[1] ) : '';

                $block = $zone->addBlock( new eZPageBlock( $blockName ) );
                $block->setAttribute( 'action', 'add' );
                $block->setAttribute( 'id', md5( mt_rand() . microtime() . $zone->getBlockCount() ) );
                $block->setAttribute( 'zone_id', $zone->attribute( 'id' ) );
                $block->setAttribute( 'type', $blockType );

                $blockState = 'id_' . $block->attribute( 'id' ) . '=1';
                setrawcookie( 'eZPageBlockState', isset( $_COOKIE['eZPageBlockState'] ) ? $_COOKIE['eZPageBlockState'] . '&' . $blockState : $blockState, time() + 3600, '/' );
                break;
            case 'move_block_up':
                $page = $contentObjectAttribute->content();
                $zone = $page->getZone( $params[1] );
                $zone->moveBlockUp( $params[2] );
                break;
            case 'move_block_down':
                $page = $contentObjectAttribute->content();
                $zone = $page->getZone( $params[1] );
                $zone->moveBlockDown( $params[2] );
                break;
            case 'new_item':
                if ( $http->hasPostVariable( 'SelectedNodeIDArray' ) )
                {
                    if ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
                    {
                        $selectedNodeIDArray = $http->postVariable( 'SelectedNodeIDArray' );

                        $page = $contentObjectAttribute->content();
                        $zone = null;
                        $block = null;

                        if( isset( $params[1] ) && ( $page instanceof eZPage ) )
                            $zone = $page->getZone( $params[1] );

                        if ( $zone instanceof eZPageZone )
                            $block = $zone->getBlock( $params[2] );

                        if ( $block instanceof eZPageBlock )
                        {
                            foreach ( $selectedNodeIDArray as $index => $nodeID )
                            {
                                $object = eZContentObject::fetchByNodeID( $nodeID );

                                if ( !$object instanceof eZContentObject )
                                    return false;

                                $objectID = $object->attribute( 'id' );

                                    //judge the list if there is a same item in history
                                    $itemAdded = false;
                                    $itemValid = false;
                                    $historyItems = $block->attribute( 'archived' );
                                    foreach( $historyItems as $historyItem )
                                    {
                                        if( $historyItem->attribute( 'object_id' ) == $objectID )
                                        {
                                            $itemAdded = $historyItem;
                                        }
                                    }
                                    $validItems = $block->attribute( 'valid' );
                                    foreach( $validItems as $validItem )
                                    {
                                        if( $validItem->attribute( 'object_id' ) == $objectID )
                                        {
                                            $itemValid = $validItem;
                                        }
                                    }

                                    //judge if the item will be removed
                                    $itemToBeRemoved = false;
                                    if ( $block->getItemCount() > 0 )
                                    {
                                        foreach ( $block->attribute( 'items' ) as $itemID => $item )
                                        {
                                            if ( $item->attribute( 'object_id' ) == $objectID )
                                            {
                                                if ( $item->toBeRemoved() )
                                                {
                                                    $itemToBeRemoved = true;
                                                    $itemAdded = $item;
                                                 }
                                            }
                                        }
                                    }

                                    if( $itemAdded || $itemToBeRemoved )
                                    {
                                        //if there is same item in history, or item to be removed (in history or valid), set the item in history to be modified
                                        // if item is not to be removed, add to the block since it's not in block ,but in history or valid
                                        if( !$itemToBeRemoved )
                                        {
                                            $block->addItem( $itemAdded );
                                        }
                                        $itemAdded->setXMLStorable( true );
                                        $itemAdded->setAttribute( 'node_id', $nodeID );
                                        $itemAdded->setAttribute( 'priority', $block->getItemCount() );
                                        $itemAdded->setAttribute( 'ts_publication', time() );
                                        $itemAdded->setAttribute( 'ts_visible', '0' );
                                        $itemAdded->setAttribute( 'ts_hidden', '0' );
                                        $itemAdded->setAttribute( 'action', 'modify' );
                                    }
                                    else
                                    {
                                        if( !$itemValid )
                                        {
                                            //if there is no same item in history and valid, also the item is not to be removed, add new
                                            $item = $block->addItem( new eZPageBlockItem() );
                                            $item->setAttribute( 'object_id', $objectID );
                                            $item->setAttribute( 'node_id', $nodeID );
                                            $item->setAttribute( 'priority', $block->getItemCount() );
                                            $item->setAttribute( 'ts_publication', time() );
                                            $item->setAttribute( 'action', 'add' );
                                        }
                                    }
                                }
                            }

                            $contentObjectAttribute->setContent( $page );
                            $contentObjectAttribute->store();
                        }
                    }
                    break;
            case 'new_item_browse':
                $module = $parameters['module'];
                $redirectionURI = $redirectionURI = $parameters['current-redirection-uri'];

                $page = $contentObjectAttribute->content();
                $zone = $page->getZone( $params[1] );
                $block = $zone->getBlock( $params[2] );

                $type = $block->attribute( 'type' );
                $blockINI = eZINI::instance( 'block.ini' );
                $classArray = false;

                if( $blockINI->hasVariable( $type, 'AllowedClasses' ) )
                    $classArray = $blockINI->variable( $type, 'AllowedClasses' );

                $browseParameters = array( 'class_array' => $classArray,
                                           'action_name' => 'AddNewBlockItem',
                                           'browse_custom_action' => array( 'name' => 'CustomActionButton[' . $contentObjectAttribute->attribute( 'id' ) . '_new_item-' . $params[1] . '-' . $params[2] . ']',
                                                                            'value' => $contentObjectAttribute->attribute( 'id' ) ),
                                           'from_page' => $redirectionURI,
                                           'cancel_page' => $redirectionURI,
                                           'persistent_data' => array( 'HasObjectInput' => 0 ) );

                if( $blockINI->hasVariable( $block->attribute( 'type' ), 'ManualBlockStartBrowseNode' ) )
                {
                    $browseParameters['start_node'] = $blockINI->variable( $block->attribute( 'type' ), 'ManualBlockStartBrowseNode' );
                }

                eZContentBrowse::browse( $browseParameters, $module );
                break;
            case 'new_source':
                $page = $contentObjectAttribute->content();

                $zone = $page->getZone( $params[1] );
                $block = $zone->getBlock( $params[2] );

                if ( $http->hasPostVariable( 'SelectedNodeIDArray' ) )
                {
                    $selectedNodeIDArray = $http->postVariable( 'SelectedNodeIDArray' );
                    $blockINI = eZINI::instance( 'block.ini' );

                    $fetchParametersSelectionType = $blockINI->variable( $block->attribute( 'type' ), 'FetchParametersSelectionType' );
                    $fetchParams = unserialize( $block->attribute( 'fetch_params' ) );

                    if ( $fetchParametersSelectionType['Source'] == 'single' )
                        $fetchParams['Source'] = $selectedNodeIDArray[0];
                    else
                        $fetchParams['Source'] = $selectedNodeIDArray;

                    $block->setAttribute( 'fetch_params', serialize( $fetchParams ) );

                    $persBlockObject = eZFlowBlock::fetch( $block->attribute( 'id' ) );

                    if ( $persBlockObject instanceof eZFlowBlock )
                    {
                        $persBlockObject->setAttribute( 'last_update', 0 );
                        $persBlockObject->store();
                    }
                }

                $contentObjectAttribute->setContent( $page );
                $contentObjectAttribute->store();
                break;
            case 'new_source_browse':
                $page = $contentObjectAttribute->content();
                $zone = $page->getZone( $params[1] );
                $block = $zone->getBlock( $params[2] );

                $blockINI = eZINI::instance( 'block.ini' );

                $fetchParametersSelectionType = $blockINI->variable( $block->attribute( 'type' ), 'FetchParametersSelectionType' );

                $module = $parameters['module'];
                $redirectionURI = $redirectionURI = $parameters['current-redirection-uri'];

                $browseParameters = array( 'action_name' => 'AddNewBlockSource',
                                           'selection' => $fetchParametersSelectionType['Source'],
                                           'browse_custom_action' => array( 'name' => 'CustomActionButton[' . $contentObjectAttribute->attribute( 'id' ) . '_new_source-' . $params[1] . '-' . $params[2] . ']',
                                                                            'value' => $contentObjectAttribute->attribute( 'id' ) ),
                                           'from_page' => $redirectionURI,
                                           'cancel_page' => $redirectionURI,
                                           'persistent_data' => array( 'HasObjectInput' => 0 ) );

                if( $blockINI->hasVariable( $block->attribute( 'type' ), 'DynamicBlockStartBrowseNode' ) )
                {
                    $browseParameters['start_node'] = $blockINI->variable( $block->attribute( 'type' ), 'DynamicBlockStartBrowseNode' );
                }

                eZContentBrowse::browse( $browseParameters, $module );
                break;
            case 'remove_source':
                $page = $contentObjectAttribute->content();

                $zone = $page->getZone( $params[1] );
                $block = $zone->getBlock( $params[2] );

                $fetchParams = unserialize( $block->attribute( 'fetch_params' ) );

                unset( $fetchParams['Source'] );

                $block->setAttribute( 'fetch_params', serialize( $fetchParams ) );
                $contentObjectAttribute->setContent( $page );
                $contentObjectAttribute->store();
                break;
            case 'custom_attribute':
                $page = $contentObjectAttribute->content();
                $zone = $page->getZone( $params[1] );
                $block = $zone->getBlock( $params[2] );

                if ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
                {
                    $customAttributes = $block->attribute( 'custom_attributes' );

                    if ( $http->hasPostVariable( 'SelectedNodeIDArray' ) )
                    {
                        $selectedNodeIDArray = $http->postVariable( 'SelectedNodeIDArray' );
                        $customAttributes[$params[3]] = $selectedNodeIDArray[0];
                    }

                    $block->setAttribute( 'custom_attributes', $customAttributes );
                    $contentObjectAttribute->setContent( $page );
                    $contentObjectAttribute->store();
                }
                break;
            case 'custom_attribute_browse':
                $module = $parameters['module'];
                $redirectionURI = $redirectionURI = $parameters['current-redirection-uri'];
                $page = $contentObjectAttribute->content();
                $zone = $page->getZone( $params[1] );
                $block = $zone->getBlock( $params[2] );
                $blockINI = eZINI::instance( 'block.ini' );

                $browseParameters = array( 'action_name' => 'CustomAttributeBrowse',
                                           'browse_custom_action' => array( 'name' => 'CustomActionButton[' . $contentObjectAttribute->attribute( 'id' ) . '_custom_attribute-' . $params[1] . '-' . $params[2] . '-' . $params[3] . ']',
                                                                            'value' => $contentObjectAttribute->attribute( 'id' ) ),
                                           'from_page' => $redirectionURI,
                                           'cancel_page' => $redirectionURI,
                                           'persistent_data' => array( 'HasObjectInput' => 0 ) );

                if( $blockINI->hasVariable( $block->attribute( 'type' ), 'CustomAttributeStartBrowseNode' ) )
                {
                    $customAttributeStartBrowseNode = $blockINI->variable( $block->attribute( 'type' ), 'CustomAttributeStartBrowseNode' );
                    $customAttributeIdentifier = $params[3];
                    if( isset( $customAttributeStartBrowseNode[$customAttributeIdentifier] ) )
                    {
                        $browseParameters['start_node'] = $customAttributeStartBrowseNode[$customAttributeIdentifier];
                    }
                }

                eZContentBrowse::browse( $browseParameters, $module );
                break;
            case 'custom_attribute_remove_source':
                $page = $contentObjectAttribute->content();
                $zone = $page->getZone( $params[1] );
                $block = $zone->getBlock( $params[2] );

                $customAttributes = $block->attribute( 'custom_attributes' );

                unset( $customAttributes[$params[3]] );

                $block->setAttribute( 'custom_attributes', $customAttributes );
                $contentObjectAttribute->setContent( $page );
                $contentObjectAttribute->store();
                break;
            case 'remove_item':
                $page = $contentObjectAttribute->content();
                $zone = $page->getZone( $params[1] );
                $block = $zone->getBlock( $params[2] );

                $deleteItemIDArray = $http->postVariable( 'DeleteItemIDArray' );

                if ( $block->getItemCount() > 0 )
                {
                    foreach ( $block->attribute( 'items' ) as $itemID => $item )
                    {
                        foreach ( $deleteItemIDArray as $index => $deleteItemID )
                        {
                            if ( $item->attribute( 'object_id' ) == $deleteItemID )
                            {
                                if ( $item->toBeAdded() )
                                {
                                    $block->removeItem( $itemID );
                                    unset( $deleteItemIDArray[$index] );
                                }
                                elseif( $item->toBeModified() )
                                {
                                    $block->removeItem( $itemID );
                                }
                             }
                         }
                     }
                 }

                 foreach ( $deleteItemIDArray as $deleteItemID )
                 {
                     $item = $block->addItem( new eZPageBlockItem() );
                     $item->setAttribute( 'object_id', $deleteItemID );
                     $item->setAttribute( 'action', 'remove' );
                 }

                 break;
            default:
            break;
        }
    }

    /**
     * Performs necessary actions with attribute data after object is published,
     * it means that you have access to published nodes.
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param eZContentObject $contentObject
     * @param array(eZContentObjectTreeNode) $publishedNodes
     * @return bool
     */
    function onPublish( $contentObjectAttribute, $contentObject, $publishedNodes )
    {
        $db = eZDB::instance();
        $page = $contentObjectAttribute->content();

        foreach ( $publishedNodes as $publishedNode )
        {
            if ( $publishedNode->isMain() )
                $mainNode = $publishedNode;
        }

        foreach ( $publishedNodes as $node )
        {
            $nodeID = $node->attribute( 'node_id' );

            if ( $page->getZoneCount() != 0 )
            {
                foreach ( $page->attribute( 'zones' ) as $zone )
                {
                    if ( $zone->getBlockCount() != 0 )
                    {
                        if ( $zone->toBeRemoved() )
                        {
                            foreach ( $zone->attribute( 'blocks' ) as $index => $block )
                            {
                                $block->setAttribute( 'action', 'remove' );
                            }
                        }

                        $newItems = array();

                        foreach ( $zone->attribute( 'blocks' ) as $block )
                        {
                            $blockID = $block->attribute( 'id' );
                            $fetchParams = $block->attribute( 'fetch_params' );
                            $escapedBlockName = $db->escapeString( $block->attribute( 'name' ) );

                            switch ( $block->attribute( 'action' ) )
                            {
                                case 'remove':
                                    $db->query( "UPDATE ezm_block SET is_removed='1' WHERE id='" . $blockID . "'" );
                                    break;

                                case 'add':
                                    $blockCount = $db->arrayQuery( "SELECT COUNT( id ) as count FROM ezm_block WHERE id='" . $blockID ."'" );

                                    if ( $blockCount[0]['count'] == 0 )
                                    {
                                        $rotationType = 0;
                                        $rotationInterval = 0;
                                        $overflowID = null;

                                        if ( $block->hasAttribute( 'rotation' ) )
                                        {
                                            $rotation = $block->attribute( 'rotation' );

                                            $rotationType = $rotation['type'];
                                            $rotationInterval = $rotation['interval'];
                                        }


                                        if ( $block->hasAttribute( 'overflow_id' ) )
                                        $overflowID = $block->attribute( 'overflow_id' );

                                        $db->query( "INSERT INTO ezm_block ( id, zone_id, name, node_id, overflow_id, block_type, fetch_params, rotation_type, rotation_interval )
                                                                    VALUES ( '" . $blockID . "',
                                                                             '" . $block->attribute( 'zone_id' ) . "',
                                                                             '" . $escapedBlockName . "',
                                                                             '" . $nodeID . "',
                                                                             '" . $overflowID . "',
                                                                             '" . $db->escapeString( $block->attribute( 'type' ) ) . "',
                                                                             '" . $fetchParams . "',
                                                                             '" . $rotationType . "',
                                                                             '" . $rotationInterval . "' )" );
                                    }
                                    break;

                                default:
                                    $rotationType = 0;
                                    $rotationInterval = 0;
                                    $overflowID = null;

                                    if ( $block->hasAttribute( 'rotation' ) )
                                    {
                                        $rotation = $block->attribute( 'rotation' );

                                        $rotationType = $rotation['type'];
                                        $rotationInterval = $rotation['interval'];
                                    }

                                    if ( $block->hasAttribute( 'overflow_id' ) )
                                        $overflowID = $block->attribute( 'overflow_id' );

                                    // Fixes http://jira.ez.no/browse/EZP-23124 where ezm_block.node_id might be = 0
                                    // due to the way staging handles ezflow
                                    // If the block's node id is set to 0, it gets set to the main node ID
                                    $blockNodeId = $block->attribute( 'node_id' ) ?: $mainNode->attribute( 'node_id' );

                                    $db->query( "UPDATE ezm_block SET name='" . $escapedBlockName . "',
                                                                      overflow_id='" . $overflowID . "',
                                                                      fetch_params='" . $fetchParams . "',
                                                                      rotation_type='" . $rotationType . "',
                                                                      rotation_interval='" . $rotationInterval ."',
                                                                      node_id='" . $blockNodeId. "'
                                                            WHERE id='" . $blockID . "'" );
                                    break;
                            }

                            if ( $block->getItemCount() != 0 )
                            {
                                foreach ( $block->attribute( 'items' ) as $item )
                                {
                                    switch ( $item->attribute( 'action' ) )
                                    {
                                        case 'remove':

                                            $db->query( "DELETE FROM ezm_pool
                                                            WHERE object_id='" . $item->attribute( 'object_id' ) . "'
                                                            AND block_id='" . $blockID . "'" );
                                            break;

                                        case 'add':
                                            $newItems[] =  array(
                                                'blockID' => $blockID,
                                                'objectID' => $item->attribute( 'object_id' ),
                                                'nodeID' => $item->attribute( 'node_id' ),
                                                'priority' => $item->attribute( 'priority' ),
                                                'timestamp' => $item->attribute( 'ts_publication' ),
                                            );
                                            break;

                                        case 'modify':
                                            $updateQuery = array();

                                            if ( $item->hasAttribute( 'ts_publication' ) )
                                            {
                                                $updateQuery[] = " ts_publication="
                                                    . (int)$item->attribute( 'ts_publication' );
                                            }

                                            //make sure to update different node locations of the same object
                                            if ( $item->hasAttribute( 'node_id' ) )
                                            {
                                                $updateQuery[] = " node_id="
                                                    . (int)$item->attribute( 'node_id' );
                                            }

                                            if ( $item->hasAttribute( 'priority' ) )
                                            {
                                                $updateQuery[] = " priority="
                                                    . (int)$item->attribute( 'priority' );
                                            }

                                            //if there is ts_hidden and ts_visible, update the two fields. This is the case when add items from history
                                            if ( $item->hasAttribute( 'ts_hidden' ) && $item->hasAttribute( 'ts_visible' ) )
                                            {
                                                $updateQuery[] = " ts_hidden="
                                                    . (int)$item->attribute( 'ts_hidden' )
                                                    . ", ts_visible="
                                                    . (int)$item->attribute( 'ts_visible' );
                                            }

                                            if ( !empty( $updateQuery ) )
                                            {
                                                $db->query(
                                                    "UPDATE ezm_pool SET "
                                                    . join( ", ", $updateQuery )
                                                    . " WHERE object_id="
                                                    . (int)$item->attribute( 'object_id' )
                                                    . " AND block_id='" . $blockID ."'"
                                                );
                                            }
                                            break;
                                    }
                                }
                            }
                        }

                        if ( !empty( $newItems ) )
                        {
                            eZFlowPool::insertItems( $newItems );
                        }
                    }
                }
            }
        }

        if ( eZFlowOperations::updateOnPublish() )
        {
            $nodeArray = array();
            foreach ( $publishedNodes as $node )
            {
                $nodeArray[] = $node->attribute( 'node_id' );
            }
            eZFlowOperations::update( $nodeArray );
        }

        foreach ( $publishedNodes as $node )
        {
            $url = $node->attribute( 'path_identification_string' );
            eZURI::transformURI( $url, false, 'full' );
            eZHTTPCacheManager::execute( $url );
        }

        $page->removeProcessed();

        $contentObjectAttribute->content( $page );
        $contentObjectAttribute->store();

        return true;
    }

    /**
     * return string representation of an contentobjectattribute data for simplified export.
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return string
     */
    function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    /**
     * Set contentobject attribute data from $string
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param string $string
     * @return bool
     */
    function fromString( $contentObjectAttribute, $string )
    {
        return $contentObjectAttribute->setAttribute( 'data_text', $string );
    }

    /**
     * Return a DOM representation of the content object attribute
     *
     * @param eZPackage $package
     * @param eZContentObjectAttribute $objectAttribute
     * @return DOMElement
     */
    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        $dom = new DOMDocument( '1.0', 'utf-8' );
        $success = $dom->loadXML( $objectAttribute->attribute( 'data_text' ) );

        $importedRoot = $node->ownerDocument->importNode( $dom->documentElement, true );
        $node->appendChild( $importedRoot );

        return $node;
    }

    /**
     * Unserailize contentobject attribute
     *
     * @param eZPackage $package
     * @param eZContentObjectAttribute $objectAttribute
     * @param DOMElement $attributeNode
     */
    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $rootNode = $attributeNode->childNodes->item( 0 );
        $xmlString = $rootNode ? $rootNode->ownerDocument->saveXML( $rootNode ) : '';
        $objectAttribute->setAttribute( 'data_text', $xmlString );
    }
}

eZDataType::register( eZPageType::DATA_TYPE_STRING, "ezpagetype" );
?>
