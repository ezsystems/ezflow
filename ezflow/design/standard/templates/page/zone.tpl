<div class="block">
    <div class="object-left">
    <label>Block name:</label>
        <input type="text" value="" name="ContentObjectAttribute_ezpage_block_name_{$attribute.id}_{$zone_id}" size="70" class="halfbox" />
        <label>Block type:</label>
        <select name="ContentObjectAttribute_ezpage_block_type_{$attribute.id}_{$zone_id}">
        {foreach ezini( 'General', 'AllowedTypes', 'block.ini' ) as $type}
            <option value="{$type}">{ezini( $type, 'Name', 'block.ini' )}</option>
        {/foreach}
        </select>
        <p><input class="button" type="submit" name="CustomActionButton[{$attribute.id}_new_block-{$zone_id}]" value="Add block" /></p>
    </div>
<div class="break">
</div>

{foreach $zone.blocks as $index => $block}
    {if ne( $block.action, 'remove' )}
        {block_edit_gui block=$block block_id=$index current_time=currentdate() zone_id=$zone_id attribute=$attribute zone=$zone}
    {/if}
{/foreach}