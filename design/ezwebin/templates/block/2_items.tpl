<div id="address-{$block.zone_id}-{$block.id}">
{def $block_name = ''}
{if is_set( $block.name )}
    {set $block_name = $block.name}
{else}
    {set $block_name = ezini( $block.type, 'Name', 'block.ini' )}
{/if}
<h2 class="grey_background">{$block_name}</h2>

<div class="columns-two">
<div class="col-1">
<div class="col-content">

{def $item_node = fetch( 'content', 'node', hash( 'node_id' , $block.valid_nodes[0].node_id ) )}

{node_view_gui view=line content_node=$item_node}

{undef $item_node}
</div>
</div>
<div class="col-2">
<div class="col-content">


{def $item_node = fetch( 'content', 'node', hash( 'node_id' , $block.valid_nodes[1].node_id ) )}

{node_view_gui view=line content_node=$item_node}

{undef $item_node}
</div>
</div>
</div>
</div>