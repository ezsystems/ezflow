{def $valid_nodes = $block.valid_nodes}
<!-- BLOCK: START -->
<div class="block-type-gallery">

<div class="border-box block-style6-box-outside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<div class="block">
    <div class="left">
        <h2>{$block.name}</h2>
    </div>
    <div class="right">
    {*
        <input type="image" src={"input-img-prev.png"|ezimage()} />
        <input type="image" src={"input-img-next.png"|ezimage()} />
    *}
    </div>
    <div class="break"></div>
</div>
<!-- BLOCK BORDER INSIDE: START -->

<div class="border-box block-style6-box-inside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BLOCK CONTENT: START -->

<div class="columns-three">
<div class="col-1-2">
<div class="col-1">
<div class="col-content">

<!-- BORDER BOX STYLE 1: START -->

<div class="border-box border-box-style1">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BOX CONTENT: START -->

    <div class="attribute-image">{attribute_view_gui href=$valid_nodes[0].url_alias|ezurl() attribute=$valid_nodes[0].data_map.image image_class="ifrontpagegallery"}</div>

<!-- BOX CONTENT: END -->

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- BORDER BOX STYLE 1: END -->

<div class="attribute-caption">
    {if $valid_nodes[0].data_map.caption.has_content}
        {attribute_view_gui attribute=$valid_nodes[0].data_map.caption}
    {else}
        <p>{$valid_nodes[0].name}</p>
    {/if}
</div>

</div>
</div>
<div class="col-2">
<div class="col-content">

<!-- BORDER BOX STYLE 1: START -->

<div class="border-box border-box-style1">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BOX CONTENT: START -->

    <div class="attribute-image">{attribute_view_gui href=$valid_nodes[1].url_alias|ezurl() attribute=$valid_nodes[1].data_map.image image_class="ifrontpagegallery"}</div>

<!-- BOX CONTENT: END -->

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- BORDER BOX STYLE 1: END -->

<div class="attribute-caption">
    {if $valid_nodes[1].data_map.caption.has_content}
        {attribute_view_gui attribute=$valid_nodes[1].data_map.caption}
    {else}
        <p>{$valid_nodes[1].name}</p>
    {/if}
</div>

</div>
</div>
</div>
<div class="col-3">
<div class="col-content">

<!-- BORDER BOX STYLE 1: START -->

<div class="border-box border-box-style1">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BOX CONTENT: START -->

    <div class="attribute-image">{attribute_view_gui href=$valid_nodes[2].url_alias|ezurl() attribute=$valid_nodes[2].data_map.image image_class="ifrontpagegallery"}</div>

<!-- BOX CONTENT: END -->

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- BORDER BOX STYLE 1: END -->

<div class="attribute-caption">
    {if $valid_nodes[2].data_map.caption.has_content}
        {attribute_view_gui attribute=$valid_nodes[2].data_map.caption}
    {else}
        <p>{$valid_nodes[2].name}</p>
    {/if}
</div>

</div>
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