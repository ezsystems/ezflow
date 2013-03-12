{*
Input:
 image_class - Which image alias to show, default is large
 css_class     - Optional css class to wrap around the <img> tag, the
                 class will be placed in a <div> tag.
 alignment     - How to align the image, use 'left', 'right' or false().
 link_to_image - boolean, if true the url_alias will be fetched and
                 used as link.
 href          - Optional string, if set it will create a <a> tag
                 around the image with href as the link.
 border_size   - Size of border around image, default is 0
*}

{default image_class=large
         css_class=false()
         alignment=false()
         link_to_image=false()
         href=false()
         target=false()
         hspace=false()
         border_size=0}
{switch match=$image_class}
{case in=array( "large", "billboard", "iphonelarge", "imagelarge" )}
    {set $image_class="iphonelarge"}
{/case}
{case match="mainstory1"}
    {set $image_class="imainstory1"}
{/case}
{case match="ifrontpagegallery"}
    {set $image_class="ifrontpagegallery"}
{/case}

{case}
    {set $image_class="iphonethumb"}
{/case}
{/switch}


{let image_content=$attribute.content}

{section show=$image_content.is_valid}

    {let image=$image_content[$image_class]}

    {section show=$link_to_image}
        {let image_original=$image_content['original']}
        {set href=$image_original.url|ezroot}
        {/let}
    {/section}
    {switch match=$alignment}
    {case match='left'}
        <div class="imageleft">
    {/case}
    {case match='right'}
        <div class="imageright">
    {/case}
    {case/}
    {/switch}

    {section show=$css_class}
        <div class="{$css_class|wash}">
    {/section}

    {section show=and( is_set( $image ), $image )}
        {section show=$href}<a href={$href}{section show=and( is_set( $link_class ), $link_class )} class="{$link_class}"{/section}{section show=and( is_set( $link_id ), $link_id )} id="{$link_id}"{/section}{section show=$target} target="{$target}"{/section}>{/section}
        <img src={$image.url|ezroot} width="{$image.width}" height="{$image.height}" {section show=$hspace}hspace="{$hspace}"{/section} style="border: {$border_size}px;" alt="{$image.text|wash(xhtml)}" title="{$image.text|wash(xhtml)}" />
        {section show=$href}</a>{/section}
    {/section}

    {section show=$css_class}
        </div>
    {/section}

    {switch match=$alignment}
    {case match='left'}
        </div>
    {/case}
    {case match='right'}
        </div>
    {/case}
    {case/}
    {/switch}

    {/let}

{/section}

{/let}

{/default}
