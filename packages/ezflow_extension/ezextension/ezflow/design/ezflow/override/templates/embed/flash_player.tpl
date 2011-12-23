{def $siteurl=concat( "http://", ezini( 'SiteSettings', 'SiteURL' ) ) 
     $attribute_file=$object.data_map.file
     $video=concat( "content/download/",$attribute_file.contentobject_id,"/", $attribute_file.content.contentobject_attribute_id )|ezurl(no)
     $flash_var=concat( "moviepath=", $video )}
    
    {* Embed URL, which URL to retrieve the embed code from. *}
    {set $flash_var=$flash_var|append( "&amp;embedurl=", concat( $siteurl, "/flash/embed/", $object.id ) )}

    {* Embed Link *}
    {set $flash_var=$flash_var|append( "&amp;embedlink=", concat( $siteurl, $object.main_node.url_alias|ezurl(no) ) )}
    
    <script type="text/javascript">
        insertMedia( '<object type="application/x-shockwave-flash"  data="{'flash/flash_player.swf'|ezdesign(no)}"  width="448" height="354"> ');
        insertMedia( '<param name="movie" value="{'flash/flash_player.swf'|ezdesign(no)}"  /> ');
        insertMedia( '<param name="scale" value="exactfit" /> ');
        insertMedia( '<param name="allowScriptAccess" value="sameDomain" />');
        insertMedia( '<param name="allowFullScreen" value="true" />');
        insertMedia( '<param name="flashvars" value="{$flash_var}" />');
        insertMedia( '<p>No <a href="http://www.macromedia.com/go/getflashplayer">Flash player<\/a> avaliable!<\/p>');
        insertMedia( '<\/object>' );
    </script>

    <noscript>
    <object type="application/x-shockwave-flash" data="{'flash/flash_player.swf'|ezdesign(no)}" width="448" height="354">
        <param name="movie" value="{'flash/flash_player.swf'|ezdesign(no)}" />
        <param name="scale" value="exactfit" />
        <param name="allowScriptAccess" value="sameDomain" />
        <param name="allowFullScreen" value="true" />
        <param name="flashvars" value="{$flash_var}" />
        <p>No <a href="http://www.macromedia.com/go/getflashplayer">Flash player</a> avaliable!</p>
    </object>
    </noscript>