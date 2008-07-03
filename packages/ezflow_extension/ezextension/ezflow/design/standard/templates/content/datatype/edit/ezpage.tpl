<script src={"javascript/utilities/utilities.js"|ezdesign} type="text/javascript" language="javascript"></script>
<script src={"javascript/tabview/tabview-min.js"|ezdesign} type="text/javascript" language="javascript"></script>

{def $zone_id = ''
     $block_id = ''
     $item_id = ''
     $zone_names = ezini( $attribute.content.zone_layout, 'ZoneName', 'zone.ini' )}

<div class="zones float-break">
{foreach ezini( 'General', 'AllowedTypes', 'zone.ini' ) as $allowed_type}
{if ezini( $allowed_type, 'AvailableForClasses', 'zone.ini' )|contains( $attribute.object.content_class.identifier )}
    <div class="zone">
        <div class="zone-label">{ezini( $allowed_type, 'ZoneTypeName', 'zone.ini' )}</div>
        <div class="zone-thumbnail"><img src={concat( "ezpage/thumbnails/", ezini( $allowed_type, 'ZoneThumbnail', 'zone.ini' ) )|ezimage()} /></div>
        <div class="zone-selector"><input type="radio" name="ContentObjectAttribute_ezpage_zone_allowed_type_{$attribute.id}" value="{$allowed_type}" {if eq( $allowed_type, $attribute.content.zone_layout)}checked="checked"{/if} /></div>
    </div>
{/if}
{/foreach}
    <div class="break"></div>

    <div class="block">
        <input class="button" type="submit" onclick="return confirmDiscard( 'Are you sure you want to change zone layout? Existing structure will be destroyed!' );" name="CustomActionButton[{$attribute.id}_new_zone_layout]" value="Set layout" />
    </div>
</div>

<div id="container"></div>
<script type="text/javascript">

function setCookie( cookieName, cookieValue, nDays )
{ldelim}
    var today = new Date();
    var expire = new Date();

    if ( nDays == null || nDays == 0 ) 
        nDays = 1;

    expire.setTime( today.getTime() + 3600000 * 24 * nDays );

    document.cookie = cookieName + "=" + escape( cookieValue ) + ";expires=" + expire.toGMTString();
{rdelim}

function getCookie( cookieName )
{ldelim}
    var theCookie = "" + document.cookie;
    var ind = theCookie.indexOf( cookieName );

    if ( ind == -1 || cookieName == "" ) 
        return ""; 

    var ind1 = theCookie.indexOf( ';', ind );

    if ( ind1 == -1 )
        ind1 = theCookie.length; 

    return unescape( theCookie.substring( ind + cookieName.length + 1, ind1 ) );
{rdelim}

    var tabView = new YAHOO.widget.TabView();
{foreach $attribute.content.zones as $index => $zone}
    {if and( is_set( $zone.action ), eq( $zone.action, 'remove' ) )}
        {skip}
    {/if}
    tabView.addTab( new YAHOO.widget.Tab({ldelim}
        label: '{$zone_names[$zone.zone_identifier]}',
        dataSrc: '{concat( '/ezflow/zone/', $attribute.id, '/', $attribute.version, '/', $index  )|ezurl(no)}',
        cacheData: true
        {rdelim}));
{/foreach}

var activeTabIndex = getCookie( 'eZPageActiveTabIndex' );
if ( activeTabIndex )
{ldelim}
    if ( tabView.getTab( activeTabIndex ) )
        tabView.set( 'activeIndex',  activeTabIndex );
    else
        tabView.set( 'activeIndex', 0 );
{rdelim}
else
{ldelim}
    tabView.set( 'activeIndex', 0 );
{rdelim}

tabView.addListener( 'activeTabChange', handler );

function handler(e)
{ldelim}
    var tabIndex = tabView.getTabIndex( e.newValue );
    setCookie( 'eZPageActiveTabIndex', tabIndex, 1 );
{rdelim}
    tabView.appendTo('container');
</script>

<script type="text/javascript"><!--
{literal}

