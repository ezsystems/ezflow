<div id="address-{$block.zone_id}-{$block.id}">
{def $valid_items = $block.valid_nodes
     $block_name = ''}
{if is_set( $block.name )}
    {set $block_name = $block.name}
{else}
    {set $block_name = ezini( $block.type, 'Name', 'block.ini' )}
{/if}
<h2 class="grey_background">{$block_name}</h2>

<h1><a href="{$items[0].url_alias|ezurl(no)}" style="font-size: 28px">{$valid_items[0].name}</a></h1>

<div style=" float: left; clear: left; width: 49%">

<span style="font-size:10px; color: #990000">{$valid_items[0].ts_publication|l10n(shortdatetime)}</span>

{attribute_view_gui attribute=$valid_items[0].data_map.intro}
</div>

<div style=" float: right; clear: right; width: 49%">
{attribute_view_gui attribute=$valid_items[0].data_map.image image_class='mainstory'}
</div>

<div style="clear: both;"></div>
</div>