<script type="text/javascript" src="http://yui.yahooapis.com/2.5.2/build/logger/logger-min.js"></script>
<script src={"javascript/utilities/utilities.js"|ezdesign} type="text/javascript" language="javascript"></script>
<script src={"javascript/cookie/cookie-beta-min.js"|ezdesign} type="text/javascript" language="javascript"></script>
<script src={"javascript/blockddapp.js"|ezdesign} type="text/javascript" language="javascript"></script>
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

<div id="zone-tabs-container"></div>
<script type="text/javascript">
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
        YAHOO.ez.BlockDDApp.cfg = {
{/literal} 
        url: "{'ezflow/request'|ezurl('no')}",
        attributeid: {$attribute.id},
        version: {$attribute.version}
{literal} 
        };
        YAHOO.ez.BlockDDApp.init();
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