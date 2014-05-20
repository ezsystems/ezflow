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

class eZFlowBlock extends eZPersistentObject
{
    /**
     * Constructor
     * 
     * @param array $row
     */
    function __construct( $row )
    {
        parent::__construct( $row );
    }

    /**
     * Return the definition for the object
     * 
     * @static
     * @return array
     */
    public static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'string',
                                                        'default' => '',
                                                        'required' => true ),
                                         'zone_id' => array( 'name' => 'ZoneID',
                                                             'datatype' => 'string',
                                                             'default' => '',
                                                             'required' => true ),
                                         'name' => array( 'name' => 'Name',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => false ),
                                         'node_id' => array( 'name' => 'NodeID',
                                                             'datatype' => 'integer',
                                                             'default' => '0',
                                                             'required' => true ),
                                         'overflow_id' => array( 'name' => 'OverflowID',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => false ),
                                         'last_update' => array( 'name' => 'LastUpdate',
                                                                 'datatype' => 'integer',
                                                                 'default' => '0',
                                                                 'required' => false ),
                                         'block_type' => array( 'name' => 'BlockType',
                                                                'datatype' => 'string',
                                                                'default' => '',
                                                                'required' => false ),
                                         'fetch_params' => array( 'name' => 'FetchParams',
                                                                  'datatype' => 'string',
                                                                  'default' => '',
                                                                  'required' => false ),
                                         'rotation_type' => array( 'name' => 'RotationType',
                                                                   'datatype' => 'integer',
                                                                   'default' => '0',
                                                                   'required' => false ),
                                         'rotation_interval' => array( 'name' => 'RotationInterval',
                                                                       'datatype' => 'integer',
                                                                       'default' => '0',
                                                                       'required' => false ),
                                         'is_removed' => array( 'name' => 'IsRemoved',
                                                                          'datatype' => 'integer',
                                                                          'default' => '0',
                                                                          'required' => false ) ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array( 'node' => 'node' ),
                      'class_name' => 'eZFlowBlock',
                      'sort' => array( 'id' => 'asc' ),
                      'name' => 'ezm_block' );
    }

    /**
     * Fetch block by ID
     * 
     * @param int $id
     * @return null|eZFlowBlock
     */
    static function fetch( $id )
    {
        $cond = array( 'id' => $id );
        $rs = eZPersistentObject::fetchObject( self::definition(), null, $cond );
        return $rs;
    }

    /**
     * Returns node associated with block
     *
     * @return eZContentObjectTreeNode
     */
    public function node()
    {
        return eZContentObjectTreeNode::fetch( $this->attribute( 'node_id' ) );
    }
}

?>
