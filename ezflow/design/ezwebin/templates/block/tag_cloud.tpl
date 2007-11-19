<div id="address-{$block.zone_id}-{$block.id}">
{def $block_name = ''}
{if is_set( $block.name )}
    {set $block_name = $block.name}
{else}
    {set $block_name = ezini( $block.type, 'Name', 'block.ini' )}
{/if}
    <div class="attribute-tag-cloud">
    <h2 class="grey_background">{$block_name}</h2>
        {eztagcloud( hash( 'parent_node_id', $block.custom_attributes.subtree_node_id ))}
    </div>
    </div>