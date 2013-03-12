<div class="content-view-block-item">
    <div class="class-comment">
        <div class="attribute-header">
            <h2><a href={$node.parent.url_alias|ezurl()}>{$node.name|wash()}</a></h2>
        </div>

        <div class="attribute-byline float-break">
            <p class="date">{$node.object.published|l10n(datetime)}</p>
            <p class="author">{$node.data_map.author.content|wash}</p>
        </div>

        <div class="attribute-short">
            {attribute_view_gui attribute=$node.data_map.message}
        </div>
    </div>
</div>