{def $pages = pagelink( $node.object.id )}

{if $pages}

<h3>{'Content "%1" is available for the following pages and blocks:'|i18n( 'design/admin2/tabs/ezflow', , array( $node.name|wash() ) )}</h3>

<div class="block">
<table class="list" cellspacing="0" summary="{'Content relation overview.'|i18n( 'design/admin2/tabs/ezflow' )}">
<tr>
    <th>{'Name'|i18n( 'design/admin2/tabs/ezflow' )}</th>
    <th>{'Node ID'|i18n( 'design/admin2/tabs/ezflow' )}</th>
    <th>{'Available for blocks'|i18n( 'design/admin2/tabs/ezflow' )}</th>
</tr>
{foreach $pages as $page sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td><a href="{$page.node.url_alias|ezurl( 'no' )}" title="{$page.node.name|wash()}">{$page.node.name|wash()}</a></td>
    <td>{$page.node.node_id}</td>
    <td>
        {foreach $page.blocks as $block}
            {$block.name|wash()}<br />
        {/foreach}
    </td>
</tr>
{/foreach}
</table>
</div>

{else}

<h3>{'Content "%1" is not related to any pages and blocks.'|i18n( 'design/admin2/tabs/ezflow', , array( $node.name|wash() ) )}</h3>

{/if}

{undef $pages}
