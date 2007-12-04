{def $attribute=$object.data_map.file
     $flash_file=concat( "/content/download/", $attribute.contentobject_id, "/", $attribute.content.contentobject_attribute_id )
     $screenshot=false()
     $size="large"
     $header=""
     $logo=""
     $commercial=""}

{switch match=$object.data_map.flash_player_type.content.0}
    {case match="0"}
        {set $size='large'}
    {/case}
    {case match="1"}
        {set $size='small'}
    {/case}
{/switch}

{if is_set( $object.data_map.poster_frame.content )}
    {set $screenshot=$object.data_map.poster_frame.content["original"].full_path}
{/if}

{if is_set( $object.data_map.header.content )}
    {set $header=$object.data_map.header.content}
{/if}

{if is_set( $object.data_map.logo.content )}
    {set $logo=$object.data_map.logo.content['small'].full_path}
{/if}

{* Both .content and .has_content returns true so we check if there is a filename defined. *}
{if $object.data_map.commercial.content.filename|ne( "" )}
    {set $commercial=concat( "/content/download/", $object.data_map.commercial.contentobject_id, "/", $object.data_map.commercial.content.contentobject_attribute_id )}
{/if}

{include uri="design:parts/flash_player.tpl" 
         movie=$flash_file 
         screenshot=$screenshot 
         size=$size 
         header=$header
         logo=$logo
         commercial=$commercial
         object_id=$object.id
         url_alias=$object.main_node.url_alias
         generate_shareable_code=true()
         raw_html_sharable_code=true()}