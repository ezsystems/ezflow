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
     $attribute = $block.custom_attributes.attribute}
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
    function initialize{$block.id}() {ldelim}
        var bounds = new google.maps.LatLngBounds();
        var myLatlng = new google.maps.LatLng(0,0);
        var myOption = {ldelim}
            mapTypeId: google.maps.MapTypeId.ROADMAP
        {rdelim};
        var map = new google.maps.Map(document.getElementById("ezflb-map-{$block.id}"), myOption);    
        {foreach $locations as $index=>$location}        
            {if $location.data_map[$attribute].content.latitude|ne("")}
            var marker{$index} = new google.maps.Marker({ldelim}
                position: new google.maps.LatLng({$location.data_map[$attribute].content.latitude}, {$location.data_map[$attribute].content.longitude}), 
                map: map,
                title:""            
            {rdelim});
            google.maps.event.addListener(marker{$index}, 'click', function() {ldelim}
              window.location.href={$location.url_alias|ezurl()};  
                        {rdelim} );
            {/if}
            bounds.extend(new google.maps.LatLng({$location.data_map[$attribute].content.latitude}, {$location.data_map[$attribute].content.longitude}));
    {/foreach}
        map.fitBounds(bounds);
        alert(map.getZoom());
    {rdelim}
    </script>
<script type="text/javascript">
<!--
    if ( window.addEventListener )
        window.addEventListener('load', function(){ldelim} initialize{$block.id}() {rdelim}, false);
    else if ( window.attachEvent )
        window.attachEvent('onload', function(){ldelim} initialize{$block.id}() {rdelim} );
-->
</script>

<h2>{$block.name|wash()}</h2>

<ul>
{foreach $locations as $index => $location}
    <li><a id="ezflb-pointer-{$block.id}-{$index}" href="{$location.url_alias|ezurl('no')}">{$location.name|wash()}</a></li>
{/foreach}
</ul>

<div id="ezflb-map-{$block.id}" style="width: {$width}px; height: {$height}px"></div>