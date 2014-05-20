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

/**
 * eZRed5StreamListOperator class impelement red5list tpl operator methods
 * 
 */
class eZRed5StreamListOperator
{
    /**
     * Constructor
     * 
     */
    function __construct()
    {
    }

    /**
     * Return an array with the template operator name.
     * 
     * @return array
     */
    function operatorList()
    {
        return array( 'red5list' );
    }

    /**
     * Return true to tell the template engine that the parameter list exists per operator type,
     * this is needed for operator classes that have multiple operators.
     * 
     * @return bool
     */
    public function namedParameterPerOperator()
    {
        return true;
    }

    /**
     * Returns an array of named parameters, this allows for easier retrieval
     * of operator parameters. This also requires the function modify() has an extra
     * parameter called $namedParameters.
     * 
     * @return array
     */
    function namedParameterList()
    {

        return array( 'red5list' => array( 'fileserver' => array( 'type' => 'string',
                                             'required' => true,
                                             'default' => "" ),
                      'key' => array( 'type' => 'string',
                                      'required' => true,
                                      'default' => "" ) ) );
    }

    /**
     * Stitch together parts of an URL and avoid double slashes (/).
     * 
     * @param array $urlPartsArray
     * @return string
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

    /**
     * Executes the PHP function for the operator cleanup and modifies $operatorValue.
     * 
     * @param eZTemplate $tpl
     * @param string $operatorName
     * @param array $operatorParameters
     * @param string $rootNamespace
     * @param string $currentNamespace
     * @param mixed $operatorValue
     * @param array $namedParameters
     */
   function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $red5ListURL = $namedParameters['fileserver'];
        $key = $namedParameters['key'];
        $timeout = 8;
        
        if ( !$red5ListURL or !$key )
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
            
            $absoluteURL = $this->buildURL( array( $red5ListURL, $file ) );
            $returnArray[$name] = array( "filename" => $file, 
                                          "absoluteURL" => $absoluteURL );
        }
        $operatorValue = $returnArray;
    }
}

?>