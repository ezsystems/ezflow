<?php

include_once( 'kernel/common/template.php' );
//include_once( 'kernel/classes/eznodeviewfunctions.php' );
//include_once( 'kernel/classes/ezcontentobject.php' );
//include_once( 'kernel/classes/ezcontentobjecttreenode.php' );


$Module = $Params["Module"];
if ( isset( $Params['NodeID'] ) )
    $nodeID = $Params['NodeID'];

if ( !$nodeID )
    return $Module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
    
if ( isset( $Params['LanguageCode'] ) )
    $languageCode = $Params['LanguageCode'];
else
    $languageCode = 'eng-GB';

$node = eZContentObjectTreeNode::fetch( $nodeID, $languageCode );

if ( !$node )
    return $Module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );

$tpl = templateInit();
$ini = eZINI::instance();

$contentObject = $node->attribute( 'object' );

$nodeResult = eZNodeviewfunctions::generateNodeView( $tpl, $node, $contentObject, $languageCode, 'full', 0,
                                                      false, false, false );

// Generate a unique cache key for use in cache-blocks in pagelayout.tpl.
// This should be looked as a temporary fix as ideally all cache-blocks 
// should be disabled by this view.
$cacheKey = "timeline-" + time();
$nodeResult["title_path"] = array( array( "text" => "Timeline Preview" ), array( "text" => $node->attribute( 'name' ) ) );
$site = array( 'title' => $ini->variable( 'SiteSettings', 'SiteName' ) );

$tpl->setVariable( "site", $site );
$tpl->setVariable( "timeline_cache_key", $cacheKey );
$tpl->setVariable( "module_result", $nodeResult );
$tpl->setVariable( "node", $node );
$tpl->setVariable( "display_timeline_sider", true );

$pagelayoutResult = $tpl->fetch( 'design:pagelayout.tpl' );


eZDisplayResult( $pagelayoutResult );

// Stop execution at this point, if we do not we'll have the 
// pagelayout.tpl inside another pagelayout.tpl.
eZExecution::cleanExit();

?>