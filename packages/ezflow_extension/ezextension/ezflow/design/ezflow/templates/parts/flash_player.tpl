{* Flash player. This template is responsible for producing the html needed to display the flash player
    and generate embeddable code so others can reuse the flash player on their site. *}

{* Parameters: 
    - movie
      String: Path to the flash movie which the player will display.
      Default: Empty.
      Required: Yes
      
    - object_id 
      Int: The object id of the flash player. Used for the /flash/embed/<object_id> module which generates embedable code.
      Default: Empty.
      Required: Yes.

    - url_alias
      string: Path to the flash player node.
      Default: Emtpy.
      Required: Yes.
    
    ========================================================================================================================
    
    - commercial
      String: Path to a flash (.flv) movie which will be shown before and after the main movie.
      Default: Empty.
      Required: No.

    - header
      String: A string that can be used as a header inside the flash movie.
      Default: Emtpy.
      Required: No.

    - generate_shareable_code 
      Boolean: Generate the embedable code which others can cut'n'paste into their blog, etc. The code will be put inside a <form>.
      Default: False.
      Required: No.

    - logo
      String: Path to a small image shown top left of the flash player.
      Default: Empty.
      Required: No.

    - raw_html_sharable_code
      Boolean: Only generate and output the embedable code. 
      Default: False.
      Required: No.

    - screenshot
      String: Path to an image of a poster frame/first frame.
      Default: No poster frame. 
      Required: No

    - size
      String: If "small" is specified the audio-only flash player will be used. If nothing is specified the default video flash player is used.
      Default: "large"
      Required: No
*}

{def $width=0
     $height=0
     $flash_player='flash/flash_player.swf'|ezdesign(no)
     $flash_player_embed='flash/flash_player.swf'|ezdesign(no)
     $siteurl=concat( "http://", ezini( 'SiteSettings', 'SiteURL' ) ) 
     $flash_var_value=concat( "moviepath=", $siteurl, $movie|ezurl(no) )}

{switch match=$size}
    {case match="small"}
        {set $height=82}
        {set $width=270}
        {set $flash_player='flash/flash_player_small.swf'|ezdesign(no)}
    {/case}
    {case}
        {set $height=420}
        {set $width=530}
    {/case}
{/switch}

{* Poster Frame/Screenshot. Shown as first frame in the movie. *}
{if and( is_set( $screenshot ), $screenshot|ne( "" ) )}
    {set $flash_var_value=$flash_var_value|append( "&amp;screenshotpicture=", $siteurl, "/", $screenshot )}
{/if}

{* Addition header, displayed inside the flash right above the center play button *}
{if and( is_set( $header ), $header|ne( "" ) )}
    {set $flash_var_value=$flash_var_value|append( "&amp;title=", $header )}
{/if}

{* Logo (shown at the upper left of the image ) *}
{if and( is_set( $logo ), $logo|ne( "" ) )}
    {set $flash_var_value=$flash_var_value|append( "&amp;logopicture=", $siteurl, "/", $logo )}
{/if}

{* Commerical *}
{if and( is_set( $commercial ), $commercial|ne( "" ) )}
    {set $flash_var_value=$flash_var_value|append( "&amp;commercial=", $siteurl, $commercial|ezurl(no) )}
{/if}

{* Embed URL, which URL to retrieve the embed code from. *}
{set $flash_var_value=$flash_var_value|append( "&amp;embedurl=", concat( $siteurl, "/flash/embed/", $object_id ) )}

{* Embed Link *}
{set $flash_var_value=$flash_var_value|append( "&amp;embedlink=", concat( $siteurl, $url_alias|ezurl(no) ) )}


{if is_set( $generate_shareable_code )}
    {* Embed code, spanned over multiple lines to be easier to deal with. *}
    {def $embed_code=concat( "<object width='", $width, "' height='", $height, "' " )}
    {set $embed_code=$embed_code|append( "type='application/x-shockwave-flash' data='", $siteurl, $flash_player_embed, "'>" )}
    {set $embed_code=$embed_code|append( "<param name='movie' value='", $siteurl, $flash_player_embed, "' />" )}
    {set $embed_code=$embed_code|append( "<param name='allowScriptAccess' value='sameDomain' />" )}
    {set $embed_code=$embed_code|append( "<param name='allowFullScreen' value='true' />" )}
    {set $embed_code=$embed_code|append( "<param name='scale' value='exactfit' />" )}
    {set $embed_code=$embed_code|append( "<param name='flashvars' value='", $flash_var_value, "'/>" )}    
    {set $embed_code=$embed_code|append( "</object>" )}
        
    {if is_set( $raw_html_sharable_code )}
        {$embed_code}
    {else}
        <form name="embed_form" id="embed_form" action="#">
            <label>Embed code:</label>
            <input size="45" name="embed_url" type="text" readonly="readonly" value="{$embed_code|wash}" onclick="javascript:document.embed_form.embed_url.focus() ;document.embed_form.embed_url.select();" />
        </form>
    {/if}
{else}
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
{/if}