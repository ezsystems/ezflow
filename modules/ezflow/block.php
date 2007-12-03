<?php
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.0.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2007 eZ Systems AS
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

$module =& $Params['Module'];

$blockID = $Params['BlockID'];
$output = $Params['Output'];

$block = eZPageBlock::fetch( $blockID );

$tpl =& templateInit();

$tpl->setVariable('block', $block );

if ( isset( $output ) )
{
    switch ( strtolower( $output ) )
    {
        case 'xhtml':
            echo $tpl->fetch( 'design:page/block.tpl' );
            break;
        case 'xml':
            $dom = domxml_new_doc( '1.0' );
            $dom->formatOutput = true;

            $items = eZFlowPool::validItems( $blockID );
            foreach( $items as $item )
            {
                $block->addItem( new eZPageBlockItem( $item, true ) );
            }
            
            $blockElement = $block->toXML( $dom );
            $dom->append_child( $blockElement );
            echo $dom->dump_mem( true );
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