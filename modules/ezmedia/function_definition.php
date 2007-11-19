<?php

$FunctionList = array();

$FunctionList['waiting'] = array( 'name' => 'waiting',
                                  'operation_types' => array( 'read' ),
                                  'call_method' => array( 'include_file' => 'extension/ezflow/modules/ezflow/functioncollection.php',
                                                          'class' => 'ezmMediaFunctionCollection',
                                                          'method' => 'fetchWaiting' ),
                                  'parameter_type' => 'standard',
                                  'parameters' => array(
                                      array( 'name' => 'block_id',
                                             'type' => 'string',
                                             'required' => true ),
                                  ) );

$FunctionList['valid'] = array( 'name' => 'valid',
                                'operation_types' => array( 'read' ),
                                'call_method' => array( 'include_file' => 'extension/ezflow/modules/ezflow/functioncollection.php',
                                                        'class' => 'ezmMediaFunctionCollection',
                                                        'method' => 'fetchValid' ),
                                'parameter_type' => 'standard',
                                'parameters' => array(
                                    array( 'name' => 'block_id',
                                           'type' => 'string',
                                           'required' => true ),
                                ) );

$FunctionList['archived'] = array( 'name' => 'archived',
                                   'operation_types' => array( 'read' ),
                                   'call_method' => array( 'include_file' => 'extension/ezflow/modules/ezflow/functioncollection.php',
                                                           'class' => 'ezmMediaFunctionCollection',
                                                           'method' => 'fetchArchived' ),
                                   'parameter_type' => 'standard',
                                   'parameters' => array(
                                       array( 'name' => 'block_id',
                                              'type' => 'string',
                                              'required' => true ),
                                   ) );

$FunctionList['valid_nodes'] = array( 'name' => 'valid_nodes',
                                      'operation_types' => array( 'read' ),
                                      'call_method' => array( 'include_file' => 'extension/ezflow/modules/ezflow/functioncollection.php',
                                                              'class' => 'ezmMediaFunctionCollection',
                                                              'method' => 'fetchValidNodes' ),
                                      'parameter_type' => 'standard',
                                      'parameters' => array(
                                          array( 'name' => 'block_id',
                                                 'type' => 'string',
                                                 'required' => true ),
                                      ) );

$FunctionList['block'] = array( 'name' => 'block',
                                   'operation_types' => array( 'read' ),
                                   'call_method' => array( 'include_file' => 'extension/ezflow/modules/ezflow/functioncollection.php',
                                                           'class' => 'ezmMediaFunctionCollection',
                                                           'method' => 'fetchBlock' ),
                                   'parameter_type' => 'standard',
                                   'parameters' => array(
                                       array( 'name' => 'block_id',
                                              'type' => 'string',
                                              'required' => true ),
                                   ) );
?>