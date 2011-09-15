{if $block_wrap_template}
    {include uri='design:block_wrap_top.tpl'}
{/if}

{if or( $block.valid_nodes|count(), 
    and( is_set( $block.custom_attributes), $block.custom_attributes|count() ), 
    and( eq( ezini( $block.type, 'ManualAddingOfItems', 'block.ini' ), 'disabled' ), ezini_hasvariable( $block.type, 'FetchClass', 'block.ini' )|not ) )}
    <div id="address-{$block.zone_id}-{$block.id}">
    {block_view_gui block=$block}
    </div>
    <div class="block-separator"></div>
{/if}

{if $block_wrap_template}
    {include uri='design:block_wrap_bottom.tpl'}
{/if}
