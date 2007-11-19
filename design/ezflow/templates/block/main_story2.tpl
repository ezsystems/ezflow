<div id="address-{$block.zone_id}-{$block.id}">

{def $valid_items = $block.valid_nodes
     $block_name = ''}
{if is_set( $block.name )}
    {set $block_name = $block.name}
{else}
    {set $block_name = ezini( $block.type, 'Name', 'block.ini' )}
{/if}
<h2 class="grey_background">{$block_name}</h2>

{attribute_view_gui attribute=$valid_items[0].data_map.image image_class='mainstory1'}

<h1><a href="{$valid_items[0].url_alias|ezurl(no)}" style="font-size: 30px; font-weight: bold; color: #000000; text-decoration: none">{$valid_items[0].name}</a></h1>

{attribute_view_gui attribute=$valid_items[0].data_map.intro}

</div>