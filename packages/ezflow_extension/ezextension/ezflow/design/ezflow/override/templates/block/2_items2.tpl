{def $valid_nodes = $block.valid_nodes
     $valid_node = false()}

<div class="block-type-2items block-view-{$block.view}">

{foreach $valid_nodes as $valid_node}

{node_view_gui view='block_item' image_class='block2items1' content_node=$valid_node}

{/foreach}

</div>

{undef $valid_nodes $valid_node}
