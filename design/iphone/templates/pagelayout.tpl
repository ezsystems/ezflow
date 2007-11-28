<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$site.http_equiv.Content-language|wash}" lang="{$site.http_equiv.Content-language|wash}">
<head>
    <style type="text/css">
        @import url({"stylesheets/core.css"|ezdesign(no)});
        @import url({"stylesheets/content.css"|ezdesign(no)});
        @import url({ezini('StylesheetSettings','ClassesCSS','design.ini')|ezroot(no)});
        @import url({ezini('StylesheetSettings','SiteCSS','design.ini')|ezroot(no)});
        {foreach ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ) as $css_file}
        @import url({concat( 'stylesheets/', $css_file )|ezdesign});
        {/foreach}
    
        @import url({"stylesheets/iphone.css"|ezdesign(no)});
    </style>
    
    {include uri='design:page_head.tpl'}

	<meta id="viewport" name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/
</head>

{def $pagedesign_class = fetch( 'content', 'class', hash( 'class_id', 'template_look' ) )}
{if $pagedesign_class.object_count|eq( 0 )|not}
    {def $pagedesign = $pagedesign_class.object_list[0]}
{/if}

<body>
    <div id="page">
        <div id="main">
            {* Only show full version link and logo on the frontpage *}
            {if $module_result.path|count|eq(1)}
                <div id="logo">
                    <div id="full-version-link">
                        <a href="/">Non-iPhone optimized site</a>
                    </div>

                        {if $pagedesign.data_map.image.content.is_valid|not()}
                            <h1><a href={"/"|ezurl} title="{ezini('SiteSettings','SiteName')}">{ezini('SiteSettings','SiteName')}</a></h1>
                        {else}
                            <a href={"/"|ezurl} title="{ezini('SiteSettings','SiteName')}"><img src={$pagedesign.data_map.image.content[iphonethumb].full_path|ezroot} alt="{$pagedesign.data_map.image.content[iphonethumb].text}" width="{$pagedesign.data_map.image.content[iphonethumb].width}" height="{$pagedesign.data_map.image.content[iphonethumb].height}" /></a>
                        {/if}
                </div>
            {else}
                <div id="navigation-buttons" class="float-break">
                    <div class="object-right">
                        <a href={"/"|ezurl}><div id="frontpagebutton" class="blue">Frontpage</div></a>
                    </div>
                    {if is_set( $module_result.node_id )}
                    <div class="object-left">
                        {def $current_node=fetch( 'content', 'node', hash( node_id, $module_result.node_id ))}
                        <a href={$current_node.parent.url_alias|ezurl}><div id="backbutton" class="blue">Back</div></a>
                    </div>
                    {/if}
                </div>
            {/if}

            {$module_result.content}

        </div>
    
        {include uri='design:page_footer.tpl'}


        {if $pagedesign.data_map.footer_script.has_content}
        <script language="javascript" type="text/javascript">
        <!--
            {$pagedesign.data_map.footer_script.content}
        //-->
        </script>
        {/if}
    
    </div>
    
    
    
    <!--DEBUG_REPORT-->
</body>