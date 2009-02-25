<?php

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