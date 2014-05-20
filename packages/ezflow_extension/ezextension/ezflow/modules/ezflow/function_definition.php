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

$FunctionList = array();

$FunctionList['waiting'] = array( 'name' => 'waiting',
                                  'operation_types' => array( 'read' ),
                                  'call_method' => array( 'include_file' => 'extension/ezflow/modules/ezflow/functioncollection.php',
                                                          'class' => 'eZFlowFunctionCollection',
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
                                                        'class' => 'eZFlowFunctionCollection',
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
                                                           'class' => 'eZFlowFunctionCollection',
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
                                                              'class' => 'eZFlowFunctionCollection',
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
                                                           'class' => 'eZFlowFunctionCollection',
                                                           'method' => 'fetchBlock' ),
                                   'parameter_type' => 'standard',
                                   'parameters' => array(
                                       array( 'name' => 'block_id',
                                              'type' => 'string',
                                              'required' => true ),
                                   ) );

$FunctionList['allowed_zones'] = array( 'name' => 'allowed_zones',
                                      'operation_types' => array( 'read' ),
                                      'call_method' => array( 'include_file' => 'extension/ezflow/modules/ezflow/functioncollection.php',
                                                              'class' => 'eZFlowFunctionCollection',
                                                              'method' => 'fetchAllowedZones' ),
                                      'parameter_type' => 'standard',
                                      'parameters' => array() );
?>