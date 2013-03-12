{def $valid_nodes = $block.valid_nodes}

<div class="block-type-3items block-view-{$block.view}">

<!-- BLOCK: START -->
<div class="border-box block-style3-box-outside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BLOCK BORDER INSIDE: START -->

<div class="border-box block-style3-box-inside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BLOCK CONTENT: START -->

<div class="columns-three-divider-1-2">
<div class="columns-three-divider-2-3">

<div class="columns-three">
<div class="col-1-2">
<div class="col-1">
<div class="col-content">

{node_view_gui view='block_item' image_class='block3items3' content_node=$valid_nodes[0]}

</div>
</div>
<div class="col-2">
<div class="col-content">

{node_view_gui view='block_item' image_class='block3items3' content_node=$valid_nodes[1]}

</div>
</div>
</div>
<div class="col-3">
<div class="col-content">

{node_view_gui view='block_item' image_class='block3items3' content_node=$valid_nodes[2]}

</div>
</div>
</div>

</div>
</div>

<!-- BLOCK CONTENT: END -->

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- BLOCK BORDER INSIDE: END -->


</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- BLOCK: END -->

</div>

{undef $valid_nodes}