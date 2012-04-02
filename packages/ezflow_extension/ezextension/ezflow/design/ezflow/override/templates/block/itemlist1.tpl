{def $valid_nodes = $block.valid_nodes
     $valid_node = false()}

<!-- BLOCK: START -->

<div class="block-type-itemlist">

<div class="attribute-header">
    <h2>{$block.name|wash()}</h2>
</div>
<div class="block-content">
    <ul>
    {foreach $valid_nodes as $valid_node}
       <li><a href={$valid_node.url_alias|ezurl()}>{$valid_node.name|wash()}</a></li>
    {/foreach}
    </ul>
</div>

</div>

<!-- BLOCK: END -->

{undef $valid_nodes $valid_node}
