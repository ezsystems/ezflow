{def $root_node = ezini('PushToBlock', 'RootSubtree', 'block.ini')
     $classes = ezini('PushToBlock', 'ContentClasses', 'block.ini')
     $frontpage_list = fetch( 'content', 'tree', hash( 'parent_node_id', $root_node,
                                                       'class_filter_type', 'include',
                                                       'class_filter_array', $classes ))}
<div id="page-datatype-container" class="yui-skin-sam yui-skin-ezflow">

<form method="post" action="{concat('ezflow/push/', $node.node_id)|ezurl('no')}">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Choose placement for "%node_name"'|i18n('design/admin/page/push', , hash( '%node_name', $node.name|wash() ) )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<div class="block">

<input type="button" id="select-frontpage-button" name="SelectFrontpageButton" value="{'Select frontpage'|i18n('design/admin/page/push')}" />
<select id="select-frontpage-list" name="SelectFrontpageList">
    {foreach $frontpage_list as $frontpage}
    <option value="{$frontpage.node_id}">{$frontpage.name|wash()}</option>
    {/foreach}
</select>

<input type="button" id="select-zone-button" name="SelectZoneButton" value="{'Select zone'|i18n('design/admin/page/push')}" />
<select id="select-zone-list" name="SelectZoneList">
</select>

<input type="button" id="select-block-button" name="SelectBlockButton" value="{'Select block'|i18n('design/admin/page/push')}" />
<select id="select-block-list" name="SelectBlockList">
</select>

<input type="button" id="placement-button" name="PlacementButton" value="{'Add'|i18n('design/admin/page/push')}" /> 

<h2>Placement list</h2>

<table id="placement-list">
    <tbody>
        
    </tbody>
</table>
<p> </p>
<input type="button" id="placement-remove-button" name="PlacementRemoveButton" value="{'Remove'|i18n('design/admin/page/push')}" /> 

<input type="submit" id="placement-store-button" name="PlacementStoreButton" value="{'Store'|i18n('design/admin/page/push')}" /> 

</div>

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>

</form>

</div>

