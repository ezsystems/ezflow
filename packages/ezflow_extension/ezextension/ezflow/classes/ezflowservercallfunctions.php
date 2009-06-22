<?php

/**
 * Implements methods called remotely by sending XHR calls
 * 
 */
class eZFlowServerCallFunctions
{
    /**
     * Returns statistics about users which are currently online 
     * 
     * @param mixed $args
     * @return array
     */
    public static function onlineUsers( $args )
    {
        $result = array();
        
        $result['logged_in_count'] = eZFunctionHandler::execute( 'user', 'logged_in_count', array() );
        $result['anonymous_count'] = eZFunctionHandler::execute( 'user', 'anonymous_count', array() );

        return $result;
    }
}

?>