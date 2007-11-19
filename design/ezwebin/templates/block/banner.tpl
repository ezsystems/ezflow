<div id="address-{$block.zone_id}-{$block.id}">
<div class="banner">

{def $item_node = $banner.valid_nodes[0]}

{def $size="banner"
     $alternative_text=$item_node.data_map.name.content}

{if is_set( $item_node.data_map.image.content[$size].alternative_text )}
{set $alternative_text=$item_node.data_map.image.content[$size].alternative_text}
{/if}


	{if and(is_set($item_node.data_map.url), $item_node.data_map.url.content)}
				<a href={$item_node.data_map.url.content|ezurl}>
				<img src={$item_node.data_map.image.content[$size].full_path|ezroot} alt="{$alternative_text}" border="0" width="{$item_node.data_map.image.content[$size].width}" height="{$item_node.data_map.image.content[$size].height}" />
				</a>
	{else}
				<img src={$item_node.data_map.image.content[$size].full_path|ezroot} alt="{$alternative_text}" border="0" width="{$item_node.data_map.image.content[$size].width}" height="{$item_node.data_map.image.content[$size].height}" />
	{/if}
</div>
</div>