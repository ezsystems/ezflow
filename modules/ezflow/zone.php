<?php

include_once( 'kernel/common/template.php' );

$module =& $Params["Module"];
$contentObjectAttributeID = $Params['ContentObjectAttributeID'];
$version = $Params['Version'];
$zoneID = $Params['ZoneID'];

$contentObjectAttribute = eZContentObjectAttribute::fetch( $contentObjectAttributeID, $version );
$page =& $contentObjectAttribute->content();
$zone =& $page->getZone( $zoneID );

$tpl =& templateInit();

$tpl->setVariable('zone_id', $zoneID );
$tpl->setVariable('zone', $zone );
$tpl->setVariable('attribute', $contentObjectAttribute );

echo $tpl->fetch( 'design:page/zone.tpl' );

eZExecution::cleanExit();

?>