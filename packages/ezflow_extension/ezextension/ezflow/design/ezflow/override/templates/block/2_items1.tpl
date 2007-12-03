{def $valid_nodes = $block.valid_nodes}

<!-- BLOCK: START -->
<div class="block-type-2items">

<div class="border-box block-style1-box-outside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BLOCK BORDER INSIDE: START -->

<div class="border-box block-style1-box-inside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BLOCK CONTENT: START -->

<div class="class-article">

    <div class="attribute-header">
        <h2><a href={$valid_nodes[0].url_alias|ezurl()}>{$valid_nodes[0].name}</a></h2>
    </div>

    <div class="attribute-image">{attribute_view_gui attribute=$valid_nodes[0].data_map.image image_class=block2items1}</div>

    <div class="attribute-short">
        {attribute_view_gui attribute=$valid_nodes[0].data_map.intro}
    </div>

</div>

<div class="separator"></div>

<div class="class-article">

    <div class="attribute-header">
        <h2><a href={$valid_nodes[1].url_alias|ezurl()}>{$valid_nodes[1].name}</a></h2>
    </div>

    <div class="attribute-image">{attribute_view_gui attribute=$valid_nodes[1].data_map.image image_class=block2items1}</div>

    <div class="attribute-short">
        {attribute_view_gui attribute=$valid_nodes[1].data_map.intro}
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

</div>
<!-- BLOCK: END -->