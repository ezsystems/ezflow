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
    <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={$key}&sensor={ezini('GMap', 'UseSensor', 'block.ini')}"></script>
{/if}

<script type="text/javascript">
YUI(YUI3_config).use('event', function(Y) {ldelim}
    Y.on('domready', function() {ldelim}

        var mapContainer = document.getElementById("map-container-{$block.id}");

        var latlng = new google.maps.LatLng(-34.397, 150.644);

        var mapOptions = {ldelim}
            zoom: 12,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        {rdelim};

        var map = new google.maps.Map(mapContainer, mapOptions);

        var geocoder = new google.maps.Geocoder();

        geocoder.geocode( {ldelim} 'address': "{$location|wash('javascript')}" {rdelim}, function(results, status) {ldelim}
            if (status == google.maps.GeocoderStatus.OK) {ldelim}
                map.setCenter(results[0].geometry.location);
                var marker = new google.maps.Marker({ldelim}
                    map: map,
                    position: results[0].geometry.location
                {rdelim});
            {rdelim} else {ldelim}
                alert("Geocode was not successful for the following reason: " + status);
            {rdelim}
        });

    {rdelim});
{rdelim});
</script>

{undef $key $location}