<?php
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

            $items = ezmPool::validItems( $blockID );
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