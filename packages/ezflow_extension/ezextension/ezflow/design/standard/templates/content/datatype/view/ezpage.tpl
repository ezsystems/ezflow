{def $template = ezini( $attribute.content.zone_layout, 'Template', 'zone.ini' )
     $zones = $attribute.content.zones
     $zone_layout = $attribute.content.zone_layout}

{include uri=concat( 'design:zone/', $template ) zones=$zones zone_layout=$zone_layout attribute=$attribute}
