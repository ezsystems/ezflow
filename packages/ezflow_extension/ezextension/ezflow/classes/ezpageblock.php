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

class eZPageBlock
{
    private $attributes = array();
    private $dynamicAttributeFunctions = array( 'waiting' => 'getWaitingItems',
                                                'valid' => 'getValidItems',
                                                'valid_nodes' => 'getValidItemsAsNodes',
                                                'archived' => 'getArchivedItems',
                                                'view_template' => 'viewTemplate',
                                                'edit_template' => 'editTemplate',
                                                'last_valid_item' => 'getLastValidItem' );

    /**
     * Constructor
     *
     * @param string $name
     * @param array $row
     */
    function __construct( $name = null, $row = null )
    {
        if ( isset( $name ) )
            $this->attributes['name'] = $name;

        if ( isset( $row ) )
            $this->attributes = $row;
    }

    /**
     * Return object ID
     *
     * @return string
     */
    public function id()
    {
        return $this->attribute( 'id' );
    }

    /**
     * Add new $item to eZPageBlock object
     *
     * @return eZPageBlockItem
     * @param eZPageBlockItem $item
     */
    public function addItem( eZPageBlockItem $item )
    {
        $this->attributes['items'][] = $item;
        return $item;
    }

    /**
     * Creates DOMElement with block data
     *
     * @param DOMDocument $dom
     * @return DOMElement
     */
    public function toXML( DOMDocument $dom )
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

    /**
     * Creates and return eZPageBlock object from given XML
     *
     * @static
     * @param DOMElement $node
     * @return eZPageBlock
     */
    public static function createFromXML( DOMElement $node )
    {
        $newObj = new eZPageBlock();

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
            if ( $node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'item' )
            {
                $blockItemNode = eZPageBlockItem::createFromXML( $node );
                $newObj->addItem( $blockItemNode );
            }
            elseif ( $node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'rotation' )
            {
                $attrValue = array();

                foreach ( $node->childNodes as $subNode )
                {
                    if ( $subNode->nodeType == XML_ELEMENT_NODE )
                        $attrValue[$subNode->nodeName] = $subNode->nodeValue;
                }

                $newObj->setAttribute( $node->nodeName, $attrValue );
            }
            elseif ( $node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'custom_attributes' )
            {
                $attrValue = array();

                foreach ( $node->childNodes as $subNode )
                {
                    if ( $subNode->nodeType == XML_ELEMENT_NODE )
                        $attrValue[$subNode->nodeName] = $subNode->nodeValue;
                }

                $newObj->setAttribute( $node->nodeName, $attrValue );
            }
            else
            {
                if ( $node->nodeType == XML_ELEMENT_NODE )
                    $newObj->setAttribute( $node->nodeName, $node->nodeValue );
            }
        }

