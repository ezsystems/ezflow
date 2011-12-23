<table>
<tr>
<td valign="top">
<div class="attribute-heading">
    <h1>Flash Live Streaming</h1>
</div>

{def $path="http://localhost:91/streams/streams/"
     $flash_var_value=""
     $flash_player='flash/streaming.swf'|ezdesign(no)
     $height=420
     $width=530}

{if is_set( $view_parameters.file )}
    {set $flash_var_value=concat( "moviepath=", $path, $list[$view_parameters.file|int] )}
{else}
    {set $flash_var_value=concat( "moviepath=", $path, $list[0] )}
{/if}

<script type="text/javascript">
    insertMedia( '<object type="application/x-shockwave-flash" data="{$flash_player}" width="{$width}" height="{$height}"> ');
    insertMedia( '<param name="movie" value="{$flash_player}" /> ');
    insertMedia( '<param name="scale" value="exactfit" /> ');
    insertMedia( '<param name="allowScriptAccess" value="sameDomain" />');
    insertMedia( '<param name="allowFullScreen" value="true" />');
    insertMedia( '<param name="flashvars" value="{$flash_var_value}" />');
    insertMedia( '<p>No <a href="http://www.macromedia.com/go/getflashplayer">Flash player</a> avaliable!</p>');
    insertMedia( '</object>' );
</script>

<noscript>
<object type="application/x-shockwave-flash" data="{$flash_player}" width="{$width}" height="{$height}">          
    <param name="movie" value="{$flash_player}" />
    <param name="scale" value="exactfit" />
    <param name="allowScriptAccess" value="sameDomain" />
    <param name="allowFullScreen" value="true" />
    <param name="flashvars" value="{$flash_var_value}" />
    <p>No <a href="http://www.macromedia.com/go/getflashplayer">Flash player</a> avaliable!</p>
</object>
</noscript>
</td>
<td valign="top">
<div class="attribute-heading">
    <h2 class="bullet">Flash files</h2>
</div>


<ul class="linklist">
{foreach $list as $index => $file}
    <li><a href={concat( "/flash/list/(file)/", $index)|ezurl}>{$file|explode(".flv").0}</a></li>
{/foreach}
</ul>
</tr>
</table>