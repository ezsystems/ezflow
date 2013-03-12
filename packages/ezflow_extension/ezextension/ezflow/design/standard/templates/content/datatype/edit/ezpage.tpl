{def $zone_id = ''
     $block_id = ''
     $item_id = ''
     $zone_names = ezini( $attribute.content.zone_layout, 'ZoneName', 'zone.ini' )
     $allowed_zones = fetch('ezflow', 'allowed_zones')
     $can_change_layout = fetch( 'user', 'has_access_to', hash( 'module', 'ezflow', 'function', 'changelayout' ) )
     $current_user = fetch( 'user', 'current_user' )
     $content_object = fetch( 'content', 'object', hash( 'object_id', $attribute.contentobject_id ) )
     $policies = fetch( 'user', 'user_role', hash( 'user_id', $current_user.contentobject_id ) )
     $layout_for_current_class = false()}

     {foreach $policies as $policy}
        {if and( eq( $policy.moduleName, 'ezflow' ),
                    eq( $policy.functionName, 'changelayout' ),
                        is_array( $policy.limitation ) )}
            {if $policy.limitation[0].values_as_array|contains( $content_object.content_class.id )}
                {set $layout_for_current_class = true()}
            {/if}
        {elseif or( and( eq( $policy.moduleName, '*' ),
                             eq( $policy.functionName, '*' ),
                                 eq( $policy.limitation, '*' ) ),
                    and( eq( $policy.moduleName, 'ezflow' ),
                             eq( $policy.functionName, '*' ),
                                 eq( $policy.limitation, '*' ) ),
                    and( eq( $policy.moduleName, 'ezflow' ),
                             eq( $policy.functionName, 'changelayout' ),
                                 eq( $policy.limitation, '*' ) ) )}
            {set $layout_for_current_class = true()}
        {/if}
     {/foreach}

<div id="page-datatype-container" class="yui-skin-sam yui-skin-ezflow">
{if and( $can_change_layout, $layout_for_current_class )}
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

    <div id="zone-map-container" class="hide float-break">
        <div id="zone-map-type"></div>
        <p>{'The total number of zones in the new layout is less than the number of zones in the previous layout. Therefore, you must map the previous zones to new zones. Unmapped zones will be removed!'|i18n( 'design/standard/datatype/ezpage' )}</p>
        <div id="zone-map-placeholder"></div>
    </div>

    <div class="block">
        <input id="set-zone-layout" class="button" type="submit" name="CustomActionButton[{$attribute.id}_new_zone_layout]" value="{'Set layout'|i18n( 'design/standard/datatype/ezpage' )}" />
    </div>
    <input type="hidden" class="current-zone-count" name="ContentObjectAttribute_ezpage_zone_count_{$attribute.id}" value="{$attribute.content.zones|count()}" />
</div>
{/if}
<div id="zone-tabs-container"></div>
</div>

{ezscript_require( array( 'ezjsc::yui2', 'ezjsc::yui3', 'ezjsc::yui3io' ) )}

<script type="text/javascript">
(function() {ldelim}
    YUILoader.onSuccess = function() {ldelim}
        YAHOO.ez.ZoneLayout.cfg = {ldelim} 'allowedzones': '{$allowed_zones|json()}',
                                           'zonelayout': '{$attribute.content.zone_layout}' {rdelim};
        YAHOO.ez.ZoneLayout.init();

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
                    var cfg = {
        {/literal} 
                        url: "{'ezflow/request'|ezurl('no')}",
                        attributeid: {$attribute.id},
                        version: {$attribute.version},
                        zone: tabView.getTabIndex(this)
        {literal} 
                    };
                    YAHOO.ez.BlockDD.cfg = cfg;
                    YAHOO.ez.BlockDD.init();
                    YAHOO.ez.BlockCollapse.init();
                    YAHOO.ez.sheduleDialog.init();
                    BlockDDInit.cfg = cfg;
                    BlockDDInit();
                }, this, true);
            });
        }

        tabView.on("activeTabChange", function(e) {
            var tabIndex = tabView.getTabIndex( e.newValue );
            YAHOO.util.Cookie.set("eZPageActiveTabIndex", tabIndex, {path: "/"});
            BlockDDInit.cfg.zone = tabIndex;
        });

        tabView.appendTo('zone-tabs-container');
        {/literal}
    {rdelim}
    
    YUILoader.addModule({ldelim}
        name: 'blocktools',
        type: 'js',
        fullpath: '{"javascript/blocktools.js"|ezdesign( 'no' )}'
    {rdelim});

    YUILoader.addModule({ldelim}
        name: 'zonetools',
        type: 'js',
        fullpath: '{"javascript/zonetools.js"|ezdesign( 'no' )}'
    {rdelim});

    YUILoader.addModule({ldelim}
        name: 'scheduledialog',
        type: 'js',
        fullpath: '{"javascript/scheduledialog.js"|ezdesign( 'no' )}'
    {rdelim});

    YUILoader.addModule({ldelim}
        name: 'scheduledialog-css',
        type: 'css',
        fullpath: '{"stylesheets/scheduledialog.css"|ezdesign( 'no' )}'
    {rdelim});

    YUILoader.addModule({ldelim}
        name: 'pagedatatype-css',
        type: 'css',
        fullpath: '{"stylesheets/ezpage/ezpage.css"|ezdesign( 'no' )}'
    {rdelim});

    YUILoader.require(["button","calendar","container","cookie","get","json","tabview","utilities","blocktools","zonetools","scheduledialog","scheduledialog-css", "pagedatatype-css"]);

    // Load the files using the insert() method.
    YUILoader.insert();
{rdelim})();

function confirmDiscard( question )
{ldelim}
    // Ask user if he really wants to do it.
    return confirm( question );
{rdelim}
</script>