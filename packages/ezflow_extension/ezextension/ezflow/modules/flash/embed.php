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

$http = eZHTTPTool::instance();

if ( $Params['ObjectID'] )
    $objectID = $Params['ObjectID'];

if ( $http->hasPostVariable( "ObjectID" ) )
    $objectID = $http->postVariable( "ObjectID" );

if ( is_numeric( $objectID ) )
{
    $object = eZContentObject::fetch( $objectID );
    if ( $object )
    {
        $tpl = eZTemplate::factory();
        $template = "design:parts/flash_player_embed_code.tpl";
        $tpl->setVariable( 'object', $object );
        $body = $tpl->fetch( $template );
        print( "&result=" . urlencode( trim( $body ) ) );
    }
}

eZExecution::cleanup();
eZExecution::setCleanExit();
exit;

?>
