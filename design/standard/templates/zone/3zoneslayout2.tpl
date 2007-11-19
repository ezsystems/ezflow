{foreach $zones as $zone}

<h1>Zone: {$zone.name}</h1>

    {foreach $zone.blocks as $block}
        {block_view_gui block=$block}
    {/foreach}

{/foreach}