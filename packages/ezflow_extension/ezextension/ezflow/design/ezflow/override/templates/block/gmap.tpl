{def $key = $block.custom_attributes.key
     $location = $block.custom_attributes.location}

{ezscript_require( 'ezjsc::yui3' )}

<h1>{$block.name|wash()}</h1>

<div id="map-container-{$block.id}" class="map-container"></div>

{* 
    Do not load GMap API if key is empty. 
    Option to skip loading GMap API in case when it was loaded globally e.g in <head> section
*}
{if ne( $key, '' )}
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key={$key}" type="text/javascript"></script>
{/if}

<script type="text/javascript">
YUI(YUI3_config).use('event', function(Y) {ldelim}
    Y.on('domready', function() {ldelim}
        if (GBrowserIsCompatible()) {ldelim}
            var mapContainer = document.getElementById("map-container-{$block.id}");
            var map = new GMap2(mapContainer);
            var geocoder = new GClientGeocoder();
            geocoder.getLatLng("{$location}", function(point) {ldelim}
                if (point) {ldelim}
                    map.setCenter(point, 13);
                    var marker = new GMarker(point);
                    map.addOverlay(marker);
                    marker.openInfoWindowHtml("{$location}");
                    {rdelim}
                {rdelim});
            {rdelim}
        {rdelim});
{rdelim});
</script>

{undef $key $location}