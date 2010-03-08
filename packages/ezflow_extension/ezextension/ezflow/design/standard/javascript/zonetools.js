/**
 * @author ls
 */

YAHOO.namespace("ez");

YAHOO.ez.ZoneLayout = function() {

    // Private
    
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event,
        JSON = YAHOO.lang.JSON;

    var getSelectedZone = function() {
        var selectedZone = false;
        var selectedZoneArray = Dom.getElementsBy( function(el) {
            if(el.className == "zone-type-selector"
                    && el.checked ) {
                return true;
            }
        }, "input");
        
        if( selectedZoneArray[0] ) {
            selectedZone = selectedZoneArray[0];
        }
        return selectedZone;
    }
    
    var setValidatorListener = function() {
        var zoneLayoutButton = Dom.get("set-zone-layout");
        
        if(zoneLayoutButton) {
            Event.on(zoneLayoutButton, "click", validate);
        }
    }

    var objectConv = function(a) {
        var o = {};
        for (var i = 0; i < a.length; i++) {
            o[a[i]] = '';
        }
        return o;
    }

    var validate = function(e) {
        var selecZoneTypeElem = getSelectedZone();
        var allowedZones = JSON.parse(YAHOO.ez.ZoneLayout.cfg.allowedzones);
        var allowedZoneCount = allowedZones.length;

        for(var i=0; i<allowedZoneCount; i++) {
            var allowedZone = allowedZones[i];

            if(allowedZone.type == selecZoneTypeElem.value) {
                var selectedZoneType = allowedZone;
            }
            
            if(allowedZone.type == YAHOO.ez.ZoneLayout.cfg.zonelayout) {
                var zoneLayout = allowedZone;
            }
        }
        
        var zoneCountDiff = 0;
        var allowedZonesCount = selectedZoneType.zones.length;
        var existingZoneCount = zoneLayout.zones.length;

        if ( allowedZonesCount < existingZoneCount )
            zoneCountDiff = existingZoneCount - allowedZonesCount;
        
        var zoneMapContainer = Dom.get('zone-map-container');
        var zoneMapPlaceholder = Dom.get('zone-map-placeholder');
        
        if ( zoneCountDiff != 0 && !Dom.hasClass(zoneMapPlaceholder, 'type_' + selectedZoneType.type) ) {
            zoneMapPlaceholder.className = '';
            Dom.addClass(zoneMapPlaceholder, 'type_' + selectedZoneType.type);
            Dom.replaceClass(zoneMapContainer, 'hide', 'show');
            
            Dom.get('zone-map-type').innerHTML = '<p class="zone-map-type">' + selectedZoneType.name + ' [' + selectedZoneType.type + ']</p>'
            
            var html = '';
            var currZones = zoneLayout.zones;
            var currZonesCount = zoneLayout.zones.length;
            var selZones = selectedZoneType.zones;
            var selZoneCount = selectedZoneType.zones.length;
            
            for(var i=0; i<selZoneCount; i++) {
                var selZone = selZones[i];
                html += '<div class="zone-map-item">';
                html += '<label>' + selZone.name + '</label>';
                html += '<select name="ContentObjectAttribute_ezpage_zone_map[' + selZone.id + ']">';

                for(var j=0; j<currZonesCount; j++) {
                    var currZone = currZones[j];
                    html += '<option value="' + currZone.id  + '">';
                    html += currZone.name;
                    html += '</option>';
                }

                html += '</select>';
                html += '</div>';
            }
            
            zoneMapPlaceholder.innerHTML = html;
            Event.preventDefault(e);
        }

        if (zoneCountDiff != 0 && Dom.hasClass(zoneMapPlaceholder, 'type_' + selectedZoneType.type)) {
            var zoneIDArray = [];
            var selectElements = Dom.getElementsBy(function(e){
                return true;
            }, 'select', 'zone-map-container');
            
            var selectElementsCount = selectElements.length;
            for (var i = 0; i < selectElementsCount; i++) {
                var selectElement = selectElements[i];
                var selectedIndex = selectElement.selectedIndex;
                var selectedValue = selectElement[selectedIndex].value;
                
                if (selectedValue in objectConv(zoneIDArray)) {
                    Event.preventDefault(e);
                }
                else {
                    zoneIDArray.push(selectedValue);
                }
            }
        }
    }
    
    // Public

    return {
        
        init: function() {
            setValidatorListener();
        },
        
        cfg: {}
        
    }

}();
