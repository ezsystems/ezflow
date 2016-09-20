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

$module = $Params["Module"];
$contentObjectAttributeID = $Params['ContentObjectAttributeID'];
$version = $Params['Version'];
$zoneID = $Params['ZoneID'];

$contentObjectAttribute = eZContentObjectAttribute::fetch( $contentObjectAttributeID, $version );
if ( !$contentObjectAttribute )
{
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$page = $contentObjectAttribute->content();
$zone = $page->getZone( $zoneID );

$tpl = eZTemplate::factory();

$tpl->setVariable('zone_id', $zoneID );
$tpl->setVariable('zone', $zone );
$tpl->setVariable('attribute', $contentObjectAttribute );

echo $tpl->fetch( 'design:page/zone.tpl' );

eZExecution::cleanExit();

?>
