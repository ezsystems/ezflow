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

// Do not add trailing slash '/'
$directory = ".";

// This key must match the key spesified in your flash recorder ez publish object.
// If they don't match no files will be returned. 

// IMPORTANT: Change the key (here and in your flash recorder object) from the 
// default key if you do not want people to be able to retreive the file list.
$key = "ThisIsTheDefaultKeyChangeMe";
$limit = 10;

if ( isset( $_GET["key"] ) === false || $_GET['key'] != $key )
    return;

$files = findFiles( $directory );

// Output the list one file pr line
$counter = 0;
foreach( $files as $file )
{
    if ( $counter < $limit )
    {
        echo $file . "\n";
    }
    else
    {
        break;
    }

    $counter++;
}

// Find all files in $directory with $fileExtension as their extension.
// This function will not look for files recursively. 

// Returns an array of filenames sorted by mtime (modified), newest first.
function findFiles( $directory, $fileExtension = '.flv' )
{
    $fileArray = array();
    foreach ( glob( "$directory/*$fileExtension" ) as $filename ) 
    {
        $stat = stat( $filename );
        $name = trim( $filename, "./" );
        $fileArray[$name] = $stat['mtime'];
    }        
    arsort( $fileArray );
    return array_keys( $fileArray );
}

?>
