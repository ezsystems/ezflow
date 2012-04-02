{if and( is_set( $block.custom_attributes.limit ), 
            ne( $block.custom_attributes.limit, '' ) )}
    {def $limit = $block.custom_attributes.limit}
{else}
    {def $limit = '5'}
{/if}

{if and( is_set( $block.custom_attributes.width ), 
            ne( $block.custom_attributes.width, '' ) )}
    {def $width = $block.custom_attributes.width}
{else}
    {def $width = '460'}
{/if}

{if and( is_set( $block.custom_attributes.height ), 
            ne( $block.custom_attributes.height, '' ) )}
    {def $height = $block.custom_attributes.height}
{else}
    {def $height = '600'}
{/if}

{def $locations = fetch( 'content', 'tree', hash( 'parent_node_id', $block.custom_attributes.parent_node_id,
                                                  'class_filter_type', 'include',
                                                  'class_filter_array', array( $block.custom_attributes.class ),
                                                  'sort_by', array( 'published', false() ),
                                                  'limit', $limit ) )
     $attribute = $block.custom_attributes.attribute
     $location = false()
     $index = 0}

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={ezini('SiteSettings','GMapsKey')}" type="text/javascript"></script>
{ezscript_require( 'ezflgmapview.js' )}

<script type="text/javascript">
    var data{$block.id} = [];
    
    {foreach $locations as $location}
    data{$block.id}.push( {ldelim}
                            point: new GLatLng( {$location.data_map[$attribute].content.latitude}, {$location.data_map[$attribute].content.longitude} ),
                            address: '{$location.data_map[$attribute].content.address}'
                        {rdelim} );
    {/foreach}

    eZFLGMapAddListener( window, 'load', function(){ldelim} eZFLGMapView( '{$block.id}', data{$block.id} ) {rdelim}, false );
</script>

<h2>{$block.name|wash()}</h2>

<ul>
{foreach $locations as $index => $location}
    <li><a id="ezflb-pointer-{$block.id}-{$index}" href="{$location.url_alias|ezurl('no')}">{$location.name|wash()}</a></li>
{/foreach}
</ul>

<div id="ezflb-map-{$block.id}" style="width: {$width}px; height: {$height}px"></div>
{undef $limit $width $height $locations $attribute $index $location}
