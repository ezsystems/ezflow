<div id="address-{$block.zone_id}-{$block.id}">
{def $valid_items = $block.valid_nodes
     $block_name = ''}
{if is_set( $block.name )}
    {set $block_name = $block.name}
{else}
    {set $block_name = ezini( $block.type, 'Name', 'block.ini' )}
{/if}
<h2 class="grey_background">{$block_name}</h2>
{def  $attribute_file=$valid_items[0].data_map.file
     $video=concat("content/download/",$attribute_file.contentobject_id,"/",$attribute_file.content.contentobject_attribute_id,"/",$attribute_file.content.original_filename)|ezurl(no)}

    <div class="content-media" id="flash-{$block.zone_id}-{$block.id}">
    <script type="text/javascript">
        <!--
        var flash_id="flash-{$block.zone_id}-{$block.id}";
        
        var flash = '<param name="movie" value={"flash/flash_player.swf"|ezdesign}  /> ';
        flash = flash + '<param name="scale" value="exactfit" /> ';
        flash = flash + '<param name="allowScriptAccess" value="sameDomain" />';
        flash = flash + '<param name="allowFullScreen" value="true" />';
        flash = flash + '<param name="flashvars" value="moviepath={$video}" />';
        flash = flash + '<param name="wmode" value="opaque" />';
        flash = flash + '<p>No <a href="http://www.macromedia.com/go/getflashplayer">Flash player<\/a> avaliable!<\/p>';
        
        var flashNode = document.getElementById(flash_id);
        var object = document.createElement('object');
        object.type = 'application/x-shockwave-flash';
        object.data = {"flash/flash_player.swf"|ezdesign};
        object.width = 297;
        object.height = 235;
        object.innerHTML = flash;
        flashNode.appendChild(object);
        //-->
    </script>

    <noscript>
    <object type="application/x-shockwave-flash" data="{'flash/flash_player.swf'|ezdesign(no)}" width="297" height="235">
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
<br />
</div>