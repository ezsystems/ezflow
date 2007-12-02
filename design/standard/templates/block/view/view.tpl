<h2>Block: {ezini( $block.type, 'Name', 'block.ini' )}</h2>

<ul>
{foreach $block.valid_nodes as $valid_node}
    <li>{$valid_node.name}</li>
{/foreach}
</ul>