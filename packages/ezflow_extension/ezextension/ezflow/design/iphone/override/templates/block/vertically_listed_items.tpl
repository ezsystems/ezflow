<div id="address-{$block.zone_id}-{$block.id}">
{def $block_name = ''}
{if is_set( $block.name )}
    {set $block_name = $block.name}
{else}
    {set $block_name = ezini( $block.type, 'Name', 'block.ini' )}
{/if}
<div class="vertically_listed_items">

<h2>{$block_name|wash()}</h2>

<div class="box">

    <div class="content-view-children float-break">

    {foreach $block.valid_nodes as $item}
             {node_view_gui view=line content_node=$item}
    {/foreach}

    </div>

</div>

</div>

</div>