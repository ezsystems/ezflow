<div id="address-{$block.zone_id}-{$block.id}">
{def $block_name = ''}
{if is_set( $block.name )}
    {set $block_name = $block.name}
{else}
    {set $block_name = ezini( $block.type, 'Name', 'block.ini' )}
{/if}
<div class="itemized_sub_items">
<div class="box">
<div class="tl"><div class="tr">
<div class="box-content">
    <h2>{$block_name}</h2>

    
    <ul>
    {foreach $block.valid_nodes as $item}
       <li><div><a href={$item.url_alias|ezurl}>{$item.name|wash()}</a></div></li>
    {/foreach}
    </ul>
</div></div>
</div>
</div>
</div>
</div>