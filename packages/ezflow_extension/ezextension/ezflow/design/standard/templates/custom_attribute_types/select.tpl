{set $custom_attribute_selections = ezini( $block.type, concat( 'CustomAttributeSelection_', $custom_attrib ), 'block.ini' )}
<select id="block-custom_attribute-{$block_id}-{$loop_count}" class="block-control" name="ContentObjectAttribute_ezpage_block_custom_attribute_{$attribute.id}[{$zone_id}][{$block_id}][{$custom_attrib}]">
    {foreach $custom_attribute_selections as $selection_value => $selection_name}
        <option value="{$selection_value|wash()}"{if eq( $block.custom_attributes[$custom_attrib], $selection_value )} selected="selected"{/if} />{$selection_name|wash()}</option>
    {/foreach}
</select>