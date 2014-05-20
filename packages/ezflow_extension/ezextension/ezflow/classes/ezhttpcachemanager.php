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
 * eZHTTPCacheManager class implements methods required for loading
 * user definied http cache handlers
 * 
 */
class eZHTTPCacheManager
{
    /**
     * Constructor
     *  
     */
    public function __construct()
    {
    }
    
    /**
     * Return list of available handler class names
     * 
     * @static
     * @return array $handlerList
     */
    public static function getHandlerList()
    {
        $handlerList = array();
        $ini = eZINI::instance( 'http.ini' );
        if ( $ini->hasGroup( 'HTTPCacheHandlers' ) && $ini->hasVariable( 'HTTPCacheHandlers', 'Handlers' ) )
            $handlerList = $ini->variable( 'HTTPCacheHandlers', 'Handlers' );
        
        return $handlerList;
    }

    /**
     * Return list of available handler objects
     * 
     * @static
     * @return array $handlers
     */
    public static function getHandlers()
    {
        $handlers = array();
        $handlerList = self::getHandlerList();
        $cacheHash = md5( serialize( $handlerList ) );
        
        if ( isset( $GLOBALS['eZHTTPCacheManager_' . $cacheHash] ) )
            return $GLOBALS['eZHTTPCacheManager_' . $cacheHash];
        
        foreach ( $handlerList as $handler )
        {
            $object = new $handler;
            if ( $object instanceof eZHTTPCacheManagerInterface )
                $handlers[] = $object;
        }
        
        $GLOBALS['eZHTTPCacheManager_' . $cacheHash] = $handlers;
        
        return $handlers;
    }

    /**
     * Call user definied handler methods
     * 
     * @static
     * @param string $url
     */
    public static function execute( $url )
    {
        $handlers = self::getHandlers();
        
        foreach ( $handlers as $handler )
        {
            if ( $handler->isEnabled() )
                $handler->purgeURL( $url );
        }
    }
}

?>