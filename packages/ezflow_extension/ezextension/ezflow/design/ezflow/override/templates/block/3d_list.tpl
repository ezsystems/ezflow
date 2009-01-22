{def $valid_nodes = $block.valid_nodes}

<div id="address-{$block.zone_id}-{$block.id}">

<div class="itemized_sub_items">
<div class="box">
<div class="tl"><div class="tr">
<div class="box-content">
    <h2>{$block_name|wash()}</h2>

    <ul>
    {foreach $valid_nodes as $valid_node}
       <li><div><a href={$valid_node.url_alias|ezurl}>{$valid_node.name|wash()}</a></div></li>
    {/foreach}
    </ul>
</div></div>
</div>
</div>
</div>
</div>

{undef $valid_nodes}