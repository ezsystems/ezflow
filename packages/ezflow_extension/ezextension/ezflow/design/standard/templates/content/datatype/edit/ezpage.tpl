<script type="text/javascript" src={"lib/yui/2.6.0/build/utilities/utilities.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/cookie/cookie-min.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/tabview/tabview-min.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/get/get-min.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/button/button-min.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/container/container-min.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/calendar/calendar-min.js"|ezdesign}></script>
<script type="text/javascript" src={"lib/yui/2.6.0/build/json/json.js"|ezdesign}></script>
<script type="text/javascript" src={"javascript/blocktools.js"|ezdesign}></script>
<script type="text/javascript" src={"javascript/zonetools.js"|ezdesign}></script>
<script type="text/javascript" src={"javascript/scheduledialog.js"|ezdesign}></script>
{def $zone_id = ''
     $block_id = ''
     $item_id = ''
     $zone_names = ezini( $attribute.content.zone_layout, 'ZoneName', 'zone.ini' )
     $allowed_zones = fetch('ezflow', 'allowed_zones')}

<div id="page-datatype-container" class="yui-skin-sam yui-skin-ezflow">
<div class="zones float-break">
{foreach $allowed_zones as $allowed_zone}
{if $allowed_zone['classes']|contains( $attribute.object.content_class.identifier )}
    <div class="zone">
        <div class="zone-label">{$allowed_zone['name']|wash()}</div>
        <div class="zone-thumbnail"><img src={concat( "ezpage/thumbnails/", $allowed_zone['thumbnail'] )|ezimage()} alt="{$allowed_zone['name']|wash()}" /></div>
        <div class="zone-selector">
            <input type="radio" class="zone-type-selector" name="ContentObjectAttribute_ezpage_zone_allowed_type_{$attribute.id}" value="{$allowed_zone['type']}" {if eq( $allowed_zone['type'], $attribute.content.zone_layout )}checked="checked"{/if} />
        </div>
    </div>
{/if}
{/foreach}
    <div class="break"></div>

    <div id="zone-map-container" class="hidden float-break">
        <div id="zone-map-type"></div>
        <p>{'The total number of zones in the new layout is less than the number of zones in the previous layout. Therefore, you must map the previous zones to new zones. Unmapped zones will be removed!'|i18n( 'design/standard/datatype/ezpage' )}</p>
        <div id="zone-map-placeholder"></div>
    </div>

    <div class="block">
        <input id="set-zone-layout" class="button" type="submit" name="CustomActionButton[{$attribute.id}_new_zone_layout]" value="{'Set layout'|i18n( 'design/standard/datatype/ezpage' )}" />
    </div>
    <input type="hidden" class="current-zone-count" name="ContentObjectAttribute_ezpage_zone_count_{$attribute.id}" value="{$attribute.content.zones|count()}" />
</div>

<div id="zone-tabs-container"></div>
</div>
<script type="text/javascript">
YAHOO.ez.ZoneLayout.cfg = {ldelim} 'allowedzones': '{$allowed_zones|json()}',
                                   'zonelayout': '{$attribute.content.zone_layout}' {rdelim};
YAHOO.ez.ZoneLayout.init();
{literal}

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