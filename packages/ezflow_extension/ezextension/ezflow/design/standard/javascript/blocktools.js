YAHOO.namespace("ez");

YAHOO.ez.BlockDD = function() {

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
                //clear the time left of items in rotation queue
                var tableBody = srcEl.parentNode;
                var timeLeft = Dom.getElementsByClassName("rotation-time-left", "span", tableBody);
                for (i=0;i<timeLeft.length;i++){
                    timeLeft[i].innerHTML="";
                }
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
            this.initDragHandlers();
        },
        
        initDragHandlers: function() {
            var qTable = Dom.getElementsByClassName("queue", "table", "zone-tabs-container");
            var oTable = Dom.getElementsByClassName("online", "table", "zone-tabs-container");

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

YAHOO.ez.BlockCollapse = function(){
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event,
        Cookie = YAHOO.util.Cookie;

    var getTriggers = function() {
        var emTriggers = Dom.getElementsByClassName( "trigger", "em", "zone-tabs-container" );
        var aTriggers = Dom.getElementsByClassName( "trigger", "a", "zone-tabs-container" );
        var buttonTriggers = Dom.getElementsByClassName( "trigger", "button", "zone-tabs-container" );
        var triggers = emTriggers.concat(aTriggers).concat(buttonTriggers);

        return triggers;
    };
    
    var exec = function() {
        var triggers = getTriggers();

        for( var i = 0; i < triggers.length; i++ ) {
            var triggerEl = triggers[i];
            
            setTriggerEvent(triggerEl);

            if(triggerEl.nodeName.toLowerCase() === "em") {
                updateBlockView(triggerEl);
            }
        }
    };
    
    var setTriggerEvent = function(o) {
        Event.purgeElement(o);
        Event.on(o, "click", triggerAction, o, true);
    };

    var getBlockContainer = function(o) {
        var currentEl = o;
        var isContainer = false;
        
        while(!isContainer) {
            if( Dom.hasClass(currentEl, "block-container") ) {
                isContainer = true;
            }
            else {
                currentEl = currentEl.parentNode;
            }
        }
        
        return currentEl;
    }

    var getCollapsedEl = function(o) {
        var blockContainer = getBlockContainer(o);
        var collapsedEl = Dom.getElementsByClassName("collapsed", "div", blockContainer)[0];
        
        return collapsedEl;
    };
    
    var getExpandedEl = function(o) {
        var blockContainer = getBlockContainer(o);
        var expandedEl = Dom.getElementsByClassName("expanded", "div", blockContainer)[0];
        
        return expandedEl;
    };
    
    var getBlockID = function(o) {
        var blockContainer = getBlockContainer(o);
        var id = blockContainer.id;
        
        return id;
    };
    
    var expandBlock = function(o) {
        Dom.replaceClass(o,"expand", "collapse" );

        var collapsedEl = getCollapsedEl(o);

        if(collapsedEl) {
            Dom.replaceClass( collapsedEl, "collapsed", "expanded" );
        }
        
        Cookie.setSub("eZPageBlockState", getBlockID(o), "1", {path: "/"});
    };
    
    var collapseBlock = function(o) {
        Dom.replaceClass( o, "collapse", "expand" );
            
        var expandedEl = getExpandedEl(o);
            
        if(expandedEl) {
            Dom.replaceClass( expandedEl, "expanded", "collapsed" );
        }
        
        Cookie.setSub("eZPageBlockState", getBlockID(o), "0", {path: "/"});
    }
    
    var updateBlockView = function(o) {
        var id = getBlockID(o);

        var state = Cookie.getSub("eZPageBlockState", id);

        if(state == "1") {
            expandBlock(o);
        } else {
            collapseBlock(o);
        }
    };
    
    var expandAll = function() {
        var triggers = getTriggers();
        
        for( var i = 0; i < triggers.length; i++ ) {
            var triggerEl = triggers[i];
            
            if(triggerEl.nodeName.toLowerCase() == "em") {
                expandBlock(triggerEl);
            }
        }
    };
    
    var collapseAll = function() {
        var triggers = getTriggers();
        
        for( var i = 0; i < triggers.length; i++ ) {
            var triggerEl = triggers[i];
            
            if(triggerEl.nodeName.toLowerCase() == "em") {
                collapseBlock(triggerEl);
            }
        }
    };
    
    var triggerAction = function(e, triggerEl) {
        if( Dom.hasClass( triggerEl, "expand" ) ) {
            expandBlock(triggerEl);
        }
        else if( Dom.hasClass( triggerEl, "collapse" ) ) {
            collapseBlock(triggerEl);
        }
        else if( Dom.hasClass( triggerEl, "expand-all" ) ) {
            expandAll();
        }
        else if( Dom.hasClass( triggerEl, "collapse-all" ) ) {
            collapseAll();
        }
        Event.preventDefault(e);
    };

    return {
        init: function() {
            exec();
        }
    };
}();

var BlockDDInit = function() {
    YUI( YUI3_config ).use('dd-constrain', 'dd-proxy', 'dd-drop', 'io-ez', function(Y) {
        Y.DD.DDM.on('drop:over', function(e) {
            var drag = e.drag.get('node'), drop = e.drop.get('node');
            
            if (drop.get('tagName').toLowerCase() === 'div' && drop.get('parentNode').get('id') === drag.get('parentNode').get('id') ) {
                if (!goingUp) {
                    var dropSibling = drop.get('nextSibling');
                    if (!dropSibling) {
                        drop.get('parentNode').append(drag);
                    } else {
                        drop.get('parentNode').insertBefore(drag, dropSibling);
                    }
                } else {
                    drop.get('parentNode').insertBefore(drag, drop);
                }
                e.drop.sizeShim();
            }
        });

        Y.DD.DDM.on('drag:drag', function(e) {
            var y = e.target.lastXY[1];

            if (y < lastY) {
                goingUp = true;
            }
            else {
                goingUp = false;
            }

            lastY = y;
        });

        Y.DD.DDM.on('drag:start', function(e) {
            var drag = e.target;

            drag.get('node').setStyle('opacity', '.25');
            drag.get('dragNode').appendChild( Y.Node.create( '<div></div>' ).addClass( 'block-container' ).set('innerHTML', drag.get('node').get('innerHTML') ) );
            drag.get('dragNode').setStyles({
                opacity: '.5',
                borderColor: drag.get('node').getStyle('borderColor'),
                backgroundColor: drag.get('node').getStyle('backgroundColor')
            });
        });

        Y.DD.DDM.on('drag:end', function(e) {
            var drag = e.target;

            drag.get('node').setStyles({
                visibility: '',
                opacity: '1'
            });
            drag.get('dragNode').set('innerHTML', '');
            
            var blocks = Y.Node.all( '#'+ drag.get('node').get('parentNode').get('id') + ' .block-container' );
            var data = '';

            blocks.each( function(v, k) {
                data += 'block_order%5B%5D=' + v.get('id');
                data += '&';
            } );

            data += 'contentobject_attribute_id=' + BlockDDInit.cfg.attributeid;
            data += '&version=' + BlockDDInit.cfg.version;
            data += '&zone=' + BlockDDInit.cfg.zone;
            Y.io.ez( 'ezflow::updateblockorder', { on: { success: _callBack }, method: 'POST', data: data } );

            var newOrder = drag.get('node').get('parentNode').all('.block-container');
            var index = 0;
            newOrder.each(function(v, k) {
                var inputList = v.all('.block-control');

                for(var i = 0; i < inputList.size(); i++) {
                    var input = inputList.item(i);
                    var name = input.get('name');

                    if( name.match(/([a-z]+)+_([\d]+)\[([\d]+)\]\[([\d]+)\]/) ) {
                        name = name.replace( /([a-z]+)+_([\d]+)\[([\d]+)\]\[([\d]+)\]/, "$1_$2[$3][" + index + "]" );
                    } else if ( name.match(/([a-zA-Z+]+)\[([\d-\w_]+)-([\d]+)+(-[\w_]+)?\]/) ) {
                        name = name.replace( /([a-zA-Z+]+)\[([\d-\w_]+)-([\d]+)+(-[\w_]+)?\]/, "$1[$2-" + index + "$4]" );
                    } else if ( name.match(/([a-zA-Z]+)+\_+([0-9])/) ) {
                        name = name.replace( /([a-zA-Z]+)+\_+([0-9])/, "$1_" + index );
                    }

                    input.set('name', name);
                }

                index++;
            });
        });

        Y.DD.DDM.on('drag:drophit', function(e) {
            var drop = e.drop.get('node'), drag = e.drag.get('node');
            
            if (drop.get('tagName').toLowerCase() !== 'div' && drop.get('id') !== drag.get('parentNode').get('id') ) {
                if (!drop.contains(drag)) {
                    drop.appendChild(drag);
                }
            }
        });

        function _callBack( id, o ) {

        }

        var goingUp = false, lastY = 0;
        
        var dragList = Y.Node.all('#zone-' + BlockDDInit.cfg.zone + '-blocks .block-container');
        dragList.each(function(v, k) {
            var dd = new Y.DD.Drag({
                node: v,
                target: {
                    padding: '0'
                }
            }).plug(Y.Plugin.DDProxy, {
                moveOnEnd: false
            }).plug(Y.Plugin.DDConstrained, {
                constrain2node: '#zone-' + BlockDDInit.cfg.zone + '-blocks'
            });
            // Workround for Safari 4.0.2
            // TODO: Remove after 3.0.0 GA release
            dd.addInvalid('select');
        });

        var dropList = Y.Node.all('#zone-' + BlockDDInit.cfg.zone + '-blocks');
        dropList.each(function(v, k) {
            var drop = new Y.DD.Drop({
                node: v
            });
        });
        
    });
}
