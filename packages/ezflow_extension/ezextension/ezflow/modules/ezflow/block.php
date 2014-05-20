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

include_once( 'extension/ezflow/classes/ezpageblock.php' );
include_once( 'extension/ezflow/classes/ezpageblockitem.php' );

$module = $Params['Module'];

$blockID = $Params['BlockID'];
$output = $Params['Output'];

$block = eZPageBlock::fetch( $blockID );

$tpl = eZTemplate::factory();

$tpl->setVariable('block', $block );

$template = 'design:page/block.tpl';

if ( !isset( $output ) )
    $output = 'xhtml';

switch ( strtolower( $output ) )
{
    case 'json':
        $template = 'design:page/preview.tpl';
        $obj = new stdClass;

        foreach ( $block->attributes() as $attr )
        {
            if ( !in_array( $attr, array( 'waiting', 'valid', 'valid_nodes', 'archived' ) ) )
                $obj->$attr = $block->attribute($attr);
        }
        $obj->html = htmlentities($tpl->fetch($template), ENT_QUOTES);

        header('Content-type: application/json');
        echo json_encode(array('block' => $obj));
        break;
    case 'xml':
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->formatOutput = true;

        $items = eZFlowPool::validItems( $blockID );
        foreach( $items as $item )
        {
            $block->addItem( new eZPageBlockItem( $item, true ) );
        }

        $block->setAttribute( 'xhtml', $tpl->fetch( $template ) );

        $blockElement = $block->toXML( $dom );
        $dom->appendChild( $blockElement );
        echo $dom->saveXML();
        break;
    case 'xhtml':
    default:
        echo $tpl->fetch($template);
        break;
}

eZExecution::cleanExit();

?>
