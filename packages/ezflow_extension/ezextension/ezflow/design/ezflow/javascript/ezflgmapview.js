function eZFLGMapView( blockId, data ){
    var bounds = new google.maps.LatLngBounds();

    var map = new google.maps.Map(
        document.getElementById('ezflb-map-' + blockId), {
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

    for(var i = 0; i < data.length; i++){
        var locationData = data[i];

        var pointerHandler = document.getElementById('ezflb-pointer-' + blockId + '-' + i);
        pointerHandler.point = locationData.point;
        pointerHandler.address = locationData.address;

        eZFLGMapAddListener( pointerHandler, 'click', function(e) {
            map.panTo(this.point);
            map.setCenter(this.point, 13);

            var infoWindow = new google.maps.InfoWindow();
            infoWindow.setContent(this.address);
            infoWindow.setPosition(this.point);
            infoWindow.open(map);

            e.preventDefault();
        } );

        var marker = new google.maps.Marker({
            position: locationData.point,
            map: map
        });

        marker.setMap(map);
        bounds.extend( locationData.point );

        google.maps.event.addListener( marker, 'click', function() {
            var infoWindow = new google.maps.InfoWindow();
            infoWindow.setContent(locationData.address);
            infoWindow.setPosition(locationData.point);
            infoWindow.open(map);
        } );
    }

    map.fitBounds(bounds);
}

function eZFLGMapAddListener( element, type, expression, bubbling )
{
    bubbling = bubbling || false;

    if( window.addEventListener )
    {
        element.addEventListener( type, expression, bubbling );
        return true;
    } 
    else if ( window.attachEvent ) 
    {
        element.attachEvent( 'on' + type, expression );
        return true;
    } 
    else
    {
        return false;
    }
}