(function() {

var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var DDM = YAHOO.util.DragDropMgr;

{/literal}
// app

YAHOO.example.DDApp = {ldelim}
    init: function() {ldelim}
{foreach $attribute.content.zones as $zone_id => $zone}
    {if and( is_set( $zone.blocks ), $zone.blocks|count() )}
    {foreach $zone.blocks as $block_id => $block}
        new YAHOO.util.DDTarget("z:{$zone_id}_b:{$block_id}_q");

        {foreach $block.waiting as $item}
            dd{$zone_id}{$block_id}{$item.object_id} = new YAHOO.example.DDList("z:{$zone_id}_b:{$block_id}_i:{$item.object_id}");

            dd{$zone_id}{$block_id}{$item.object_id}.setHandleElId("z:{$zone_id}_b:{$block_id}_i:{$item.object_id}_h");
        {/foreach}

    {/foreach}
    {/if}
{/foreach}

    {rdelim}

{rdelim};
{literal}
// custom drag and drop implementation

YAHOO.example.DDList = function(id, sGroup, config) {

    YAHOO.example.DDList.superclass.constructor.call(this, id, sGroup, config);

    this.logger = this.logger || YAHOO;
    var el = this.getDragEl();
    Dom.setStyle(el, "opacity", 0.67); // The proxy is slightly transparent

    this.goingUp = false;
    this.lastY = 0;
};

YAHOO.extend(YAHOO.example.DDList, YAHOO.util.DDProxy, {

    startDrag: function(x, y) {
        this.logger.log(this.id + " startDrag");

        // make the proxy look like the source element
        var dragEl = this.getDragEl();
        var clickEl = this.getEl();
        Dom.setStyle(clickEl, "visibility", "hidden");

        dragEl.innerHTML = clickEl.innerHTML;

        Dom.setStyle(dragEl, "color", Dom.getStyle(clickEl, "color"));
        Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor"));
        Dom.setStyle(dragEl, "border", "2px solid gray");
    },

    endDrag: function(e) {

        var srcEl = this.getEl();
        var proxy = this.getDragEl();

        // Show the proxy element and animate it to the src element's location
        Dom.setStyle(proxy, "visibility", "");
        var a = new YAHOO.util.Motion( 
            proxy, { 
                points: { 
                    to: Dom.getXY(srcEl)
                }
            }, 
            0.2, 
            YAHOO.util.Easing.easeOut 
        )
        var proxyid = proxy.id;
        var thisid = this.id;

        // Hide the proxy and show the source element when finished with the animation
        a.onComplete.subscribe(function() {
                Dom.setStyle(proxyid, "visibility", "hidden");
                Dom.setStyle(thisid, "visibility", "");
            });
        a.animate();

        var url;
        var tableBody = srcEl.parentNode;
        var postData = "";
        var items = tableBody.getElementsByTagName("tr");
        for (i=0;i<items.length;i=i+1) {
            postData += "Items%5B%5D=" + items[i].id + "&";
        }

        var tableID = tableBody.parentNode.id;
{/literal}
        postData += 'Block=' + tableID + '&ContentObjectAttributeID=' + {$attribute.id} + '&Version=' + {$attribute.version};
        url = "{'/ezflow/request'|ezurl(no)}";
        YAHOO.util.Connect.asyncRequest( 'POST', url, false, postData );
{literal}

    },

    onDragDrop: function(e, id) {

        // If there is one drop interaction, the li was dropped either on the list,
        // or it was dropped on the current location of the source element.
        if (DDM.interactionInfo.drop.length === 1) {

            // The position of the cursor at the time of the drop (YAHOO.util.Point)
            var pt = DDM.interactionInfo.point; 

            // The region occupied by the source element at the time of the drop
            var region = DDM.interactionInfo.sourceRegion; 

            // Check to see if we are over the source element's location.  We will
            // append to the bottom of the list once we are sure it was a drop in
            // the negative space (the area of the list without any list items)
            if (!region.intersect(pt)) {
                var destEl = Dom.get(id);
                var destDD = DDM.getDDById(id);
                destEl.appendChild(this.getEl());
                destDD.isEmpty = false;
                DDM.refreshCache();
            }

        }
    },

    onDrag: function(e) {

        // Keep track of the direction of the drag for use during onDragOver
        var y = Event.getPageY(e);

        if (y < this.lastY) {
            this.goingUp = true;
        } else if (y > this.lastY) {
            this.goingUp = false;
        }

        this.lastY = y;
    },

    onDragOver: function(e, id) {
    
        var srcEl = this.getEl();
        var destEl = Dom.get(id);

        // We are only concerned with list items, we ignore the dragover
        // notifications for the list.
        if (destEl.nodeName.toLowerCase() == "tr") {
            var orig_p = srcEl.parentNode;
            var p = destEl.parentNode;

            if (this.goingUp) {
                p.insertBefore(srcEl, destEl); // insert above
            } else {
                p.insertBefore(srcEl, destEl.nextSibling); // insert below
            }

            DDM.refreshCache();
        }
    }
});

Event.onDOMReady(YAHOO.example.DDApp.init, YAHOO.example.DDApp, true);

})();


function confirmDiscard( question )
{
    // Ask user if he really wants to do it.
    return confirm( question );
}

{/literal}
--></script>
