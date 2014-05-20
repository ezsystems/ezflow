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

$Module = array( 'name' => 'eZ Flow',
                 'functions' => array( 'changelayout' ) );

$ViewList = array();
$ViewList['get'] = array( 'script' => 'get.php',
                          'functions' => array( 'edit' ) );

$ViewList['timeline'] = array( 'script' => 'timeline.php',
                               'functions' => array( 'timeline' ),
                               'params' => array( 'NodeID', 'LanguageCode' ) );

$ViewList['preview'] = array( 'script' => 'preview.php',
                              'functions' => array( 'timeline' ),
                              'params' => array( 'Time', 'NodeID' ) );

$ViewList['zone'] = array( 'script' => 'zone.php',
                           'functions' => array( 'edit' ),
                           'params' => array( 'ContentObjectAttributeID', 'Version', 'ZoneID' ) );

$ViewList['request'] = array( 'script' => 'request.php',
                              'functions' => array( 'edit' ),
                              'unordered_params' => array( 'items' => 'Items',
                                                         'block' => 'Block' ) );

$ViewList['push'] = array( 'script' => 'push.php',
                           'functions' => array( 'edit' ),
                           'params' => array( 'NodeID' ),
                           'single_post_actions' => array( 'PlacementStoreButton' => 'Store' ),
                           'post_action_parameters' => array( 'Store' => array( 'PlacementList' => 'PlacementTSArray' ) ) );

$ViewList['block'] = array( 'script' => 'block.php',
                            'functions' => array( 'call' ),
                            'params' => array( 'BlockID', 'Output' ) );

$FunctionList = array();
$FunctionList['timeline'] = array();
$FunctionList['edit'] = array();
$FunctionList['call'] = array();
$FunctionList['changelayout'] = array( 'Class' => array( 'name'=> 'Class',
                                                                  'values'=> array(),
                                                                  'path' => 'classes/',
                                                                  'file' => 'ezcontentclass.php',
                                                                  'class' => 'eZContentClass',
                                                                  'function' => 'fetchList',
                                                                  'parameter' => array( 0, false, false, array( 'name' => 'asc' ) ) ) );
?>