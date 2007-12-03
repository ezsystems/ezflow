{* Article - Line view *}

<div class="content-view-line">
    <div class="class-article float-break">
        <a href={$node.url_alias|ezurl}>
            <h2>{$node.data_map.title.content|wash} <span class="arrow">&nbsp;</span></h2>

            {section show=$node.data_map.image.has_content}
                <div class="attribute-image">
                    {attribute_view_gui attribute=$node.data_map.image image_class="iphonethumb"}
                </div>
            {/section}

            {section show=$node.data_map.intro.content.is_empty|not}
            <div class="attribute-short">
                {attribute_view_gui attribute=$node.data_map.intro}
            </div>
            {/section}    
        </a>
    </div>
</div>