<div id="address-{$block.zone_id}-{$block.id}">


{def $valid_items = $block.valid_nodes
     $block_name = ''}
{if is_set( $block.name )}
    {set $block_name = $block.name}
{else}
    {set $block_name = ezini( $block.type, 'Name', 'block.ini' )}
{/if}

<div class="block">
    {node_view_gui view=line content_node=$valid_items[0]}
</div>

<div class="block">
    {node_view_gui view=line content_node=$valid_items[1]}
</div>

<div class="block">
    {node_view_gui view=line content_node=$valid_items[2]}
</div>

</div>