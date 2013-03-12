<div class="class-article float-break">

    <div class="attribute-header">
        <h2><a href={$node.url_alias|ezurl()}>{$node.name|wash()}</a></h2>
    </div>

    <div class="attribute-image">{attribute_view_gui attribute=$node.data_map.image image_class=$image_class}</div>

    <div class="attribute-short">
        {attribute_view_gui attribute=$node.data_map.intro}
    </div>

</div>