{def $valid_node = $block.valid_nodes[0]}

<div class="block-type-video">

<div class="attribute-header">
    <h2>{$block.name}</h2>
</div>


{def $attribute_file=$valid_node.data_map.file
     $video=concat("content/download/", $attribute_file.contentobject_id, "/", $attribute_file.content.contentobject_attribute_id)|ezurl(no)}

    <div class="content-media" id="flash-{$block.zone_id}-{$block.id}">

    <script type="text/javascript">
        <!--
        var flash_id="flash-{$block.zone_id}-{$block.id}";
        
        var flashStart = '<object type="application/x-shockwave-flash" data={"flash/flash_player.swf"|ezdesign} width="269" height="213">';
        var flash = '<param name="movie" value={"flash/flash_player.swf"|ezdesign}  /> ';
        flash = flash + '<param name="scale" value="exactfit" /> ';
        flash = flash + '<param name="allowScriptAccess" value="sameDomain" />';
        flash = flash + '<param name="allowFullScreen" value="true" />';
        flash = flash + '<param name="flashvars" value="moviepath={$video}" />';
        flash = flash + '<param name="wmode" value="opaque" />';
        flash = flash + '<p>No <a href="http://www.macromedia.com/go/getflashplayer">Flash player<\/a> avaliable!<\/p>';
        var flashEnd = '<\/object>';
        
        insertMedia2( flash_id, flashStart + flash + flashEnd );
        //-->
    </script>
    <noscript>
    <object type="application/x-shockwave-flash" data="{'flash/flash_player.swf'|ezdesign(no)}" width="269" height="213">
        <param name="movie" value="{'flash/flash_player.swf'|ezdesign(no)}" />
        <param name="scale" value="exactfit" />
        <param name="allowScriptAccess" value="sameDomain" />
        <param name="allowFullScreen" value="true" />
        <param name="flashvars" value="moviepath={$video}" />
        <param name="wmode" value="opaque" />
        <p>No <a href="http://www.macromedia.com/go/getflashplayer">Flash player</a> avaliable!</p>
    </object>
</noscript>
    
    </div>
</div>