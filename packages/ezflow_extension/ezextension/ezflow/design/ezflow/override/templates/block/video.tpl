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

    <script type="text/javascript">
        var flash_id="flash-{$block.zone_id}-{$block.id}";
        
        var flashStart = '<object type="application/x-shockwave-flash" data={"flash/flash_player.swf"|ezdesign} width="266" height="211">';
        var flash = '<param name="movie" value={"flash/flash_player.swf"|ezdesign}  /> ';
        flash = flash + '<param name="scale" value="exactfit" /> ';
        flash = flash + '<param name="allowScriptAccess" value="sameDomain" />';
        flash = flash + '<param name="allowFullScreen" value="true" />';
        flash = flash + '<param name="flashvars" value="{$flash_var}" />';
        flash = flash + '<param name="wmode" value="opaque" />';
        flash = flash + '<p>No <a href="http://www.macromedia.com/go/getflashplayer">Flash player<\/a> avaliable!<\/p>';
        var flashEnd = '<\/object>';
        
        insertMedia2( flash_id, flashStart + flash + flashEnd );
    </script>
    <noscript>
    <object type="application/x-shockwave-flash" data="{'flash/flash_player.swf'|ezdesign(no)}" width="266" height="211">
        <param name="movie" value="{'flash/flash_player.swf'|ezdesign(no)}" />
        <param name="scale" value="exactfit" />
        <param name="allowScriptAccess" value="sameDomain" />
        <param name="allowFullScreen" value="true" />
        <param name="flashvars" value="{$flash_var}" />
        <param name="wmode" value="opaque" />
        <p>No <a href="http://www.macromedia.com/go/getflashplayer">Flash player</a> avaliable!</p>
    </object>
</noscript>
    
    </div>
</div>

{undef}