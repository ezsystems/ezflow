<?php

/**
 * Interface declaration 
 * 
 */
interface eZHTTPCacheManagerInterface
{
    /**
     * Purges the given URL on the server. The full URL is passed.
     * 
     * @static
     * @param string $url
     */
    public static function purgeURL( $url );

    /**
     * Return true or false whenever the HTTP cache handler is enabled or disabled
     * 
     * @static 
     * @return bool
     */ 
    public static function isEnabled();
}

?>