<?php
//
// Created on: <30-Jul-2007 00:00:00 ar>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZajax
// SOFTWARE RELEASE: 0.x.x
// COPYRIGHT NOTICE: Copyright (C) 2008 eZ Systems AS
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
// ## BEGIN ABOUT TEXT, SPECS AND DESCRIPTION  ##
//
// MODULE:		ezajax
// VIEW:		SEARCH
// PARAMS:		'SearchStr', 'SearchOffset', 'SearchLimit', 'VarName'
// POST:		SearchStr, VarName, SearchOffset, SearchLimit, SearchContentClassAttributeID
//              SearchSubTreeArray, SearchSectionID, SearchTimestamp, SearchDate
// OUTPUT:		xhtml
// TEMPLATE(S):	standard/templates/jaxx/search.tpl
// DESCRIPTION: Script for getting list of search result to Sidebar Search
//				Fetches 6 nodes, but only displays 5 of them. 
//				The spare node is to determin if there are more nodes.
//
// ## END ABOUT TEXT, SPECS AND DESCRIPTION  ##
//


include_once( 'kernel/classes/ezsearch.php' );
include_once( 'extension/ezflow/classes/ezflowajaxcontent.php' );

$http = eZHTTPTool::instance();
$debug = false;

if ( $http->hasPostVariable( 'SearchStr' ) )
{
    $searchStr = trim( $http->postVariable( 'SearchStr' ) );
}
elseif ( isSet( $Params['SearchStr'] ) )
{
    $searchStr = trim( $Params['SearchStr'] );
}

$varName = '';
if ( $http->hasPostVariable( 'VarName' ))
{
    $varName = trim( $http->postVariable( 'VarName' ) );
}
elseif ( isSet( $Params['VarName'] ) )
{
    $varName = trim( $Params['VarName'] );
}

if ( $varName )
{
    $varName .= ' = ';
}


$searchOffset = 0;
if ( $http->hasPostVariable( 'SearchOffset' ))
{
    $searchOffset = (int) $http->postVariable( 'SearchOffset' );
}
elseif ( isSet( $Params['SearchOffset'] ) )
{
    $searchOffset = (int) $Params['SearchOffset'];
}

$searchLimit = 10;
if ( $http->hasPostVariable( 'SearchLimit' ))
{
    $searchLimit = (int) $http->postVariable( 'SearchLimit' );
}
elseif ( isSet( $Params['SearchLimit'] ) )
{
    $searchLimit = (int) $Params['SearchLimit'];
}

if ( $searchLimit > 30 ) $searchLimit = 30;


//Preper the search params
$param = array( 'SearchOffset' => $searchOffset,
                'SearchLimit' => $searchLimit+1,
                'SortArray' => array('published', 0)
              );


// if no checkbox select class_attr first if valid
if ( $http->hasPostVariable( 'SearchContentClassAttributeID' ) && $http->postVariable( 'SearchContentClassAttributeID' ) )
{
    $param['SearchContentClassAttributeID'] = explode( ',', $http->postVariable( 'SearchContentClassAttributeID' ) );
}
elseif ( $http->hasPostVariable( 'SearchContentClassID' ) && $http->postVariable( 'SearchContentClassID' ) )
{
    $param['SearchContentClassID'] = explode( ',', $http->postVariable( 'SearchContentClassID' ) );
}
              
if ( $http->hasPostVariable( 'SearchSubTreeArray' ) && $http->postVariable( 'SearchSubTreeArray' ) )
{
    $param['SearchSubTreeArray'] = explode( ',', $http->postVariable( 'SearchSubTreeArray' ) );
}

if ( $http->hasPostVariable( 'SearchSectionID' ) && $http->postVariable( 'SearchSectionID' ) )
{
    $param['SearchSectionID'] = explode( ',', $http->postVariable( 'SearchSectionID' ) );
}

if ( $http->hasPostVariable( 'SearchDate' ) && $http->postVariable( 'SearchDate' ) )
{
    $param['SearchDate'] = (int) $http->postVariable( 'SearchDate' );
}    
else if ( $http->hasPostVariable( 'SearchTimestamp' ) && $http->postVariable( 'SearchTimestamp' ) )
{
    $param['SearchTimestamp'] = explode( ',', $http->postVariable( 'SearchTimestamp' ) );
	if ( isSet( $param['SearchTimestamp'][0] ) && !isSet( $param['SearchTimestamp'][1] ) )
	    $param['SearchTimestamp'] = $param['SearchTimestamp'][0];
}
//$debug = var_export($param['SearchTimestamp'], true);

$searchList = eZSearch::search( $searchStr, $param );

$r = '[]';
if ($searchList  && count($searchList["SearchResult"]) > 0)
{
	$r = eZFlowAjaxContent::nodeEncode( $searchList["SearchResult"] );
}

echo $varName . '{SearchResult:' . $r . ",\nSearchCount:" . $searchList['SearchCount'] .
     ",\nSearchOffset:" . $searchOffset . ",\nSearchLimit:" . $searchLimit;
     
if (  $debug ) echo ",\ndebug:'" . $debug . "'";
echo '};';

eZExecution::cleanExit();

?>