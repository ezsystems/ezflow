<div id="address-{$block.zone_id}-{$block.id}">

<h2 class="grey_background">{$block_name|wash()}</h2>

<div class="columns-three">
<div class="col-1-2">
<div class="col-1">
<div class="col-content">

{node_view_gui view=line content_node=$block.valid_nodes[0]}

</div>
</div>
<div class="col-2">
<div class="col-content">

{node_view_gui view=line content_node=$block.valid_nodes[1]}

</div>
</div>
</div>
<div class="col-3">
<div class="col-content">

{node_view_gui view=line content_node=$block.valid_nodes[2]}

</div>
</div>
</div>
</div>