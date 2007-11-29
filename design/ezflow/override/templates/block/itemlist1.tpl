<!-- BLOCK: START -->

<div class="block-type-itemlist">

<div class="attribute-header">
    <h2>{$block.name}</h2>
</div>
<div class="block-content">
    <ul>
    {foreach $block.valid_nodes as $valid_node}
       <li><a href={$valid_node.url_alias|ezurl()}>{$valid_node.name}</a></li>
    {/foreach}
    </ul>
</div>

</div>

<!-- BLOCK: END -->