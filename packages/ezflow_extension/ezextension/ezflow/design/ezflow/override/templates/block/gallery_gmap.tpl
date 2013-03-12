<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={ezini('SiteSettings','GMapsKey')}" type="text/javascript"></script>
<script type="text/javascript">
    var gmapExistingOnload{$block.id} = null;
    var gmapExistingOnUnload{$block.id} = null;

    /// used as default map center for series maps if no data passed
    var gmapStartLat = 48.856558; // Paris!
    var gmapStartLong = 2.350966; // Paris!
    var gmapStartZoomLvl = 0; // whole world

{literal}
// prevent declaring the same functions many times over, if many blocks are
// inside the same page
if (window.loadPointMap === undefined) {

    /**
     *
     * @access public
     * @return void
     **/
    function loadPointMap(elementid, lat, long, zoomlevel, maptype){
        var map = new GMap2(document.getElementById(elementid));
        map.setCenter(new GLatLng(lat, long));
        if (zoomlevel !== undefined) {
            map.setZoom(zoomlevel);
        }
        if (maptype !== undefined) {
            map.setMapType(maptype);
        }
    }

    /**
     *
     * @access public
     * @return void
     **/
    function loadSeriesMap(elementid, series, maptype){
        var map = new GMap2(document.getElementById(elementid));
        map.addControl(new GSmallMapControl());
        map.setCenter(new GLatLng(gmapStartLat, gmapStartLong));
        if (series.length == 0)
        {
            map.setZoom(gmapStartZoomLvl);
        }
        else
        {
        var point;
        for (var i = 0; i < series.length; ++i)
        {
            if (i == 0)
            {
                var bounds = new GLatLngBounds(series[i]['lat'], series[i]['long']);
            }
            else
            {
                point = new GLatLng(series[i]['lat'], series[i]['long']);
                bounds.extend(point);
            }
            var marker = new GMarker(point, {'title': series[i]['title']});
            //var target = series[i]['target'];
            marker.target = series[i]['target'];
            /// @todo find out why closures do not seem to work here...
            GEvent.addListener(marker, "click", function() {
                //window.location.href = target;
                window.location.href = this.target;
            });
            /// @todo add link, info from series array to map marker
            /*GEvent.addListener(marker, "onmouseover", function() {
                marker.openInfoWindowHtml(series[i]['content'];
            });
            GEvent.addListener(marker, "onmouseout", function() {
                marker.closeInfoWindow();
            });
            */
            map.addOverlay(marker);
        }
        map.setCenter(bounds.getCenter());
        map.setZoom(map.getBoundsZoomLevel(bounds));
        }

        if (maptype !== undefined) {
            map.setMapType(maptype);
        }
    }
}

    if (window.onunload)
    {
        //Hang on to any existing onunload function.
        gmapExistingOnUnload{/literal}{$block.id}{literal} = window.onunload;
    }

    window.onunload = function(ev){
        GUnload();
        if (gmapExistingOnUnload{/literal}{$block.id}{literal})
        {
            gmapExistingOnUnload{/literal}{$block.id}{literal}(ev);
        }
    }

    if (window.onload)
    {
        //Hang on to any existing onload function.
        gmapExistingOnload{/literal}{$block.id}{literal} = window.onload;
    }

    window.onload = function(ev){
        //Run any onload that we found.
        if (gmapExistingOnload{/literal}{$block.id}{literal})
        {
            gmapExistingOnload{/literal}{$block.id}{literal}(ev);
        }
        if (GBrowserIsCompatible())
        {
            var sequence = [];
{/literal}
{foreach $items as $index => $item}
{* @todo: this check passes even if lat and long are empty... *}
{if and(is_set($item.data_map.location), $item.data_map.location.has_content)}
{* @todo fix usage of ezurl inside javascript: it breaks on urls containing ' or " char... *}
            sequence[{$index}] = {ldelim}'lat':{$item.data_map.location.content.latitude}, 'long':{$item.data_map.location.content.longitude}, 'title':'{$item.data_map.title.content|wash('javascript')}', 'target':'{$item.url_alias|ezurl(no)}' {rdelim};

{/if}
{/foreach}
            loadSeriesMap('{$block.id}', sequence);
{literal}
        }
    }
{/literal}
</script>

<h2 class="grey_background">{$block_name}</h2>

<!-- @todo style hardcoded from blocks.css, to be put back in there -->
<div id="{$block.id}" class="googleMap" style="width:302px;height:238px;float:left; margin-bottom:14px;"></div>