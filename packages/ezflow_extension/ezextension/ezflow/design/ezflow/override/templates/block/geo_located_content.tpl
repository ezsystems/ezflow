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


<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={ezini('SiteSettings','GMapsKey')}&sensor={ezini('GMap', 'UseSensor', 'block.ini')}"></script>

{ezscript_require( 'ezflgmapview.js' )}

<script type="text/javascript">
(function() {ldelim}
    var gmapDataArray = [];

    {foreach $locations as $location}
    gmapDataArray.push( {ldelim}
                            point: new google.maps.LatLng( {$location.data_map[$attribute].content.latitude}, {$location.data_map[$attribute].content.longitude} ),
                            address: '{$location.data_map[$attribute].content.address|wash("javascript")}'
                        {rdelim} );
    {/foreach}

    eZFLGMapAddListener( window, 'load', function(){ldelim} eZFLGMapView( '{$block.id}', gmapDataArray ) {rdelim}, false );
{rdelim})();
</script>

<h2>{$block.name|wash()}</h2>

<ul>
{foreach $locations as $index => $location}
    <li><a id="ezflb-pointer-{$block.id}-{$index}" href="{$location.url_alias|ezurl('no')}">{$location.name|wash()}</a></li>
{/foreach}
</ul>

<div id="ezflb-map-{$block.id}" style="width: {$width}px; height: {$height}px"></div>

{undef $limit $width $height $locations $attribute $index $location}
