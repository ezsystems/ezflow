<div class="content-view-full">
    <div class="class-article">
    
        <div class="attribute-header">
            <a class="blackbuttonleft button" href={"/"|ezurl}>Back</a>
            <h1>{$node.data_map.title.content|wash()}</h1>
        </div>

        {if $node.data_map.image.has_content}
            <div class="attribute-image">
                {attribute_view_gui attribute=$node.data_map.image image_class="iphonethumb"}

                {if $node.data_map.caption.has_content}
                <div class="caption">
                    {attribute_view_gui attribute=$node.data_map.caption}
                </div>
                {/if}
            </div>
        {/if}

        {if eq( ezini( 'article', 'SummaryInFullView', 'content.ini' ), 'enabled' )}
            {if $node.data_map.intro.content.is_empty|not}
                <div class="attribute-short">
                    {attribute_view_gui attribute=$node.data_map.intro}
                </div>
            {/if}
        {/if}

        {if $node.data_map.body.content.is_empty|not}
            <div class="attribute-long">
                {attribute_view_gui attribute=$node.data_map.body}
            </div>
        {/if}

        <div class="attribute-byline float-break">
            {if $node.data_map.author.content.is_empty|not()}
            <p class="author">
                 {attribute_view_gui attribute=$node.data_map.author}
            </p>
            {/if}
            <p class="date">
                 {$node.object.published|l10n(shortdatetime)}
            </p>
        </div>
            
    </div>
</div>
