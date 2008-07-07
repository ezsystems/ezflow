YAHOO.namespace("ez");

YAHOO.ez.BlockDDApp = function() {

    var cfg = {};
    var Dom = YAHOO.util.Dom;
    var Event = YAHOO.util.Event;
    var DDM = YAHOO.util.DragDropMgr;

    YAHOO.ez.DDList = function(id, sGroup, config) {
        YAHOO.ez.DDList.superclass.constructor.call(this, id, sGroup, config);

        var el = this.getDragEl();
        Dom.setStyle(el, "opacity", 0.67);

        this.goingUp = false;
        this.lastY = 0;
    };

    YAHOO.extend(YAHOO.ez.DDList, YAHOO.util.DDProxy, {

        startDrag: function(x, y) {
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

            Dom.setStyle(proxy, "visibility", "");
            var a = new YAHOO.util.Motion( 
                proxy, { 
                    points: { 
                        to: Dom.getXY(srcEl)
                    }
                }, 
                0.2, 
                YAHOO.util.Easing.easeOut);
            var proxyid = proxy.id;
            var thisid = this.id;

            a.onComplete.subscribe(function() {
                Dom.setStyle(proxyid, "visibility", "hidden");
                Dom.setStyle(thisid, "visibility", "");
            });
            a.animate();

            var tableBody = srcEl.parentNode;
            var postData = "";
            var items = Dom.getElementsByClassName("handler", "td", tableBody);
            
            for (i=0;i<items.length;i++) {
                postData += "Items%5B%5D=" + items[i].id + "&";
            }

            var tableID = tableBody.parentNode.id;

            postData += "Block=" + tableID + "&ContentObjectAttributeID=" + cfg.attributeid + "&Version=" + cfg.version;

            YAHOO.util.Connect.asyncRequest( 'POST', cfg.url, '', postData );
        },

        onDragDrop: function(e, id) {
            if (DDM.interactionInfo.drop.length === 1) {
                var pt = DDM.interactionInfo.point; 
                var region = DDM.interactionInfo.sourceRegion; 

                if (!region.intersect(pt)) {
                    var destEl = Dom.get(id);
                    var srcEl = this.getEl();
                    var destDD = DDM.getDDById(id);
                    var srcTargetID = srcEl.parentNode.parentNode.id;
                    var destTargetID = destEl.parentNode.parentNode.id;
                
                    if(srcTargetID == destTargetID) {
                        destEl.appendChild(this.getEl());
                        destDD.isEmpty = false;
                    }
                
                    DDM.refreshCache();
                }
            }
        },

        onDrag: function(e) {
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
            var srcTargetID = srcEl.parentNode.parentNode.id;
            var destTargetID = destEl.parentNode.parentNode.id;

            if (destEl.nodeName.toLowerCase() == "tr" && 
                srcTargetID == destTargetID) {
                var orig_p = srcEl.parentNode;
                var p = destEl.parentNode;

                if (this.goingUp) {
                    p.insertBefore(srcEl, destEl);
                } else {
                    p.insertBefore(srcEl, destEl.nextSibling);
                }

                DDM.refreshCache();
            }
        }
    });

    return {
        
        init: function() {
            this.initCfg();
            Event.onAvailable("zone-content-container", this.initDragHandlers, null, null, true);
        },
        
        initDragHandlers: function() {
            var qTable = Dom.getElementsByClassName("queue", "table", "zone-content-container");
            var oTable = Dom.getElementsByClassName("online", "table", "zone-content-container");
        
            for(var i = 0; i < qTable.length; i+=1) {
                new YAHOO.util.DDTarget(qTable[i].id);
                var qItems = Dom.getElementsByClassName("handler", "td", qTable[i].id);

                for(var j = 0; j < qItems.length; j+=1) {
                    new YAHOO.ez.DDList(qItems[j].parentNode.id);
                }
            }
            for(var i = 0; i < oTable.length; i+=1) {
                new YAHOO.util.DDTarget(oTable[i].id);
                var oItems = Dom.getElementsByClassName("handler", "td", oTable[i].id);

                for(var j = 0; j < oItems.length; j+=1) {
                    new YAHOO.ez.DDList(oItems[j].parentNode.id);
                }
            }
        },
        
        initCfg: function() {
            cfg = this.cfg;
        },
        
        cfg: {}
    };

}();