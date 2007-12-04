{def $valid_nodes = $block.valid_nodes}
<div class="block-type-5items block-view-{$block.view}">

<div class="columns-two">
<div class="col-1">
<div class="col-content float-break">

<div class="border-box border-box-style4">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-content">

<!-- BLOCK CONTENT: START -->

<div class="class-article float-break">

    <div class="attribute-header">
        <h2><a href={$valid_nodes[0].url_alias|ezurl()}>{$valid_nodes[0].name}</a></h2>
    </div>

    <div class="attribute-image">{attribute_view_gui attribute=$valid_nodes[0].data_map.image image_class=articlethumbnail}</div>

    <div class="attribute-short">
        {attribute_view_gui attribute=$valid_nodes[0].data_map.intro}
    </div>

</div>

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

<div class="class-article float-break">

    <div class="attribute-header">
        <h2><a href={$valid_nodes[1].url_alias|ezurl()}>{$valid_nodes[1].name}</a></h2>
    </div>

    <div class="attribute-image">{attribute_view_gui attribute=$valid_nodes[1].data_map.image image_class=articlethumbnail}</div>

    <div class="attribute-short">
        {attribute_view_gui attribute=$valid_nodes[1].data_map.intro}
    </div>

</div>

<!-- BLOCK CONTENT: END -->

</div>
</div>

</div>
</div>
</div>

<div class="class-article float-break">

    <div class="attribute-image">{attribute_view_gui attribute=$valid_nodes[2].data_map.image image_class=articlethumbnail}</div>

    <div class="attribute-header">
        <h2><a href={$valid_nodes[2].url_alias|ezurl()}>{$valid_nodes[2].name}</a></h2>
    </div>

    <div class="attribute-short">
        {attribute_view_gui attribute=$valid_nodes[2].data_map.intro}
    </div>

</div>

<div class="class-article float-break">

    <div class="attribute-image">{attribute_view_gui attribute=$valid_nodes[3].data_map.image image_class=articlethumbnail}</div>

    <div class="attribute-header">
        <h2><a href={$valid_nodes[3].url_alias|ezurl()}>{$valid_nodes[3].name}</a></h2>
    </div>

    <div class="attribute-short">
        {attribute_view_gui attribute=$valid_nodes[3].data_map.intro}
    </div>

</div>

<div class="class-article float-break">

    <div class="attribute-image">{attribute_view_gui attribute=$valid_nodes[4].data_map.image image_class=articlethumbnail}</div>

    <div class="attribute-header">
        <h2><a href={$valid_nodes[4].url_alias|ezurl()}>{$valid_nodes[4].name}</a></h2>
    </div>

    <div class="attribute-short">
        {attribute_view_gui attribute=$valid_nodes[4].data_map.intro}
    </div>

</div>

</div>