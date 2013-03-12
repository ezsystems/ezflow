<div class="content-view-embed">
<div class="class-image">
    <div class="attribute-image">{if is_set( $link_parameters.href )}{attribute_view_gui attribute=$object.data_map.image image_class="iphonelarge" href=$link_parameters.href|ezurl target=$link_parameters.target link_class=first_set( $link_parameters.class, '' ) link_id=first_set( $link_parameters['xhtml:id'], '' ) link_title=first_set( $link_parameters['xhtml:title'], '' ) border_size=first_set( $object_parameters.border_size, '0' ) border_color=first_set( $object_parameters.border_color, '' ) border_style=first_set( $object_parameters.border_style, 'solid' ) margin_size=first_set( $object_parameters.margin_size, '' )}{else}{attribute_view_gui attribute=$object.data_map.image image_class="iphonelarge" border_size=first_set( $object_parameters.border_size, '0' ) border_color=first_set( $object_parameters.border_color, '' ) border_style=first_set( $object_parameters.border_style, 'solid' ) margin_size=first_set( $object_parameters.margin_size, '' )}{/if}</div>

    {if $object.data_map.caption.has_content}
    {if is_set( $object.data_map.image.content['iphonelarge'].width )}
        {def $image_width = $object.data_map.image.content['iphonelarge'].width}
        {if is_set($object_parameters.margin_size)}
            {set $image_width = $image_width|sum(  $object_parameters.margin_size|mul( 2 ) )}
        {/if}
        {if is_set($object_parameters.border_size)}
            {set $image_width = $image_width|sum(  $object_parameters.border_size|mul( 2 ) )}
        {/if}
        <div class="attribute-caption" style="width: {$image_width}px">
    {else}
        <div class="attribute-caption">
    {/if}
            {attribute_view_gui attribute=$object.data_map.caption} </div>
        {/if} </div>
</div>