        return $newObj;
    }

    /**
     * Remove eZPageBlockItem object by given $index
     *
     * @param integer $index
     */
    public function removeItem( $index )
    {
        unset( $this->attributes['items'][$index] );
    }

    /**
     * Return eZPageBlock name attribute
     *
     * @return string
     */
    public function getName()
    {
        return isset( $this->attributes['name'] ) ? $this->attributes['name'] : null;
    }

    /**
     * Return total item count
     *
     * @return integer
     */
    public function getItemCount()
    {
        return isset( $this->attributes['items'] ) ? count( $this->attributes['items'] ) : 0;
    }

    /**
     * Return eZPageBlockItem object by given $index
     *
     * @param integer $index
     * @return eZPageBlockItem
     */
    public function getItem( $index )
    {
        $item = null;

        if ( isset( $this->attributes['items'][$index] ) )
            $item = $this->attributes['items'][$index];

        return $item;
    }

    /**
     * Return attributes names
     *
     * @return array(string)
     */
    public function attributes()
    {
        return array_merge( array_keys( $this->attributes ), array_keys( $this->dynamicAttributeFunctions ) );
    }

    /**
     * Checks if attribute with given $name exists
     *
     * @param string $name
     * @return bool
     */
    public function hasAttribute( $name )
    {
        return in_array( $name, array_keys( $this->attributes ) ) || isset( $this->dynamicAttributeFunctions[$name] );
    }

    /**
     * Set attribute with given $name to $value
     *
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute( $name, $value )
    {
        if ( isset( $this->dynamicAttributeFunctions[$name] ) )
        {
            switch ( $name )
            {
                case 'valid_nodes':
                    $this->dynamicAttributeFunctions[$name] = $value;
                    break;
                default:
                break;
            }
        }
        else
        {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * Return value of attribute with given $name
     *
     * @return mixed
     * @param string $name
     */
    public function attribute( $name )
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
    }

    /**
     * Cleanup processed objects, removes action attribute
     * removes all items marked with "remove" action
     *
     * @return eZPageBlock
     */
    public function removeProcessed()
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

    /**
     * Fetches block from database by given $blockID
     *
     * @param string $blockID
     * @param bool $asObject
     * @return eZPageBlock
     */
    static public function fetch( $blockID, $asObject = true )
    {
        $db = eZDB::instance();
        $row = $db->arrayQuery(
            "SELECT zone_id, name, node_id, overflow_id, last_update, block_type as type, fetch_params, rotation_type, rotation_interval " .
            "FROM ezm_block WHERE id='" . $db->escapeString( $blockID ) . "'"
        );

        $xmlDocs = $db->arrayQuery(
            "SELECT oa.data_text " .
            "FROM ezcontentobject_tree AS t " .
            "INNER JOIN ezcontentobject AS o ON t.contentobject_id = o.id " .
            "INNER JOIN ezcontentobject_attribute AS oa ON o.id = oa.contentobject_id AND o.current_version = oa.version " .
            "WHERE t.node_id = " . $row[0]["node_id"] . " AND data_type_string = '" . eZPageType::DATA_TYPE_STRING . "'"
        );

        $pageBlock = null;

        foreach ( $xmlDocs as $xmlDoc )
        {
            $doc = new DOMDocument;
            $doc->loadXML( $xmlDoc["data_text"] );

            $xpath = new DOMXPath( $doc );
            foreach ( $xpath->evaluate( '//block[@id="id_' . $blockID . '"]' ) as $result )
            {
                $pageBlock = self::createFromXML( $result );

                // We are only interested by the first match (which is unique)
                break;
            }
        }

        if ( $pageBlock !== null )
        {
            if ( $asObject )
            {
                return $pageBlock;
            }

            $row[0] += array( "id" => $blockID, "view" => $pageBlock->attribute( "view" ) );
        }

        return $asObject ? new eZPageBlock( null, $row[0] ) : $row[0];
    }

    /**
     * Merges existing object items with those coming from database
     *
     * @param array $items
     * @param bool $mergeAdded
     * @return array(eZPageBlockItem)
     */
    protected function merge( $items, $mergeAdded = false )
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
                        //modify an item in history to be visible
                        //if item from db is hidden, visible=0 and the item from draft is not, delete it from db queue
                        //this is the case when requesting the history items and valid item
                        if( $item->attribute( 'ts_hidden' ) == 0 && $item->attribute( 'ts_visible' ) == 0 )
                        {
                            if( !$mergeAdded )
                            {
                                unset( $itemObjects[$oid] );
                            }
                        }
                        else
                        {
                            //this is the case when fetching valid items
                            $itemObjects[$oid]->setAttribute( 'ts_publication', $item->attribute( 'ts_publication' ) );
                            $itemObjects[$oid]->setAttribute( 'priority', $item->attribute( 'priority' ) );
                        }
                    }
                    else
                    {
                        //modify an item in history to be visible
                        //if the items from db don't have the item
                        //and modified item is not hidden, add it to the item list.
                        //this is the case when requesting the queue
                        if( $item->attribute( 'ts_hidden' ) == 0 && $item->attribute( 'ts_visible' ) == 0)
                        {
                            if( $mergeAdded )
                            {
                                $itemObjects[$oid] = $item;
                            }
                        }
                    }
                }
            }
        }

        return $itemObjects;
    }

    /**
     * Fetches waiting items and do merge with those already available
     *
     * @return array(eZPageBlockItem)
     */
    protected function getWaitingItems()
    {
        $waitingItems = eZFlowPool::waitingItems( $this->id() );
        $merged = $this->merge( $waitingItems, true );
        usort( $merged, array( $this, 'sortItems' ) );

        return $merged;
    }

    /**
     * Fetches valid items and do merge with those already available
     *
     * @return array(eZPageBlockItem)
     */
    protected function getValidItems()
    {
        $validItems = eZFlowPool::validItems( $this->id() );
        $merged = $this->merge( $validItems );
        usort( $merged, array( $this, 'sortItemsByPriority' ) );

        return $merged;
    }

    /**
     * Fetches valid items
     *
     * @return array(eZContentObjectTreeNode)
     */
    public function getValidItemsAsNodes()
    {
        $validItemsAsNodes = eZFlowPool::validNodes( $this->id() );
        return $validItemsAsNodes;
    }

    /**
     * Fetches archived items and do merge with those already available
     *
     * @return array(eZPageBlockItem)
     */
    protected function getArchivedItems()
    {
        $archivedItems = eZFlowPool::archivedItems( $this->id() );
        return $this->merge( $archivedItems );
    }
    
    /**
     * Fetch last valid item in valid list, if valid is empty, return null
     * in case of the same valid items, return the last one in order
     * @return eZPageBlockItem
     */
    protected function getLastValidItem()
    {
        $validItems = $this->getValidItems();
        $result = null;
        if( !empty( $validItems ) )
        {
            $result = null;
            $lastTime = 0;
            foreach($validItems as $item)
            {
                if( $item -> attribute( 'ts_visible' ) >= $lastTime)
                {
                    $lastTime = $item -> attribute( 'ts_visible' );
                    $result = $item;
                }
            }
        }
        return $result;
    }

    /**
     * Return view template string
     *
     * @return string
     */
    public function viewTemplate()
    {
        $template = 'view';
        return $template;
    }

    /**
     * Return edit template string
     *
     * @return string
     */
    public function editTemplate()
    {
        $template = 'edit';
        return $template;
    }

    /**
     * Checks if current block is to be removed
     *
     * @return bool
     */
    public function toBeRemoved()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'remove';
    }

    /**
     * Checks if current block is to be modified
     *
     * @return bool
     */
    public function toBeModified()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'modify';
    }

    /**
     * Checks if current block is to be added
     *
     * @return bool
     */
    public function toBeAdded()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'add';
    }

    /**
     * Sorting items based on the ts_publication and priority attributes
     *
     * @param eZPageBlockItem $a
     * @param eZPageBlockItem $b
     * @return integer
     */
    public function sortItems( eZPageBlockItem $a, eZPageBlockItem $b )
    {
        if ( $a->attribute('priority') == $b->attribute('priority') )
        {
            if ( $a->attribute('ts_publication') > $b->attribute('ts_publication') )
            {
                return 1;
            }
            else if ( $a->attribute('ts_publication') < $b->attribute('ts_publication') )
            {
                return -1;
            }
            else
            {
                return 0;
            }
        }
        else if ( $a->attribute('priority') > $b->attribute('priority') )
        {
            return -1;
        }
        else if ( $a->attribute('priority') < $b->attribute('priority') )
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Sorting items based on the priority attribute
     *
     * @param eZPageBlockItem $a
     * @param eZPageBlockItem $b
     * @return integer
     */
    public function sortItemsByPriority( eZPageBlockItem $a, eZPageBlockItem $b )
    {
        if ( $a->attribute('priority') > $b->attribute('priority') )
        {
            return -1;
        }
        else if ( $a->attribute('priority') < $b->attribute('priority') )
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Method executed when an object copy is created 
     * by using the clone keyword
     *
     */
    public function __clone()
    {
        $oldBlockID = $this->attributes['id'];

        $this->attributes['id'] = md5( (string)microtime() . (string)mt_rand() );
        $this->attributes['action'] = 'add';

        $oldItems = eZPersistentObject::fetchObjectList( eZFlowPoolItem::definition(), null, array( 'block_id' => $oldBlockID ) );

        foreach ( $oldItems as $oldItem )
        {
            $attrs = array(
                'object_id' => $oldItem->attribute( 'object_id' ),
                'node_id' => $oldItem->attribute( 'node_id' ),
                'priority' => $oldItem->attribute( 'priority' ),
                'ts_publication' => $oldItem->attribute( 'ts_publication' ),
                'action' => 'add'
            );
            $newPageBlockItem = new eZPageBlockItem( $attrs );
            $this->addItem( $newPageBlockItem );
        }
    }
}

?>