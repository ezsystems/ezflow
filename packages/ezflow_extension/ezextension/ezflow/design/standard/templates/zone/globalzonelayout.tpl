{foreach $zones as $zone}
    {if and( is_set( $zone.blocks ), $zone.blocks|count() )}
    {foreach $zone.blocks as $block}
        {include uri='design:parts/zone_block.tpl' zone=$zone}
    {/foreach}
    {/if}
{/foreach}
