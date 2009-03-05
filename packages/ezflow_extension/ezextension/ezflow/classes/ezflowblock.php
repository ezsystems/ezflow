<?php

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
                      'class_name' => 'eZFlowBlock',
                      'sort' => array( 'id' => 'asc' ),
                      'name' => 'ezm_block' );
    }
}

?>
