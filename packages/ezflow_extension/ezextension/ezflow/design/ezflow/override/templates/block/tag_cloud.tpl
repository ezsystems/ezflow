<div class="block-type-tagcloud block-view-{$block.view}">

<div class="attribute-header"><h2>{$block.name|wash()}</h2></div>

{if is_set( $block.custom_attributes.subtree_node_id )}
    {eztagcloud( hash( 'parent_node_id', $block.custom_attributes.subtree_node_id ))}
{/if}

</div>