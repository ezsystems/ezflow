<?php

class ezmOperations
{
    function updateBlockPoolByBlockID( $blockID, $publishedBeforeOrAt = false )
    {
        $db =& eZDB::instance();
        $blockINI =& eZINI::instance( 'block.ini' );

        if ( !$publishedBeforeOrAt )
        {
            $publishedBeforeOrAt = time();
        }
        
        $result = $db->arrayQuery( "SELECT * FROM ezm_block WHERE id='$blockID'" );
        
        if ( !$result )
        {
            eZDebug::writeWarning( "Block does not exist", "ezmOperations::updateBlockPoolByBlockID('$blockID')" );
            return false;
        }

        $result = $result[0];

        $blockType = $result['block_type'];

        if ( !$blockINI->hasVariable( $blockType, 'FetchClass' ) )
        {
            // Pure manual block, nothing is going to be fetched, but we need to update last_update as it is used for rotations
            $db->query( "UPDATE ezm_block SET last_update=$publishedBeforeOrAt WHERE id='$blockID'" );
            return 0;
        }

        $fetchClass = $blockINI->variable( $blockType, 'FetchClass' );
        @include_once( "extension/ezmedia/classes/fetches/$fetchClass.php" );
        $fetchInstance = new $fetchClass();
        // TODO: maybe check if it is of ezmFetchInterface type?
        if ( !is_subclass_of( $fetchInstance, 'ezmFetchInterface' ) )
        {
            eZDebug::writeWarning( "Can't create an instance of the $fetchClass class", "ezmOperations::updateBlockPoolByBlockID('$blockID')" );
            return false;
        }

        $fetchFixedParameters = array();
        if ( $blockINI->hasVariable( $blockType, 'FetchFixedParameters' ) )
        {
            $fetchFixedParameters = $blockINI->variable( $blockType, 'FetchFixedParameters' );
        }
        $fetchParameters = unserialize( $result['fetch_params'] );
        $parameters = array_merge( $fetchFixedParameters, $fetchParameters );

        $newItems = $fetchInstance->fetch( $parameters, $result['last_update'], $publishedBeforeOrAt );

        // Update pool
        $db->begin();

        foreach( $newItems as $item )
        {
            $objectID = $item['object_id'];
            $nodeID = $item['node_id'];
            $publicationTS = $item['ts_publication'];

            $duplicityCheck = $db->arrayQuery( "SELECT object_id
                                                FROM ezm_pool
                                                WHERE block_id='$blockID'
                                                  AND object_id=$objectID
                                                LIMIT 1" );
            if ( $duplicityCheck )
            {
                eZDebug::writeNotice( "Object $objectID is already available in the block $blockID.", 'ezmOperations' );
            }
            else
            {
                $db->query( "INSERT INTO ezm_pool(block_id,object_id,node_id,ts_publication) VALUES ('$blockID',$objectID,$nodeID,$publicationTS)" );
            }
        }

        $db->query( "UPDATE ezm_block SET last_update=$publishedBeforeOrAt WHERE id='$blockID'" );
        
        $db->commit();

        return count( $newItems );
    }

}

?>