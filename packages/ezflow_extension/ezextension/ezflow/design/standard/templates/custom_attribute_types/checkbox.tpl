<input id="block-custom_attribute-{$block_id}-{$loop_count}-a" class="block-control" type="hidden" name="ContentObjectAttribute_ezpage_block_custom_attribute_{$attribute.id}[{$zone_id}][{$block_id}][{$custom_attrib}]" value="0" />
<input id="block-custom_attribute-{$block_id}-{$loop_count}-b" class="block-control" type="checkbox" name="ContentObjectAttribute_ezpage_block_custom_attribute_{$attribute.id}[{$zone_id}][{$block_id}][{$custom_attrib}]"{if eq( $block.custom_attributes[$custom_attrib], '1')} checked="checked"{/if} value="1" />