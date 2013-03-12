<?php

$Module = $Params['Module'];
$nodeID = $Params['NodeID'];

$node = eZContentObjectTreeNode::fetch( $nodeID );

if ( $node instanceof eZContentObjectTreeNode )
    $object = $node->object();
else
    $object = false;

if ( $Module->isCurrentAction( 'Store' ) && $Module->hasActionParameter( 'PlacementList' ) )
{
    $newItems = array();

    foreach ( $Module->actionParameter( 'PlacementList' ) as $zones )
    {
        foreach ( $zones as $blocks)
        {
            foreach ( $blocks as $blockID => $timestamp )
            {
                $newItems[] = array(
                    'blockID' => $blockID,
                    'objectID' => $object->attribute( 'id' ),
                    'nodeID' => $node->attribute( 'node_id' ),
                    'priority' => 0,
                    'timestamp' => $timestamp,
                );
            }
        }
    }

    if ( !empty( $newItems ) )
    {
        eZFlowPool::insertItems( $newItems );
    }

    $Module->redirectTo( $node->urlAlias() );
}

$tpl = eZTemplate::factory();

$tpl->setVariable( 'node', $node );

$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'ezflow/push', 'Push to block' ) ) );;
$Result['content'] = $tpl->fetch('design:page/push.tpl');

?>
