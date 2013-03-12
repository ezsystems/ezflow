function eZFLGMapView( blockId, data )
{
    if (GBrowserIsCompatible()) 
    {
        var startPoint = new GLatLng( 0, 0 ), zoom = 0;

        var map = new GMap2( document.getElementById( 'ezflb-map-' + blockId ) ), 
            bounds = new GLatLngBounds();

        map.addControl( new GMapTypeControl() );
        map.addControl( new GLargeMapControl() );
        map.setCenter( startPoint, zoom );

        for( var i = 0; i < data.length; i++ )
        {
            var locationData = data[i];
            var pointerHandler = document.getElementById( 'ezflb-pointer-' + blockId + '-' + i );
            pointerHandler.point = locationData.point;
            pointerHandler.address = locationData.address;
            eZFLGMapAddListener( pointerHandler, 'click', function(e) {
                map.panTo( this.point );
                map.setCenter( this.point, 13 );
                map.openInfoWindowHtml( this.point, this.address );
                if ( e.preventDefault )
                    e.preventDefault();
                else
                    e.returnValue = false;
            } );
            map.addOverlay( eZFLGMapMarker( locationData.point, bounds, locationData.address ) );
        }

        map.setCenter( bounds.getCenter(), map.getBoundsZoomLevel( bounds ) );
    }
}

function eZFLGMapMarker( point, bounds, address )
{
    var marker = new GMarker( point );

    GEvent.addListener( marker, 'click', function() {
        marker.openInfoWindowHtml( address );
    } );

    if ( bounds )
        bounds.extend( point );

    return marker;
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