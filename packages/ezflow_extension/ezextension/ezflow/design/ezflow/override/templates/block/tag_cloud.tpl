<div class="block-type-tagcloud block-view-{$block.view}">

<div class="attribute-header"><h2>{$block.name}</h2></div>

{eztagcloud( hash( 'parent_node_id', $block.custom_attributes.subtree_node_id ))}

</div>