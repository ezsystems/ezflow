<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.1.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2008 eZ Systems AS
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
include_once( 'kernel/common/template.php' );

$module = $Params['Module'];

$blockID = $Params['BlockID'];
$output = $Params['Output'];

$block = eZPageBlock::fetch( $blockID );

$tpl = templateInit();

$tpl->setVariable('block', $block );

if ( isset( $output ) )
{
    switch ( strtolower( $output ) )
    {
        case 'xhtml':
            echo $tpl->fetch( 'design:page/block.tpl' );
            break;
        case 'json':
            $output = '[ {';
            
            foreach ( $block->attributes() as $attr )
            {
                if ( in_array( $attr, array( 'waiting', 'valid', 'valid_nodes', 'archived' ) ) )
                    continue;

                $out .= '\'' . $attr . '\':\'' . $block->attribute( $attr ) . '\', ';
            }
            $out .= '\'html\':\'' . htmlentities( $tpl->fetch( 'design:page/preview.tpl' ), ENT_QUOTES ) . '\', ';
            $out .= '} ]';
                        
            $out = str_replace( "\n", "", $out );
            echo $out;
            break;
        case 'xml':
            $dom = new DOMDocument( '1.0', 'utf-8' );
            $dom->formatOutput = true;

            $items = eZFlowPool::validItems( $blockID );
            foreach( $items as $item )
            {
                $block->addItem( new eZPageBlockItem( $item, true ) );
            }

            $block->setAttribute( 'xhtml', $tpl->fetch( 'design:page/block.tpl' ) );

            $blockElement = $block->toXML( $dom );
            $dom->appendChild( $blockElement );
            echo $dom->saveXML();
            break;
        default:
            echo $tpl->fetch( 'design:page/block.tpl' );
            break;
    }

}
else
{
    echo $tpl->fetch( 'design:page/block.tpl' );
}

eZExecution::cleanExit();

?>