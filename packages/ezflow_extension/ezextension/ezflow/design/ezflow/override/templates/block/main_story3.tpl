{def $valid_node = $block.valid_nodes[0]}

<!-- BLOCK: START -->
<div class="block-type-mainstory">
<div class="border-box block-style5-box-outside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<div class="columns-two">
<div class="col-1">
<div class="col-content">

<!-- BORDER BOX STYLE 1: START -->

<div class="border-box border-box-style1">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BOX CONTENT: START -->

    <div class="attribute-image">{attribute_view_gui attribute=$valid_node.data_map.image image_class='mainstory3'}</div>

<!-- BOX CONTENT: END -->

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- BORDER BOX STYLE 1: END -->

</div>
</div>
<div class="col-2">
<div class="col-content">

    <div class="attribute-header">
        <h2><a href="{$valid_node.url_alias|ezurl(no)}">{$valid_node.name|wash()}</a></h2>
    </div>

    <div class="attribute-short">
        {attribute_view_gui attribute=$valid_node.data_map.intro}
    </div>

</div>
</div>
</div>

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>
</div>
<!-- BLOCK: END -->

{undef $valid_node}