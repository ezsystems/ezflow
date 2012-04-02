{def $valid_nodes = $block.valid_nodes
     $valid_node = false()}

<div class="block-type-5items block-view-{$block.view}">

<div class="columns-two">
<div class="col-1">
<div class="col-content float-break">

<div class="border-box border-box-style4">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-content">

<!-- BLOCK CONTENT: START -->

{node_view_gui view='block_item' image_class='articlethumbnail' content_node=$valid_nodes[0]}

<!-- BLOCK CONTENT: END -->

</div>
</div>

</div>
</div>
<div class="col-2">
<div class="col-content float-break">

<div class="border-box border-box-style4">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-content float-break">

<!-- BLOCK CONTENT: START -->

{node_view_gui view='block_item' image_class='articlethumbnail' content_node=$valid_nodes[1]}

<!-- BLOCK CONTENT: END -->

</div>
</div>

</div>
</div>
</div>

{foreach $valid_nodes as $valid_node offset 2}

{node_view_gui view='block_item' image_class='articlethumbnail' content_node=$valid_node}

{/foreach}


</div>

{undef $valid_nodes $valid_node}