<script type="text/javascript">
(function() {ldelim}
    {literal}
    YUILoader.onSuccess = function() {
        
            var oPlacementStoreButton = new YAHOO.widget.Button("placement-store-button");

            var oPlacementRemoveButton = new YAHOO.widget.Button("placement-remove-button");

            var oPlacementButton = new YAHOO.widget.Button("placement-button");

            var oFrontpageButton = new YAHOO.widget.Button("select-frontpage-button", { 
                                                    type: "menu", 
                                                    menu: "select-frontpage-list" });


            var oZoneButton = new YAHOO.widget.Button("select-zone-button", { 
                                                    type: "menu", 
                                                    menu: "select-zone-list" });

            var oBlockButton = new YAHOO.widget.Button("select-block-button", { 
                                                    type: "menu", 
                                                    menu: "select-block-list" });

            var clearMenuContent = function(b) {
                var oMenu = b.getMenu();
                oMenu.clearContent();
                oMenu.render( b );
            }

            var handleRequest = function(p, b) {
                var handleSuccess = function(o) {
                    if(o.responseText !== undefined) {
                        var aResponse = YAHOO.lang.JSON.parse( o.responseText );
                        var oMenu  = b.getMenu();
                        clearMenuContent( b );

                        for(var i = 0; i < aResponse.length; i++) {
                            var oResItem = aResponse[i];
                            oMenu.addItem( {text: oResItem.name, value: oResItem.id} );
                        }

                        oMenu.render( b );
                    }
                }

                var callback =
                {
                  success: handleSuccess
                };

                var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, p);
            }

            {/literal}
            
            var sUrl = "{'ezflow/get'|ezurl('no')}";
            {literal}

            oFrontpageButton.getMenu().subscribe("click", function (p_sType, p_aArgs) {
                var oEvent = p_aArgs[0],
                oMenuItem = p_aArgs[1];

                oZoneButton.set( "label", "Select zone" );
                oBlockButton.set( "label", "Select block" );
                clearMenuContent( oBlockButton );

                if (oMenuItem) {
                    var sPostData = "content=frontpage&node_id=" + oMenuItem.value; 

                    handleRequest( sPostData, oZoneButton );

                    oFrontpageButton.set( "label", oMenuItem.cfg.getProperty("text") );
                }
            });

            oZoneButton.getMenu().subscribe("click", function (p_sType, p_aArgs) {
                var oEvent = p_aArgs[0],
                oMenuItem = p_aArgs[1];

                if (oMenuItem) {
                    oZoneButton.set( "label", oMenuItem.cfg.getProperty("text") );
                    var nodeID = oFrontpageButton.get("selectedMenuItem").value;
                    var sPostData = "content=zone&node_id=" + nodeID + "&zone=" + oMenuItem.value;

                    handleRequest( sPostData, oBlockButton );

                    oZoneButton.set( "label", oMenuItem.cfg.getProperty("text") );
                }
            });

            oBlockButton.getMenu().subscribe("click", function (p_sType, p_aArgs) {
                var oEvent = p_aArgs[0],
                oMenuItem = p_aArgs[1];

                if (oMenuItem) {
                    oBlockButton.set( "label", oMenuItem.cfg.getProperty("text") );
                }
            });

            oPlacementButton.on("click", function(e) {
                var oPlacementList = YAHOO.util.Dom.get("placement-list");
                var tBody = YAHOO.util.Dom.getFirstChild(oPlacementList);

                var sFrontpageText = oFrontpageButton.get("selectedMenuItem").cfg.getProperty("text");
                var sZoneText = oZoneButton.get("selectedMenuItem").cfg.getProperty("text");
                var sBlockText = oBlockButton.get("selectedMenuItem").cfg.getProperty("text");

                var sID = "id-" + oFrontpageButton.get("selectedMenuItem").value + "-" + oZoneButton.get("selectedMenuItem").value + "-" + oBlockButton.get("selectedMenuItem").value;

                var oCurrTr = YAHOO.util.Dom.get( sID );

                if (oCurrTr === null) {
                    var oTr = document.createElement("tr");
                    oTr.id = sID;

                    var oTdInput = document.createElement("td");
                    var oInput = document.createElement("input");
                    oInput.type = "checkbox";
                    oInput.name = "Remove"; // TODO: add the correct name

                    oTdInput.appendChild( oInput );

                    var oTdPlacement = document.createElement("td");
                    oTdPlacement.appendChild( document.createTextNode( sFrontpageText + " / " + sZoneText + " / " + sBlockText  ) );

                    var oImg = document.createElement("img");
                    oImg.className = "schedule-handler";
        {/literal}
                    oImg.alt = "{$node.name|wash()|shorten( '50' )}";
                    oImg.title = "{$node.name|wash()|shorten( '50' )}";
                    oImg.src = "{'ezpage/clock_ico.gif'|ezimage('no')}";
        {literal}

                    var oSpan = document.createElement("span");
                    oSpan.className = "ts-publication";

                    var oTSInput = document.createElement("input");
                    oTSInput.type = "hidden";
                    oTSInput.value = Math.round( new Date().getTime() / 1000 );
                    oTSInput.name = "PlacementTSArray[" + oFrontpageButton.get("selectedMenuItem").value + "][" + oZoneButton.get("selectedMenuItem").value + "][" + oBlockButton.get("selectedMenuItem").value + "]";

                    oTdPlacement.appendChild(oSpan);
                    oTdPlacement.appendChild(oTSInput);
                    oTdPlacement.appendChild(oImg);

                    oTr.appendChild( oTdInput );
                    oTr.appendChild( oTdPlacement );

                    tBody.appendChild( oTr );

                    YAHOO.ez.sheduleDialog.init();
                }        
            });

            oPlacementRemoveButton.on("click", function(e) {
                var oPlacementList = YAHOO.util.Dom.get("placement-list");
                var tBody = YAHOO.util.Dom.getFirstChild(oPlacementList);

                var aInput = YAHOO.util.Dom.getElementsBy( function(e) {
                    if ( e.type === "checkbox" && e.checked ) {
                        return true;
                    }
                }, "input", oPlacementList );

                for( var i = 0; i < aInput.length; i++ ) {
                    var oInput = aInput[i];

                    var oTr = YAHOO.util.Dom.getAncestorByTagName(oInput, "tr");
                    tBody.removeChild( oTr );
                }
            });
    }
    {/literal}
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

    YUILoader.require(["button","menu","calendar","container","json","utilities","scheduledialog","scheduledialog-css"]);
    YUILoader.insert();
{rdelim})();
</script>