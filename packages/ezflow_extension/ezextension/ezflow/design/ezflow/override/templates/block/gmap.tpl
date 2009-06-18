{def $key = $block.custom_attributes.key
     $location = $block.custom_attributes.location}

<h1>{$block.name|wash()}</h1>

<div id="map-container-{$block.id}" class="map-container"></div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key={$key}" type="text/javascript"></script>

<script type="text/javascript">
var GMAP{$block.id} = {ldelim}{rdelim};

YAHOO.util.Event.onDOMReady(GMAP{$block.id}.init = function() {ldelim}
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
</script>

{undef $key $location}