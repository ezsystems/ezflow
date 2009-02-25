<!-- BORDER BOX STYLE 1: START -->

<div class="border-box border-box-style1">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BOX CONTENT: START -->

    <div class="attribute-image">{attribute_view_gui href=$node.url_alias|ezurl() attribute=$node.data_map.image image_class=$image_class}</div>

<!-- BOX CONTENT: END -->

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- BORDER BOX STYLE 1: END -->

<div class="attribute-caption">
    {if $node.data_map.caption.has_content}
        {attribute_view_gui attribute=$node.data_map.caption}
    {else}
        <p>{$node.name|wash()}</p>
    {/if}
</div>