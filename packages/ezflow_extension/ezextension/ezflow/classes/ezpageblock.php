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

include_once( 'extension/ezflow/classes/ezflowpool.php' );
include_once( 'extension/ezflow/classes/ezpageblockitem.php' );

class eZPageBlock
{
    var $attributes = array();
    var $dynamicAttributeFunctions = array(
        'waiting' => 'getWaitingItems',
        'valid' => 'getValidItems',
        'valid_nodes' => 'getValidItemsAsNodes',
        'archived' => 'getArchivedItems',
        'view_template' => 'viewTemplate',
        'edit_template' => 'editTemplate'
        );

        function eZPageBlock( $name = null, $row = null )
        {
            if ( isset( $name ) )
            $this->attributes['name'] = $name;

            if ( isset( $row ) )
            $this->attributes = $row;
        }

        function id()
        {
            return $this->attributes['id'];
        }

        function &addItem( $item )
        {
            $this->attributes['items'][] =& $item;
            return $item;
        }

        function toXML( $dom )
        {
            $blockNode = $dom->createElement( 'block' );

            foreach ( $this->attributes as $attrName => $attrValue )
            {
                switch ( $attrName )
                {
                    case 'id':
                        $blockNode->setAttribute( 'id', 'id_' . $attrValue );
                        break;

                    case 'action':
                        $blockNode->setAttribute( 'action', $attrValue );
                        break;

                    case 'items':
                        foreach ( $this->attributes['items'] as $item )
                        {
                            $itemNode = $item->toXML( $dom );
                            if ( $itemNode )
                            {
                                $blockNode->appendChild( $itemNode );
                            }
                        }
                        break;

                    case 'rotation':
                        $node = $dom->createElement( $attrName );
                        $blockNode->appendChild( $node );

                        foreach ( $attrValue as $arrayItemKey => $arrayItemValue )
                        {
                            $tmp = $dom->createElement( $arrayItemKey );
                            $tmpValue = $dom->createTextNode( $arrayItemValue );
                            $tmp->appendChild( $tmpValue );
                            $node->appendChild( $tmp );
                        }
                        break;

                    case 'custom_attributes':
                        $node = $dom->createElement( $attrName );
                        $blockNode->appendChild( $node );

                        foreach ( $attrValue as $arrayItemKey => $arrayItemValue )
                        {
                            $tmp = $dom->createElement( $arrayItemKey );
                            $tmpValue = $dom->createTextNode( $arrayItemValue );
                            $tmp->appendChild( $tmpValue );
                            $node->appendChild( $tmp );
                        }
                        break;

                    default:
                        $node = $dom->createElement( $attrName );
                        $nodeValue = $dom->createTextNode( $attrValue );
                        $node->appendChild( $nodeValue );
                        $blockNode->appendChild( $node );
                        break;
                }
            }



            return $blockNode;
        }

        static function &createFromXML( $node )
        {
            $newObj = new eZPageBlock();

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
                if ( $node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'item' )
                {
                    $blockItemNode =& eZPageBlockItem::createFromXML( $node );
                    $newObj->attributes['items'][] =& $blockItemNode;
                }
                elseif ( $node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'rotation' )
                {
                    foreach ( $node->childNodes as $subNode )
                    {
                        if ( $subNode->nodeType == XML_ELEMENT_NODE )
                        $newObj->attributes[$node->nodeName][$subNode->nodeName] = $subNode->nodeValue;
                    }
                }
                elseif ( $node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'custom_attributes' )
                {
                    foreach ( $node->childNodes as $subNode )
                    {
                        if ( $subNode->nodeType == XML_ELEMENT_NODE )
                        $newObj->attributes[$node->nodeName][$subNode->nodeName] = $subNode->nodeValue;
                    }
                }
                else
                {
                    if ( $node->nodeType == XML_ELEMENT_NODE )
                    $newObj->attributes[$node->nodeName] = $node->nodeValue;
                }
            }

