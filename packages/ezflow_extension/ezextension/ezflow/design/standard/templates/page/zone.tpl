<div class="block">
    <label>{'Block name:'|i18n( 'design/standard/datatype/ezpage' )}</label>
    <input type="text" value="" name="ContentObjectAttribute_ezpage_block_name_{$attribute.id}_{$zone_id}" size="70" class="halfbox" />
</div>

<div class="block">
    <label>{'Block type:'|i18n( 'design/standard/datatype/ezpage' )}</label>
    <select name="ContentObjectAttribute_ezpage_block_type_{$attribute.id}_{$zone_id}">
    {foreach ezini( 'General', 'AllowedTypes', 'block.ini' ) as $type}
        <option value="{$type}">{ezini( $type, 'Name', 'block.ini' )}</option>
    {/foreach}
    </select>
</div>

<div class="block">
    <input class="button" type="submit" name="CustomActionButton[{$attribute.id}_new_block-{$zone_id}]" value="{'Add block'|i18n( 'design/standard/datatype/ezpage' )}" />
</div>

<div class="block">
    <a class="trigger expand-all" href="#" title="{'Expand'|i18n( 'design/standard/datatype/ezpage' )}">{'Expand'|i18n( 'design/standard/datatype/ezpage' )}</a> | <a class="trigger collapse-all" href="#" title="{'Collapse'|i18n( 'design/standard/datatype/ezpage' )}">{'Collapse'|i18n( 'design/standard/datatype/ezpage' )}</a>
</div>

{foreach $zone.blocks as $index => $block}
    {if ne( $block.action, 'remove' )}
        {block_edit_gui block=$block block_id=$index current_time=currentdate() zone_id=$zone_id attribute=$attribute zone=$zone}
    {/if}
{/foreach}