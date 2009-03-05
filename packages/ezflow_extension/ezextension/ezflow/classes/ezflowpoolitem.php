<?php

class eZFlowPoolItem extends eZPersistentObject
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
        return array( 'fields' => array( 'block_id' => array( 'name' => 'BlockID',
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => true,
                                                              'foreign_class' => 'eZFlowBlock',
                                                              'foreign_attribute' => 'id',
                                                              'multiplicity' => '1..*' ),
                                         'object_id' => array( 'name' => 'ObjectID',
                                                              'datatype' => 'integer',
                                                              'default' => '0',
                                                              'required' => true ),
                                         'node_id' => array( 'name' => 'NodeID',
                                                             'datatype' => 'integer',
                                                             'default' => '0',
                                                             'required' => true ),
                                         'priority' => array( 'name' => 'Priority',
                                                              'datatype' => 'integer',
                                                              'default' => '0',
                                                              'required' => false ),
                                         'ts_publication' => array( 'name' => 'TSPublication',
                                                                    'datatype' => 'integer',
                                                                    'default' => '0',
                                                                    'required' => false ),
                                         'ts_visible' => array( 'name' => 'TSVisible',
                                                                'datatype' => 'integer',
                                                                'default' => '0',
                                                                'required' => false ),
                                         'ts_hidden' => array( 'name' => 'TSHidden',
                                                               'datatype' => 'integer',
                                                               'default' => '0',
                                                               'required' => false ),
                                         'rotation_until' => array( 'name' => 'RotationUntil',
                                                                    'datatype' => 'integer',
                                                                    'default' => '0',
                                                                    'required' => false ),
                                         'moved_to' => array( 'name' => 'BlockID',
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => false,
                                                              'foreign_class' => 'eZFlowBlock',
                                                              'foreign_attribute' => 'id',
                                                              'multiplicity' => '1..*' ) ),
                      'keys' => array( 'block_id', 'object_id' ),
                      'class_name' => 'eZFlowPoolItem',
                      'sort' => array( 'block_id' => 'asc' ),
                      'name' => 'ezm_pool' );
    }

}

?>
