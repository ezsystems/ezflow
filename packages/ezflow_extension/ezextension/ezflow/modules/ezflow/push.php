<?php

include_once( 'kernel/common/template.php' );

$Module = $Params['Module'];
$nodeID = $Params['NodeID'];

$node = eZContentObjectTreeNode::fetch( $nodeID );

if ( $node instanceof eZContentObjectTreeNode )
    $object = $node->object();
else
    $object = false;

if ( $Module->isCurrentAction( 'Store' ) )
{
     $placementList = $Module->actionParameter( 'PlacementList' );
     $db = eZDB::instance();

     foreach( $placementList as $frontpageID => $zones )
     {
         foreach( $zones as $zoneID => $blocks)
         {
             foreach( $blocks as $blockID => $timestamp )
             {
                 $itemCount = $db->arrayQuery( "SELECT COUNT( * ) as count FROM ezm_pool
                                   WHERE block_id='" . $blockID ."'
                                      AND object_id='" . $object->attribute( 'id' ) . "'" );

                 if ( $itemCount[0]['count'] == 0 )
                 {
                     $db->query( "INSERT INTO ezm_pool ( block_id, object_id, node_id, priority, ts_publication )
                                        VALUES ( '" . $blockID . "',
                                                 '" . $object->attribute( 'id' )  . "',
                                                 '" . $node->attribute( 'node_id' ) . "',
                                                 '0',
                                                 '" . $timestamp . "'  )" );
                 }
             }
         }
     }

    $Module->redirectTo( $node->urlAlias() );
}
 
$tpl = templateInit();

$tpl->setVariable( 'node', $node );

$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'ezflow/push', 'Push to block' ) ) );;
$Result['content'] = $tpl->fetch('design:page/push.tpl');

?>