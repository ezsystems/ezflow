<?php
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.0.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2007 eZ Systems AS
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

class eZRed5StreamListOperator
{
    /*!
        Return an array with the template operator name.
    */
    function operatorList()
    {
        return array( 'red5list' );
    }

    /*!
        See eZTemplateOperator::namedParameterList
    */
    function namedParameterList()
    {
        return array( 'fileserver' => array( 'type' => 'string',
                                             'required' => true,
                                             'default' => "" ),
                      'key' => array( 'type' => 'string',
                                      'required' => true,
                                      'default' => "" ) );
    }

    /*!
        Stitch together parts of an URL and avoid double slashes (/).
    */
    function buildURL( $urlPartsArray )
    {
        $url = "";
        foreach( $urlPartsArray as $urlPart )
        {
            if ( $url == "" )
            {
                $url = $urlPart;
                continue;
            }
            
            // Detect trailing /
            if ( ( strrpos( $url, "/" ) + 1 ) == strlen( $url ) )
            {
                $url .= $urlPart;
            }
            else
            {
                $url .= "/" . $urlPart;
            }   
        }
        return $url;        
    }
    
    /*!
        Executes the PHP function for the operator cleanup and modifies \a $operatorValue.
    */
    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        $red5ListURL = $namedParameters['fileserver'];
        $key = $namedParameters['key'];
        $timeout = 8;
        
        // Remove last element (the filename) from the URL.
        $red5BaseURL = substr( $red5ListURL, 0, strrpos( $red5ListURL, "/" ) );
        
        if ( !$red5ListURL or !$key or !$red5ListURL )
            return "";

        $remoteHandle = fopen( $this->buildURL( array( $red5ListURL, "flash_video_list.php", '?key=' . $key ) ), "r" );
        if ( !$remoteHandle )
            return "";

        // Make sure our attempt to contact the remote sever doesn't go on forever.
        // A sensible timeout is about 5-20 seconds.            
        stream_set_timeout( $remoteHandle, $timeout );
        
        $content = "";    
        $info = stream_get_meta_data( $remoteHandle );
    
        while ( !feof( $remoteHandle ) and !$info['timed_out'] ) 
        { 
            $content .= fgets( $remoteHandle, 4096 ); 
            $info = stream_get_meta_data( $remoteHandle ); 
        }

        // Build return array consisting of the filname without extension
        // as key and path to file as value.
        $returnArray = array();
        $fileArray = explode( "\n", $content );
        
        foreach( $fileArray as $file )
        {
            $file = trim( $file );
            if ( $file == "" )
                continue;

            // We could have used basename() here, however since we don't know 
            // the file extension we just strip away everything from the last dot to the end.
            $dotPositon = strrpos( $file, "." );
            $name = substr( $file, 0, $dotPositon );
            
            $absoluteURL = $this->buildURL( array( $red5BaseURL, $file ) );
            $returnArray[$name] = array( "filename" => $file, 
                                          "absoluteURL" => $absoluteURL );
        }
        $operatorValue = $returnArray;
    }
}

?>