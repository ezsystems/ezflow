{cache-block expiry=300}

{def $source = $block.custom_attributes.source
     $limit = $block.custom_attributes.limit
     $offset = $block.custom_attributes.offset
     $res = feedreader( $source, $limit, $offset )}

<h2><a href="{$res.links[0]}" title="{$res.title|wash()}">{$res.title|wash()}</a></h2>

<ul>
{foreach $res.items as $item}
    <li><a href="{$item.links[0]}" title="{$item.title|wash()}">{$item.title|wash()}</a></li>
{/foreach}
</ul>

{/cache-block}