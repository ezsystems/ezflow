<?php

class eZUnserialize
{
    /*!
      Constructor, does nothing by default.
    */
    function eZUnserialize()
    {
    }

    /*!

eturn an array with the template operator name.
    */
    function operatorList()
    {
        return array( 'unserialize' );
    }
    /*!
     \return true to tell the template engine that the parameter list exists per operator type,
             this is needed for operator classes that have multiple operators.
    */
    function namedParameterPerOperator()
    {
        return true;
    }    /*!
     See eZTemplateOperator::namedParameterList
    */
    function namedParameterList()
    {
        return array( 'unserialize' => array( 'params' => array( 'type' => 'string',
                                                                           'required' => true,
                                                                           'default' => '' ) ) );
    }
    /*!
     Executes the PHP function for the operator cleanup and modifies \a $operatorValue.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $params = $namedParameters['params'];

        switch ( $operatorName )
        {
            case 'unserialize':
            {
                if ( $params )
                    $operatorValue = unserialize( $params );
                else
                    $operatorValue = false;
            } break;
        }
    }
}

?>