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

include_once( 'extension/ezflow/classes/ezpage.php' );
include_once( 'extension/ezflow/classes/ezpagezone.php' );
include_once( 'extension/ezflow/classes/ezpageblock.php' );
include_once( 'extension/ezflow/classes/ezpageblockitem.php' );
include_once( 'extension/ezflow/classes/ezflowpool.php' );
include_once( 'extension/ezflow/classes/ezflowoperations.php' );
include_once( 'extension/ezflow/classes/ezsquidcachemanager.php' );

class eZPageType extends eZDataType
{
    const DATA_TYPE_STRING = 'ezpage';
    /*!
     Constructor
     */
    function eZPageType()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, "Layout" );
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $page = $contentObjectAttribute->content();
        $zones = $page->attribute( 'zones' );
        return count( $zones ) > 0;
    }

    /*!
     Validates all variables given on content class level
     \return EZ_INPUT_VALIDATOR_STATE_ACCEPTED or EZ_INPUT_VALIDATOR_STATE_INVALID if
     the values are accepted or not
     */
    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches all variables inputed on content class level
     \return true if fetching of class attributes are successfull, false if not
     */
    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return true;
    }
    /*!
     Validates input on content object level
     \return EZ_INPUT_VALIDATOR_STATE_ACCEPTED or EZ_INPUT_VALIDATOR_STATE_INVALID if
     the values are accepted or not
     */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches all variables from the object
     \return true if fetching of class attributes are successfull, false if not
     */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $page = $contentObjectAttribute->content();

        //var_dump($_POST);

        $blockINI = eZINI::instance( 'block.ini' );


        if ( $http->hasPostVariable( $base . '_ezpage_block_fetch_param_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $blockFetchParams = $http->postVariable( $base . '_ezpage_block_fetch_param_' . $contentObjectAttribute->attribute( 'id' ) );

            foreach ( $blockFetchParams as $zoneID => $blocks )
            {
                $zone =& $page->getZone( $zoneID );

                foreach ( $blocks as $blockID => $params )
                {
                    $block =& $zone->getBlock( $blockID );

                    $fetchParams = unserialize( $block->attribute( 'fetch_params' ) );

                    foreach ( $params as $param => $value )
                    {
                        $fetchParams[$param] = $value;
                    }

                    $block->setAttribute( 'fetch_params', serialize( $fetchParams ) );
                }
            }
        }


        if ( $http->hasPostVariable( $base . '_ezpage_block_view_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {

            $blockViewArray = $http->postVariable( $base . '_ezpage_block_view_' . $contentObjectAttribute->attribute( 'id' ) );

            foreach ( $blockViewArray as $zoneID => $blocks )
            {
                $zone =& $page->getZone( $zoneID );

                foreach ( $blocks as $blockID => $view )
                {
                    $block =& $zone->getBlock( $blockID );
                    $block->setAttribute( 'view', $view );

                }
            }
        }

        if ( $http->hasPostVariable( $base . '_ezpage_block_overflow_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $blockOverflowArray = $http->postVariable( $base . '_ezpage_block_overflow_' . $contentObjectAttribute->attribute( 'id' ) );

            foreach ( $blockOverflowArray as $zoneID => $blocks )
            {
                $zone =& $page->getZone( $zoneID );

                foreach ( $blocks as $blockID => $overflowBlockID )
                {
                    $block =& $zone->getBlock( $blockID );
                    $block->setAttribute( 'overflow_id', $overflowBlockID );
                }
            }

        }

        if ( $http->hasPostVariable( $base . '_ezpage_block_name_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $blockNameArray = $http->postVariable( $base . '_ezpage_block_name_array_' . $contentObjectAttribute->attribute( 'id' ) );

            foreach ( $blockNameArray as $zoneID => $blocks )
            {
                $zone =& $page->getZone( $zoneID );

                foreach ( $blocks as $blockID => $blockName )
                {
                    $block =& $zone->getBlock( $blockID );
                    $block->setAttribute( 'name', $blockName );
                }
            }

        }

        if ( $http->hasPostVariable( $base . '_ezpage_item_ts_published_value_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $itemTSPublishedValueArray = $http->postVariable( $base . '_ezpage_item_ts_published_value_' . $contentObjectAttribute->attribute( 'id' ) );

            foreach ( $itemTSPublishedValueArray as $zoneID => $blocks )
            {
                $zone =& $page->getZone( $zoneID );

                foreach ( $blocks as $blockID => $itemTSPublishedValueIDs )
                {
                    $block =& $zone->getBlock( $blockID );

                    if ( $block->getItemCount() > 0 )
                    {
                        $items =& $block->attribute( 'items' );

                        foreach ( $items as $index => $item )
                        {
                            foreach ( $itemTSPublishedValueIDs as $objectID => $value )
                            {
                                if ( $value != '' )
                                {
                                    if ( $item->attribute( 'object_id' ) == $objectID )
                                    {
                                        //$itemTSPublishedUnitArray = $http->postVariable( $base . '_ezpage_item_ts_published_unit_' . $contentObjectAttribute->attribute( 'id' ) );
                                        /*
                                         switch ( $itemTSPublishedUnitArray[$zoneID][$blockID][$objectID] )
                                         {
                                         case 1:
                                         $item->setAttribute( 'ts_publication', time() + $value );
                                         $block->attributes['items'][$index] = $item;
                                         break;
                                         case 2:
                                         $item->setAttribute( 'ts_publication', time() + ( $value * 60 ) );
                                         $block->attributes['items'][$index] = $item;
                                         break;
                                         }
                                         */

                                        $item->setAttribute( 'ts_publication', time() + ( $value * 60 ) );
                                        $block->attributes['items'][$index] = $item;

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
                            $item =& $block->addItem( new eZPageBlockItem() );
                            $item->setAttribute( 'action', 'modify' );
                            $item->setAttribute( 'object_id', $objectID );

                            $itemTSPublishedUnitArray = $http->postVariable( $base . '_ezpage_item_ts_published_unit_' . $contentObjectAttribute->attribute( 'id' ) );

                            switch ( $itemTSPublishedUnitArray[$zoneID][$blockID][$objectID] )
                            {
                                case 1:
                                    $item->setAttribute( 'ts_publication', time() + $value );
                                    break;
                                case 2:
                                    $item->setAttribute( 'ts_publication', time() + ( $value * 60 ) );
                                    break;
                            }
                        }
                    }
                }

            }
        }

        //var_dump($page);

        $contentObjectAttribute->setContent( $page );

        return true;
    }

    function storeObjectAttribute( $contentObjectAttribute )
    {
        $page = $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( 'data_text', $page->toXML() );
    }

    /*!
     Returns the content.
     */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $source = $contentObjectAttribute->attribute( 'data_text' );
        $page = eZPage::createFromXML( $source );

        return $page;
    }

    /*!
     Returns the meta data used for storing search indeces.
     */
    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    /*!
     Returns the value as it will be shown if this attribute is used in the object name pattern.
     */
    function title( $contentObjectAttribute, $name = null  )
    {
        return '';
    }

    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        $params = explode( '-', $action );

        switch ( $params[0] )
        {
            case 'new_zone_layout':
                if ( $http->hasPostVariable( 'ContentObjectAttribute_ezpage_zone_allowed_type_' . $contentObjectAttribute->attribute( 'id' ) ) )
                {
                    $page = $contentObjectAttribute->content();
                    $zoneAllowedType = $http->postVariable( 'ContentObjectAttribute_ezpage_zone_allowed_type_' . $contentObjectAttribute->attribute( 'id' ) );
                    $page->setAttribute( 'zone_layout', $zoneAllowedType );

                    if ( $page->getZoneCount() > 0)
                    $page->removeZones();

                    $zoneINI = eZINI::instance( 'zone.ini' );

                    foreach ( $zoneINI->variable( $zoneAllowedType, 'Zones' ) as $zone )
                    {
                        $newZone =& $page->addZone( new eZPageZone() );
                        $newZone->setAttribute( 'id', md5( microtime() . $page->getZoneCount() ) );
                        $newZone->setAttribute( 'zone_identifier', $zone );
                        $newZone->setAttribute( 'action', 'add' );
                    }

                    $contentObjectAttribute->setContent( $page );
                    $contentObjectAttribute->store();
                }
                break;

            case 'set_rotation':

                $page = $contentObjectAttribute->content();
                $zone =& $page->getZone( $params[1] );
                $block =& $zone->getBlock( $params[2] );

                $rotationValue = $http->postVariable( 'RotationValue_' . $params[2] );
                $rotationUnit = $http->postVariable( 'RotationUnit_' . $params[2] );
                $rotationSuffle = $http->postVariable( 'RotationShuffle_' . $params[2] );

                if ( $rotationValue == '' )
                {
                    $block->setAttribute( 'rotation', array( 'interval' => 0,
                                                             'type' => 0,
                                                             'value' => '',
                                                             'unit' => '' ) );
                }
                else
                {
                    switch ( $rotationUnit )
                    {
                        case '1':
                            $rotationInterval = $rotationValue;
                            break;

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

                $contentObjectAttribute->setContent( $page );
                $contentObjectAttribute->store();
                break;

                        case 'remove_block':

                            $page = $contentObjectAttribute->content();
                            $zone =& $page->getZone( $params[1] );
                            $block =& $zone->getBlock( $params[2] );

                            if ( $block->toBeAdded() )
                            {
                                $zone->removeBlock( $params[2] );
                            }
                            else
                            {
                                $block->setAttribute( 'action', 'remove' );
                            }

                            $contentObjectAttribute->setContent( $page );
                            $contentObjectAttribute->store();
                            break;

                        case 'new_block':

                            $page = $contentObjectAttribute->content();
                            $zone =& $page->getZone( $params[1] );

                            if ( $http->hasPostVariable( 'ContentObjectAttribute_ezpage_block_type_' . $contentObjectAttribute->attribute( 'id' ) . '_' . $params[1] ) )
                            $blockType = $http->postVariable( 'ContentObjectAttribute_ezpage_block_type_' . $contentObjectAttribute->attribute( 'id' ) . '_' . $params[1] );

                            if ( $http->hasPostVariable( 'ContentObjectAttribute_ezpage_block_name_' . $contentObjectAttribute->attribute( 'id' ) . '_' . $params[1] ) )
                            $blockName = $http->postVariable( 'ContentObjectAttribute_ezpage_block_name_' . $contentObjectAttribute->attribute( 'id' ) . '_' . $params[1] );

                            $block =& $zone->addBlock( new eZPageBlock( $blockName ) );
                            $block->setAttribute( 'action', 'add' );
                            $block->setAttribute( 'id', md5( microtime() . $zone->getBlockCount() ) );
                            $block->setAttribute( 'zone_id', $zone->attribute( 'id' ) );
                            $block->setAttribute( 'type', $blockType );

                            $contentObjectAttribute->setContent( $page );
                            $contentObjectAttribute->store();
                            break;

                        case 'move_block_up':

                            $page = $contentObjectAttribute->content();
                            $zone =& $page->getZone( $params[1] );
                            $zone->moveBlockUp( $params[2] );

                            $contentObjectAttribute->setContent( $page );
                            $contentObjectAttribute->store();
                            break;

                        case 'move_block_down':

                            $page = $contentObjectAttribute->content();
                            $zone =& $page->getZone( $params[1] );
                            $zone->moveBlockDown( $params[2] );

                            $contentObjectAttribute->setContent( $page );
                            $contentObjectAttribute->store();
                            break;

                        case 'new_item':

                            if ( $http->hasPostVariable( 'SelectedObjectIDArray' ) )
                            {
                                if ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
                                {
                                    $selectedObjectIDArray = $http->postVariable( 'SelectedObjectIDArray' );

                                    $page = $contentObjectAttribute->content();
                                    $zone =& $page->getZone( $params[1] );
                                    $block =& $zone->getBlock( $params[2] );

                                    if ( $block->getItemCount() > 0 )
                                    {
                                        foreach ( $block->attribute( 'items' ) as $itemID => $item )
                                        {
                                            foreach ( $selectedObjectIDArray as $index => $objectID )
                                            {
                                                if ( $item->attribute( 'object_id' ) == $objectID )
                                                {
                                                    if ( $item->toBeRemoved() )
                                                    {
                                                        $block->removeItem( $itemID );
                                                        unset( $selectedObjectIDArray[$index] );
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    foreach ( $selectedObjectIDArray as $index => $objectID )
                                    {
                                        $item =& $block->addItem( new eZPageBlockItem() );
                                        $node = eZContentObjectTreeNode::fetchByContentObjectID( $objectID );
                                        $object = $node[0]->object();
                                        $nodeID = $node[0]->attribute( 'node_id' );

                                        $item->setAttribute( 'object_id', $objectID );
                                        $item->setAttribute( 'node_id', $nodeID );
                                        $item->setAttribute( 'priority', $block->getItemCount() );
                                        $item->setAttribute( 'ts_publication', time() );
                                        $item->setAttribute( 'action', 'add' );
                                    }

                                    $contentObjectAttribute->setContent( $page );
                                    $contentObjectAttribute->store();
                                }
                            }

                            break;

                        case 'new_item_browse':
                            include_once( 'kernel/classes/ezcontentbrowse.php' );
                            $module =& $parameters['module'];
                            $redirectionURI = $redirectionURI = $parameters['current-redirection-uri'];


                            eZContentBrowse::browse( array( 'action_name' => 'AddNewBlockItem',
                                                'browse_custom_action' =>
                            array( 'name' => 'CustomActionButton[' . $contentObjectAttribute->attribute( 'id' ) . '_new_item-' . $params[1] . '-' . $params[2] . ']',
                                                                                 'value' => $contentObjectAttribute->attribute( 'id' ) ),
                                                'from_page' => $redirectionURI,
                                                'cancel_page' => $redirectionURI ),
                            $module );
                            break;
                        case 'new_source':
                            $page = $contentObjectAttribute->content();

                            $zone =& $page->getZone( $params[1] );
                            $block =& $zone->getBlock( $params[2] );

                            if ( $http->hasPostVariable( 'SelectedNodeIDArray' ) )
                            $selectedNodeIDArray = $http->postVariable( 'SelectedNodeIDArray' );

                            $blockINI =& eZINI::instance( 'block.ini' );

                            $fetchParametersSelectionType = $blockINI->variable( $block->attribute('type'), 'FetchParametersSelectionType' );

                            if ( $fetchParametersSelectionType['Source'] == 'single' )
                            $serializedParams = serialize( array( 'Source' => $selectedNodeIDArray[0] ) );
                            else
                            $serializedParams = serialize( array( 'Source' => $selectedNodeIDArray ) );

                            $block->setAttribute( 'fetch_params', $serializedParams );

                            $contentObjectAttribute->setContent( $page );
                            $contentObjectAttribute->store();
                            break;

                        case 'new_source_browse':
                            include_once( 'kernel/classes/ezcontentbrowse.php' );

                            $page = $contentObjectAttribute->content();

                            $zone =& $page->getZone( $params[1] );
                            $block =& $zone->getBlock( $params[2] );

                            $blockINI =& eZINI::instance( 'block.ini' );

                            $fetchParametersSelectionType = $blockINI->variable( $block->attribute('type'), 'FetchParametersSelectionType' );

                            $module =& $parameters['module'];
                            $redirectionURI = $redirectionURI = $parameters['current-redirection-uri'];


                            eZContentBrowse::browse( array( 'action_name' => 'AddNewBlockSource',
                                                'selection' => $fetchParametersSelectionType['Source'],
                                                'browse_custom_action' =>
                            array( 'name' => 'CustomActionButton[' . $contentObjectAttribute->attribute( 'id' ) . '_new_source-' . $params[1] . '-' . $params[2] . ']',
                                                                                 'value' => $contentObjectAttribute->attribute( 'id' ) ),
                                                'from_page' => $redirectionURI,
                                                'cancel_page' => $redirectionURI ),
                            $module );
                            break;

                        case 'custom_attribute':
                            $page = $contentObjectAttribute->content();
                            $zone =& $page->getZone( $params[1] );
                            $block =& $zone->getBlock( $params[2] );

                            if ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
                            {
                                if ( $http->hasPostVariable( 'SelectedNodeIDArray' ) )
                                {
                                    $selectedNodeIDArray = $http->postVariable ('SelectedNodeIDArray' );

                                    $customAttributes[$params[3]] = $selectedNodeIDArray[0];
                                }

                                $block->setAttribute( 'custom_attributes', $customAttributes );
                            }

                            break;

                        case 'custom_attribute_browse':
                            include_once( 'kernel/classes/ezcontentbrowse.php' );
                            $module =& $parameters['module'];
                            $redirectionURI = $redirectionURI = $parameters['current-redirection-uri'];


                            eZContentBrowse::browse( array( 'action_name' => 'CustomAttributeBrowse',
                                                'browse_custom_action' =>
                            array( 'name' => 'CustomActionButton[' . $contentObjectAttribute->attribute( 'id' ) . '_custom_attribute-' . $params[1] . '-' . $params[2] . '-' . $params[3] . ']',
                                                                                 'value' => $contentObjectAttribute->attribute( 'id' ) ),
                                                'from_page' => $redirectionURI,
                                                'cancel_page' => $redirectionURI ),
                            $module );
                            break;

                        case 'remove_item':

                            $page = $contentObjectAttribute->content();
                            $zone =& $page->getZone( $params[1] );
                            $block =& $zone->getBlock( $params[2] );

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
                                $item =& $block->addItem( new eZPageBlockItem() );
                                $item->setAttribute( 'object_id', $deleteItemID );
                                $item->setAttribute( 'action', 'remove' );
                            }


                            $contentObjectAttribute->setContent( $page );
                            $contentObjectAttribute->store();
                            break;

                        default:
                            break;
        }
    }

    function onPublish( $contentObjectAttribute, $contentObject, $publishedNodes )
    {
        $db = eZDB::instance();
        $page = $contentObjectAttribute->content();

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
                                $zone->attributes['blocks'][$index] = $block;
                            }
                        }

                        foreach ( $zone->attribute( 'blocks' ) as $block )
                        {
                            $blockID = $block->attribute( 'id' );
                            $blockType = $block->attribute( 'type' );
                            $action = $block->attribute( 'action' );
                            $fetchParams = $block->attribute( 'fetch_params' );
                            $zoneID = $block->attribute( 'zone_id' );
                            $blockName = $block->attribute( 'name' );

                            switch ( $action )
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
                                                                             '" . $zoneID . "',
                                                                             '" . $blockName . "',
                                                                             '" . $nodeID . "',
                                                                             '" . $overflowID . "',
                                                                             '" . $blockType . "',
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

                                    $db->query( "UPDATE ezm_block SET name='" . $blockName . "',
                                                                      overflow_id='" . $overflowID . "',
                                                                      fetch_params='" . $fetchParams . "',
                                                                      rotation_type='" . $rotationType . "',
                                                                      rotation_interval='" . $rotationInterval ."'
                                                            WHERE id='" . $blockID . "'" );
                                    break;
                            }

                            if ( $block->getItemCount() != 0 )
                            {
                                foreach ( $block->attribute( 'items' ) as $item )
                                {
                                    $action = $item->attribute( 'action' );

                                    switch ( $action )
                                    {
                                        case 'remove':

                                            $db->query( "DELETE FROM ezm_pool
                                                            WHERE object_id='" . $item->attribute( 'object_id' ) . "'
                                                            AND block_id='" . $blockID . "'" );
                                            break;

                                        case 'add':
                                            $itemCount = $db->arrayQuery( "SELECT COUNT( * ) as count FROM ezm_pool
                                                              WHERE block_id='" . $blockID ."'
                                                                 AND object_id='" . $item->attribute( 'object_id' ) . "'" );

                                            if ( $itemCount[0]['count'] == 0 )
                                            {
                                                $db->query( "INSERT INTO ezm_pool ( block_id, object_id, node_id, priority, ts_publication )
                                            VALUES ( '" . $blockID . "',
                                                     '" . $item->attribute( 'object_id' )  . "',
                                                     '" . $item->attribute( 'node_id' ) . "',
                                                     '" . $item->attribute( 'priority' ) . "',
                                                     '" . $item->attribute( 'ts_publication' ) . "'  )" );
                                            }
                                            break;

                                        case 'modify':
                                            $db->query( "UPDATE ezm_pool SET ts_publication='" . $item->attribute( 'ts_publication' ) . "'
                                                                WHERE object_id='" . $item->attribute( 'object_id' ) . "'" );
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        eZFlowOperations::update();

        if ( eZSquidCacheManager::isEnabled() )
        {
            foreach ( $publishedNodes as $node )
            {
                $url = $node->attribute( 'path_identification_string' );
                eZURI::transformURI( $url, false, 'full' );
                eZSquidCacheManager::purgeURL( $url );
            }
        }

        $page->removeProcessed();
        $contentObjectAttribute->content( $page );
        $contentObjectAttribute->store();
    }

    /*!
     \return true if the datatype can be indexed
     */
    function isIndexable()
    {
        return true;
    }

}

eZDataType::register( eZPageType::DATA_TYPE_STRING, "ezpagetype" );
?>