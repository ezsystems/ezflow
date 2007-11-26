<div class="class-article main-article">

<div class="block">
{def $valid_items = $block.valid_nodes
     $block_name = ''}
{if is_set( $block.name )}
    {set $block_name = $block.name}
{else}
    {set $block_name = ezini( $block.type, 'Name', 'block.ini' )}
{/if}

<a href={$valid_items[0].url_alias|ezurl}>
    <h1>{$valid_items[0].name}<span class="arrow">&nbsp;</span></h1>
    <div class="attribute-image">
        {attribute_view_gui attribute=$valid_items[0].data_map.image image_class="iphonelarge"}
    </div>

    <div class="attribute-short">
        {attribute_view_gui attribute=$valid_items[0].data_map.intro}
    </div>
</a>

</div>

</div>
