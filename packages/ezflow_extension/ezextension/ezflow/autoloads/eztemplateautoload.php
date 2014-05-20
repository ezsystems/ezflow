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

$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/ezflow/autoloads/ezunserialize.php',
                                    'class' => 'eZUnserialize',
                                    'operator_names' => array( 'unserialize' ) );
$eZTemplateOperatorArray[] = array( 'script' => 'extension/ezflow/autoloads/ezjson.php',
                                    'class' => 'eZJSON',
                                    'operator_names' => array( 'json' ) );
$eZTemplateOperatorArray[] = array( 'script' => 'extension/ezflow/autoloads/ezfeedreader.php',
                                    'class' => 'eZFeedReader',
                                    'operator_names' => array( 'feedreader' ) );
$eZTemplateOperatorArray[] = array( 'script' => 'extension/ezflow/autoloads/ezred5streamlist.php',
                                    'class' => 'eZRed5StreamListOperator',
                                    'operator_names' => array( 'red5list' ) );
$eZTemplateOperatorArray[] = array( 'script' => 'extension/ezflow/autoloads/ezpagelink.php',
                                    'class' => 'eZPageLink',
                                    'operator_names' => array( 'pagelink' ) );

$eZTemplateFunctionArray = array();
$eZTemplateFunctionArray[] = array( 'function' => 'eZPageForwardInit',
                                    'function_names' => array( 'block_edit_gui',
                                                               'block_view_gui') );


if ( !function_exists( 'eZPageForwardInit' ) )
{
    function eZPageForwardInit()
    {
        $forward_rules = array(
                'block_edit_gui' => array( 'template_root' => 'block/edit',
                                           'input_name' => 'block',
                                           'output_name' => 'block',
                                           'namespace' => 'ContentAttributeBlockEdit',
                                           'attribute_keys' => array( 'type' => array( 'type' ) ),
                                           'attribute_access' => array( array( 'edit_template' ) ),
                                           'optional_views' => true,
                                           'use_views' => 'view' ),
                'block_view_gui' => array( 'template_root' => 'block/view',
                                           'render_mode' => false,
                                           'input_name' => 'block',
                                           'output_name' => 'block',
                                           'namespace' => 'ContentAttributeBlockView',
                                           'attribute_keys' => array( 'type' => array( 'type' ),
                                                                      'view' => array( 'view' ) ),
                                           'attribute_access' => array( array( 'view_template' ) ),
                                           'optional_views' => true,
                                           'use_views' => 'view' ) );

        return new eZObjectForwarder( $forward_rules );
    }
}

?>
