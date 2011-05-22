{def $key = $block.custom_attributes.key
     $location = $block.custom_attributes.location}
<h1>{$block.name|wash()}</h1>
{run-once}
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
{/run-once}
{run-once}
<script type="text/javascript">
function eZGmapLocation_MapView( attributeId, location ){ldelim}
        var myLatlng = new google.maps.LatLng(0,0);
        var myOptions = {ldelim}
              zoom: 0,
              center: myLatlng,
              mapTypeId: google.maps.MapTypeId.ROADMAP
        {rdelim}
        var mapgmap = new google.maps.Map(document.getElementById( attributeId ), myOptions);
        geocoder = new google.maps.Geocoder();
        geocoder.geocode({ldelim} 'address': location{rdelim}, function(results, status) {ldelim}
          if (status == google.maps.GeocoderStatus.OK) {ldelim}
            mapgmap.setCenter(results[0].geometry.location);
            mapgmap.setZoom(13);
            var marker = new google.maps.Marker({ldelim}
                map: mapgmap, 
                position: results[0].geometry.location
            {rdelim});
          {rdelim}
        {rdelim});
        google.maps.event.addListener(marker, 'click', function() {ldelim}
             window.location.href={$location.url_alias|ezurl()};  
        {rdelim});
{rdelim}    
</script>
{/run-once}
<script type="text/javascript">
<!--
if ( window.addEventListener )
    window.addEventListener('load', function(){ldelim} eZGmapLocation_MapView( "map-container-{$block.id}","{$location}" ) {rdelim}, false);
else if ( window.attachEvent )
    window.attachEvent('onload', function(){ldelim} eZGmapLocation_MapView( "map-container-{$block.id}", "{$location}" ) {rdelim} );
-->
</script>
<div id="map-container-{$block.id}" class="map-container"></div>