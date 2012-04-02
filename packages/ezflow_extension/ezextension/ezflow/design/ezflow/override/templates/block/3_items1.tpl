{def $valid_nodes = $block.valid_nodes
     $valid_node = false()}

<div class="block-type-3items block-view-{$block.view}">

<div class="attribute-header"><h2>{$block.name}</h2></div>

{foreach $valid_nodes as $valid_node}

{node_view_gui view='block_item' image_class='articlethumbnail' content_node=$valid_node}

{delimiter}
<div class="separator"></div>
{/delimiter}

{/foreach}

</div>

{undef $valid_nodes $valid_node}