            return $newObj;
        }

        function removeItem( $id )
        {
            unset( $this->attributes['items'][$id] );
        }

        function getName()
        {
            return isset( $this->attributes['name'] ) ? $this->attributes['name'] : null;
        }

        function getItemCount()
        {
            return isset( $this->attributes['items'] ) ? count( $this->attributes['items'] ) : 0;
        }

        function &getItem( $id )
        {
            $item =& $this->attributes['items'][$id];
            return $item;
        }

        function attributes()
        {
            return array_merge( array_keys( $this->attributes ), array_keys( $this->dynamicAttributeFunctions ) );
        }

        function hasAttribute( $name )
        {
            return in_array( $name, array_keys( $this->attributes ) ) || isset( $this->dynamicAttributeFunctions[$name] );
        }

        function setAttribute( $name, $value )
        {
            if ( isset( $this->dynamicAttributeFunctions[$name] ) )
            {
                switch ( $name )
                {
                    case 'valid_nodes':
                        $this->dynamicAttributeFunctions[$name] = $value;
                        break;
                    default
                        ;
                    break;
                }
            }
            else
            {
                $this->attributes[$name] = $value;
            }
        }

        function &attribute( $name )
        {
            if ( isset( $this->dynamicAttributeFunctions[$name] ) )
            {
                if ( is_array( $this->dynamicAttributeFunctions[$name] ) )
                {
                    return $this->dynamicAttributeFunctions[$name];
                }
                else
                {
                    $attribute = call_user_func( array( $this, $this->dynamicAttributeFunctions[$name] ) );
                    return $attribute;
                }
            }
            else
            {
                return $this->attributes[$name];
            }
        }

        function removeProcessed()
        {
            if ( $this->hasAttribute( 'action' ) )
            {
                unset( $this->attributes['action'] );
            }

            if ( $this->getItemCount() > 0 )
            {
                unset( $this->attributes['items'] );
            }

            return $this;
        }

        function fetch( $blockID, $asObject = true )
        {
            $db = eZDB::instance();
            $row = $db->arrayQuery( "SELECT *
                                   FROM ezm_block
                                   WHERE id='$blockID'" );
            if ( $asObject )
            {
                $block = new eZPageBlock( null, $row[0] );
                return $block;
            }
            else
            {
                return $row[0];
            }
        }

        // protected
        function merge( $items, $mergeAdded = false )
        {
            $itemObjects = array();
            foreach ( $items as $item )
            {
                $oid = $item['object_id'];
                $itemObjects[$oid] = new eZPageBlockItem( $item, false );
            }

            if ( isset( $this->attributes['items'] ) && $this->attributes['items'] )
            {
                foreach ( $this->attributes['items'] as $item )
                {
                    $oid = $item->attribute( 'object_id' );
                    if ( $item->toBeRemoved() )
                    {
                        unset( $itemObjects[$oid] );
                    }
                    if ( $mergeAdded && $item->toBeAdded() )
                    {
                        $itemObjects[$oid] = $item;
                    }
                    if ( $item->toBeModified() )
                    {
                        if ( isset( $itemObjects[$oid] ) )
                        {
                            $itemObjects[$oid]->setAttribute( 'ts_publication', $item->attribute( 'ts_publication' ) );
                            $itemObjects[$oid]->setAttribute( 'priority', $item->attribute( 'priority' ) );
                        }
                    }
                }
            }

            usort( $itemObjects, array( &$this, 'sortItems' ) );

            return $itemObjects;
        }

        // Function attributes
        function getWaitingItems()
        {
            $waitingItems = eZFlowPool::waitingItems( $this->id() );
            return $this->merge( $waitingItems, true );
        }

        function getValidItems()
        {
            $validItems = eZFlowPool::validItems( $this->id() );
            return $this->merge( $validItems );
        }

        function getValidItemsAsNodes()
        {
            $validItemsAsNodes = eZFlowPool::validNodes( $this->id() );
            return $validItemsAsNodes;
        }

        function getArchivedItems()
        {
            $archivedItems = eZFlowPool::archivedItems( $this->id() );
            return $this->merge( $archivedItems );
        }

        function viewTemplate()
        {
            $template = 'view';
            return $template;
        }

        function editTemplate()
        {
            $template = 'edit';
            return $template;
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

        function sortItems( $a, $b )
        {
            if ( $a->attributes['ts_publication'] == $b->attributes['ts_publication'] )
            {
                if ( $a->attributes['priority'] > $b->attributes['priority'] )
                {
                    return 1;
                }
                else if ( $a->attributes['priority'] < $b->attributes['priority'] )
                {
                    return -1;
                }
                else
                {
                    return 0;
                }
            }
            else if ( $a->attributes['ts_publication'] > $b->attributes['ts_publication'] )
            {
                return 1;
            }
            else
            {
                return -1;
            }
        }

}

?>