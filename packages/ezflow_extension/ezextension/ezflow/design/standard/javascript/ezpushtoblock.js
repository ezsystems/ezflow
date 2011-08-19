eZPushToBlock = function() {
    
    var oPlacementStoreButton,
        oPlacementRemoveButton,
        oPlacementButton,
        oFrontpageButton,
        oZoneButton,
        oBlockButton;

    var ret = {};

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

        var _tokenNode = document.getElementById('ezxform_token_js');
 	    if ( _tokenNode ) {
            if ( p ) {
                p = p + '&';
            }
            p = p + 'ezxform_token=' + _tokenNode.getAttribute('title');
        }

        var request = YAHOO.util.Connect.asyncRequest('POST', ret.cfg.requesturl, callback, p);
    }

    var handleFButtonClick = function (p_sType, p_aArgs) {
        var oEvent = p_aArgs[0],
            oMenuItem = p_aArgs[1];

        oZoneButton.set( "label", "Select zone" );
        oBlockButton.set( "label", "Select block" );
        clearMenuContent( oBlockButton );

        if (oMenuItem) {
            var sPostData = "content=frontpage&frontpage_node_id=" + oMenuItem.value;

            handleRequest( sPostData, oZoneButton );

            oFrontpageButton.set( "label", oMenuItem.cfg.getProperty("text") );
        }
    }

    var handleZButtonClick = function (p_sType, p_aArgs) {
        var oEvent = p_aArgs[0],
            oMenuItem = p_aArgs[1];

        if (oMenuItem) {
            oZoneButton.set( "label", oMenuItem.cfg.getProperty("text") );
            var nodeID = oFrontpageButton.get("selectedMenuItem").value;
            var sPostData = "content=zone&frontpage_node_id=" + nodeID + "&zone=" + oMenuItem.value + "&node_id=" + ret.cfg.nodeid;

            handleRequest( sPostData, oBlockButton );

            oZoneButton.set( "label", oMenuItem.cfg.getProperty("text") );
        }
    }

    var handleBButtonClick = function (p_sType, p_aArgs) {
        var oEvent = p_aArgs[0],
            oMenuItem = p_aArgs[1];

        if (oMenuItem) {
            oBlockButton.set( "label", oMenuItem.cfg.getProperty("text") );
        }
    }

    var handlePButtonClick = function(e) {
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
            oImg.alt = ret.cfg.nodename;
            oImg.title = ret.cfg.nodename;
            oImg.src = ret.cfg.imagepath;

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
    }

    var handlePRButtonClick = function(e) {
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
    }

    ret.cfg = {};
    
    ret.init = function() {
        oPlacementStoreButton = new YAHOO.widget.Button("placement-store-button");

        oPlacementRemoveButton = new YAHOO.widget.Button("placement-remove-button");
        oPlacementRemoveButton.on("click", handlePRButtonClick);
        
        oPlacementButton = new YAHOO.widget.Button("placement-button");
        oPlacementButton.on("click", handlePButtonClick);

        oFrontpageButton = new YAHOO.widget.Button("select-frontpage-button", { 
                                                type: "menu", 
                                                menu: "select-frontpage-list" });
        oFrontpageButton.getMenu().subscribe("click", handleFButtonClick);

        oZoneButton = new YAHOO.widget.Button("select-zone-button", { 
                                                type: "menu", 
                                                menu: "select-zone-list" });
        oZoneButton.getMenu().subscribe("click", handleZButtonClick);
        
        oBlockButton = new YAHOO.widget.Button("select-block-button", { 
                                                type: "menu", 
                                                menu: "select-block-list" });
        oBlockButton.getMenu().subscribe("click", handleBButtonClick);
    }
    
    return ret;
    
}();
