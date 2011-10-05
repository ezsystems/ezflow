{def $flash_node = $block.valid_nodes[0]
     $siteurl = concat( "http://", ezini( 'SiteSettings', 'SiteURL' ) )  
     $attribute_file = $flash_node.data_map.file
     $video = concat("content/download/", $attribute_file.contentobject_id, "/", $attribute_file.content.contentobject_attribute_id)|ezurl(no)
     $flash_var = concat( "moviepath=", $video )}

<div class="block-type-video">

<div class="attribute-header">
    <h2>{$block.name|wash()}</h2>
</div>

     {* Embed URL, which URL to retrieve the embed code from. *}
     {set $flash_var=$flash_var|append( "&amp;embedurl=", concat( $siteurl, "/flash/embed/", $valid_node.object.id ) )}

     {* Embed Link *}
     {set $flash_var=$flash_var|append( "&amp;embedlink=", concat( $siteurl, $valid_node.url_alias|ezurl(no) ) )}


    <div class="content-media" id="flash-{$block.zone_id}-{$block.id}">

    {attribute_view_gui attribute=$attribute_file}

    </div>
</div>

{undef}
