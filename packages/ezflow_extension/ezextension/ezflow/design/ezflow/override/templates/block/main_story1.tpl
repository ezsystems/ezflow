{def $valid_node = $block.valid_nodes[0]}

<!-- BLOCK: START -->

<div class="block-type-mainstory">
    <div class="attribute-image">
        {attribute_view_gui href=$valid_node.url_alias|ezurl() attribute=$valid_node.data_map.image image_class='mainstory1'}
    </div>

    <div class="trans-background">&nbsp;</div>

    <div class="attribute-link">
        <a href="{$valid_node.url_alias|ezurl(no)}">{$valid_node.name|wash()}</a>
    </div>
</div>

<!-- BLOCK: END -->

{undef $valid_node}