{def $template = ezini( $attribute.content.zone_layout, 'Template', 'zone.ini' )
     $zones = $attribute.content.zones}

{include uri=concat( 'design:zone/', $template ) zones=$zones}