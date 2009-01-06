<script type="text/javascript" src={"lib/yui/2.6.0/build/utilities/utilities.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/cookie/cookie-min.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/tabview/tabview-min.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/get/get-min.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/button/button-min.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/container/container-min.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/calendar/calendar-min.js"|ezdesign}></script>
<script type="text/javascript" src={"javascript/blocktools.js"|ezdesign}></script>
<script type="text/javascript" src={"javascript/scheduledialog.js"|ezdesign}></script>

{def $zone_id = ''
     $block_id = ''
     $item_id = ''
     $zone_names = ezini( $attribute.content.zone_layout, 'ZoneName', 'zone.ini' )}

<div class="zones float-break">
{foreach ezini( 'General', 'AllowedTypes', 'zone.ini' ) as $allowed_type}
{if ezini( $allowed_type, 'AvailableForClasses', 'zone.ini' )|contains( $attribute.object.content_class.identifier )}
    <div class="zone">
        <div class="zone-label">{ezini( $allowed_type, 'ZoneTypeName', 'zone.ini' )}</div>
        <div class="zone-thumbnail"><img src={concat( "ezpage/thumbnails/", ezini( $allowed_type, 'ZoneThumbnail', 'zone.ini' ) )|ezimage()} alt="{ezini( $allowed_type, 'ZoneTypeName', 'zone.ini' )}" /></div>
        <div class="zone-selector"><input type="radio" name="ContentObjectAttribute_ezpage_zone_allowed_type_{$attribute.id}" value="{$allowed_type}" {if eq( $allowed_type, $attribute.content.zone_layout)}checked="checked"{/if} /></div>
    </div>
{/if}
{/foreach}
    <div class="break"></div>

    <div class="block">
        <input class="button" type="submit" onclick="return confirmDiscard( 'Are you sure you want to change zone layout? Existing structure will be destroyed!' );" name="CustomActionButton[{$attribute.id}_new_zone_layout]" value="Set layout" />
    </div>
</div>

<div id="zone-tabs-container"></div>
<script type="text/javascript">
{literal}
var body = YAHOO.util.Dom.getElementsBy(function(){ return true }, "body");
YAHOO.util.Dom.addClass(body, "yui-skin-sam yui-skin-ezflow");

var handlerData = {};
var successHandler = function(oData) {};

{/literal}

var aURLs = [
    "{'lib/yui/2.6.0/build/assets/skins/sam/calendar.css'|ezdesign( 'no' )}",
    "{'lib/yui/2.6.0/build/assets/skins/sam/button.css'|ezdesign( 'no' )}",
    "{'lib/yui/2.6.0/build/assets/skins/ezflow/tabview.css'|ezdesign( 'no' )}",
    "{'lib/yui/2.6.0/build/assets/skins/sam/container.css'|ezdesign( 'no' )}",
    "{'stylesheets/scheduledialog.css'|ezdesign( 'no' )}",
    "{'stylesheets/ezpage/ezpage.css'|ezdesign( 'no' )}"
];

YAHOO.util.Get.css(aURLs, {ldelim}
                onSuccess: successHandler,
                data:   handlerData
{rdelim});

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

{literal}
var activeTabIndex = YAHOO.util.Cookie.get( 'eZPageActiveTabIndex' );

if ( activeTabIndex ) {
    if ( tabView.getTab( activeTabIndex ) ) {
        tabView.set( 'activeIndex',  activeTabIndex );
    }
    else {
        tabView.set( 'activeIndex', 0 );
    }
}
else {
    tabView.set( 'activeIndex', 0 );
}

var tabs = tabView.get("tabs");
for( var i = 0; i < tabs.length; i++ ) {
    tabs[i].on("dataLoadedChange", function(e) {
        YAHOO.util.Event.onContentReady("zone-tabs-container", function() {
            YAHOO.ez.BlockDD.cfg = {
{/literal} 
            url: "{'ezflow/request'|ezurl('no')}",
            attributeid: {$attribute.id},
            version: {$attribute.version}
{literal} 
            };
            YAHOO.ez.BlockDD.init();
            YAHOO.ez.BlockCollapse.init();
            YAHOO.ez.sheduleDialog.init();
        });
    });
}

tabView.on("activeTabChange", function(e) {
    var tabIndex = tabView.getTabIndex( e.newValue );
    YAHOO.util.Cookie.set("eZPageActiveTabIndex", tabIndex);
});

tabView.appendTo('zone-tabs-container');

function confirmDiscard( question )
{
    // Ask user if he really wants to do it.
    return confirm( question );
}

{/literal}
</script>