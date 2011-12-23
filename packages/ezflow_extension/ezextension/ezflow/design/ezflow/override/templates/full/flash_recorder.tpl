{*?template charset=utf-8?*}

{* This template can not be view-cached since we're fetching list of files from a remote server 
   and that list will change independently of this object. *}
{set-block scope=root variable=cache_ttl}0{/set-block}

{* Define default settings for the broadcaster flash *}
{def $width=340
     $height=340
     $flash='flash/broadcaster.swf'|ezdesign(no)
     $fileserver=$node.object.data_map.file_server.content
     $streamserver=$node.object.data_map.stream_server.content
     $key=$node.object.data_map.key.content
     $flash_var=concat( "streamserver=", $streamserver )}
{set $flash_var=$flash_var|append( "&amp;fileserver=", $fileserver )}

{* List flash recordings from red5 server *}
{def $flash_list=red5list( $fileserver, $key )
     $should_display_brodcaster=true()
     $video_url=""
     $video_name=""}

{* Figure out if we should play a movie or if we should record one *}
{foreach $flash_list as $name => $item}
    {if $view_parameters.video|eq( $name|wash )}
        {set $should_display_brodcaster=false()}
        {set $video_url=$item.absoluteURL}
        {set $video_name=$name}
        {break}
    {/if}
{/foreach}

{* Define defaults if we're playing a movie *}
{if $should_display_brodcaster|not()}
    {set $height=352}
    {set $width=445}
    {set $flash='flash/streaming.swf'|ezdesign(no)} 
    {set $flash_var=concat( "moviepath=", $video_url )}
{/if}


<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<div class="content-view-full">
    <div class="class-{$node.object.class_identifier}">

        <div class="attribute-header">
            <h1>{$node.name|wash()}</h1>
        </div>

        <div class="block">    
            <script type="text/javascript">
                insertMedia( '<object type="application/x-shockwave-flash" data="{$flash}" width="{$width}" height="{$height}"> ');
                insertMedia( '<param name="movie" value="{$flash}" /> ');
                insertMedia( '<param name="scale" value="exactfit" /> ');
                insertMedia( '<param name="wmode" value="opaque" />' );
                insertMedia( '<param name="allowScriptAccess" value="sameDomain" />');
                insertMedia( '<param name="allowFullScreen" value="true" />');
                insertMedia( '<param name="bgcolor" value="#ffffff" />' );
                insertMedia( '<param name="quality" value="high" />' );
                insertMedia( '<param name="flashvars" value="{$flash_var}" />');
                insertMedia( '<param name="menu" value="false" />' );
                insertMedia( '<p>No <a href="http://www.macromedia.com/go/getflashplayer">Flash player<\/a> avaliable!<\/p>');
                insertMedia( '<\/object>' );
            </script>

            <noscript>
            <object type="application/x-shockwave-flash" data="{$flash}?{$flash_var}" width="{$width}" height="{$height}">          
                <param name="movie" value="{$flash}?{$flash_var}" />
                <param name="scale" value="exactfit" />
                <param name="wmode" value="opaque" />
                <param name="allowScriptAccess" value="sameDomain" />
                <param name="bgcolor" value="#ffffff" />
                <param name="allowFullScreen" value="false" />
                <param name="quality" value="high" />
                <param name="flashvars" value="{$flash_var}" />
                <param name="menu" value="false" />
                <p>No <a href="http://www.macromedia.com/go/getflashplayer">Flash player</a> avaliable!</p>
            </object>
            </noscript>
        </div>
        <div class="block">
            {if $flash_list}
                <h2>Recorded videos</h2>
                <ul>
                    {foreach $flash_list as $name => $item}
                        {if $view_parameters.video|eq( $name|wash )}
                            <li>{$name|wash}</li>
                        {else}
                            <li><a href={concat( $node.url_alias, "/(video)/", $name|wash )|ezurl}>{$name|wash}</a></li>
                        {/if}
                    {/foreach}            
                </ul>

                {if $should_display_brodcaster|not}
                    <p><a href={$node.url_alias|ezurl}>Record a video</a></p>
                {/if}
            {/if}
        </div>
    </div>
</div>

</div>
</div>
</div>
</div>