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

$Module = $Params["Module"];

if ( isset( $Params['NodeID'] ) )
    $nodeID = $Params['NodeID'];

if ( !$nodeID )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

if ( isset( $Params['LanguageCode'] ) )
{
    $languageCode = $Params['LanguageCode'];
}
else
{
    $locale = eZLocale::instance();
    $languageCode = $locale->localeCode();
}


$node = eZContentObjectTreeNode::fetch( $nodeID, $languageCode );

if ( !$node )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

$tpl = eZTemplate::factory();
$ini = eZINI::instance();

$contentObject = $node->attribute( 'object' );

$nodeResult = eZNodeviewfunctions::generateNodeViewData( $tpl, $node, $contentObject, $languageCode, 'full', 0 );

// Generate a unique cache key for use in cache-blocks in pagelayout.tpl.
// This should be looked as a temporary fix as ideally all cache-blocks
// should be disabled by this view.
$cacheKey = "timeline-" + time();
$nodeResult["title_path"] = array( array( "text" => "Timeline Preview" ), array( "text" => $node->attribute( 'name' ) ) );

$httpCharset = eZTextCodec::httpCharset();
$locale = eZLocale::instance();
$languageCode = $locale->httpLocaleCode();

$nodeResult['content_info']['persistent_variable']['extra_template_list'] = array( 'timeline.tpl' );
$nodeResult['content_info']['persistent_variable']['pagestyle_css_classes'] = array( 'yui-skin-sam', 'yui-skin-ezflow' );

$site = array( 'title' => $ini->variable( 'SiteSettings', 'SiteName' ),
               'design' => $ini->variable( 'DesignSettings', 'SiteDesign' ),
               'uri' => eZURI::instance( eZSys::requestURI() ),
               'http_equiv' => array( 'Content-Type' => 'text/html; charset=' . $httpCharset,
                                      'Content-language' => $languageCode ) );

$currentUser = eZUser::currentUser();
$tpl->setVariable( "current_user", $currentUser );
$tpl->setVariable( 'ui_context', "" );

$uri = eZURI::instance( eZSys::requestURI() );
$GLOBALS['eZRequestedURI'] = $uri;

$access = eZSiteAccess::match( $uri,
                      eZSys::hostname(),
                      eZSys::serverPort(),
                      eZSys::indexFile() );

$tpl->setVariable( 'access_type', $access );
$tpl->setVariable( 'uri_string', $uri->uriString() );

$tpl->setVariable( "site", $site );
$tpl->setVariable( "extra_cache_key", $cacheKey );
$tpl->setVariable( "module_result", $nodeResult );
$tpl->setVariable( "node", $node );
$tpl->setVariable( "display_timeline_sider", true );

$pagelayoutResult = $tpl->fetch( 'design:pagelayout.tpl' );


eZDisplayResult( $pagelayoutResult );

// Stop execution at this point, if we do not we'll have the
// pagelayout.tpl inside another pagelayout.tpl.
eZExecution::cleanExit();

?>
