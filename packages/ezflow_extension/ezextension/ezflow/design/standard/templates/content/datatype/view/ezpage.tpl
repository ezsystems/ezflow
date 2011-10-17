{def $template = ezini( $attribute.content.zone_layout, 'Template', 'zone.ini' )
     $zones = $attribute.content.zones
     $zone_layout = $attribute.content.zone_layout
     $block_wrap_template = ezini( 'General', 'BlockWrapTemplate', 'zone.ini' )|eq('enabled')}

{include uri=concat( 'design:zone/', $template ) zones=$zones zone_layout=$zone_layout block_wrap_template=$block_wrap_template attribute=$attribute}
