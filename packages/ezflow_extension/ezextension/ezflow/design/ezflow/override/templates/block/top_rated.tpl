{def $top_rated_array = fetch_by_starrating( hash( 'parent_node_id', $block.custom_attributes.source_node_id,
                                                   'limit', $block.custom_attributes.limit,
                                                   'sort_by', array( 'rating', false() ) ) )
     $top_rated = false()}

<h2>{$block.name|wash()}</h2>

<ul>
{foreach $top_rated_array as $top_rated}
    <li><a href="{$top_rated.url_alias|ezurl( 'no' )}" title="{$top_rated.name|wash()}">{$top_rated.name|wash()}</a></li>
{/foreach}
</ul>
{undef $top_rated_array $top_rated}
