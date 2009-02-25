/**
 * @author ls
 */
YAHOO.namespace("ez");

YAHOO.ez.sheduleDialog = function() {
    
    //Private
    
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event,
        CurrentHandler = false,
        CurrentHandlerInput = false,
        CurrentHandlerLabel = false;
    
    
    var getHandlers = function() {
        var handlers;
        
        handlers = Dom.getElementsByClassName("schedule-handler", "img");
        
        return handlers;
    };

    var handleDialogSubmit = function() {
        // Get year, month, day
        var year = Dom.get("schedule-dialog-year").value;
        var month = Dom.get("schedule-dialog-month").value;
        var day = Dom.get("schedule-dialog-day").value;
        
        // Get hour, minute
        var hour = Dom.get("schedule-dialog-hour").value;
        var minute = Dom.get("schedule-dialog-minute").value;
        
        // Convert to timestamp and assing as new value to input field
        var timestamp = Number( new Date( year, ( month - 1 ), day, hour, minute ) ) / 1000;
        
        CurrentHandlerInput.value = timestamp;
        CurrentHandlerLabel.innerHTML = day + "/" + month + "/" + year + " " + hour + ":" + minute;

        this.hide();
    };
    
    var handleDialogCancel = function() {
        this.cancel();
    };
    
    var initDialog = function() {
        var hasDialog = Dom.get("schedule-dialog");
        
        if(!hasDialog) {
            Dialog = new YAHOO.widget.Dialog("schedule-dialog", 
                            { width : "30em",
                              fixedcenter : true,
                              visible : false,
                              constraintoviewport : true,
                              buttons : [ { text:"Store", handler:handleDialogSubmit, isDefault:true },
                                      { text:"Cancel", handler:handleDialogCancel } ]
                            });

            var body = "<div class=\"object-left\">";
                body += "<div class=\"block\">";
                body += "<div class=\"element\"><label>Month:</label><input id=\"schedule-dialog-month\" type=\"text\" value=\"\" class=\"schedule-dialog-input\" /></div>";
                body += "<div class=\"element\"><label>Day:</label><input id=\"schedule-dialog-day\" type=\"text\" value=\"\" class=\"schedule-dialog-input\" /></div>";
                body += "<div class=\"element\"><label>Year:</label><input id=\"schedule-dialog-year\" type=\"text\" value=\"\" class=\"schedule-dialog-input\" /></div>";
                body += "</div>";
                body += "<div class=\"block\">";
                body += "<div class=\"element\"><label>Hour:</label><input id=\"schedule-dialog-hour\" type=\"text\" value=\"\" class=\"schedule-dialog-input\" /></div>";
                body += "<div class=\"element\"><label>Minute:</label><input id=\"schedule-dialog-minute\" type=\"text\" value=\"\" class=\"schedule-dialog-input\" /></div>";
                body += "</div>";
                body += "</div>";
                body += "<div class=\"object-right\">";
                body += "<div id=\"shedule-calendar-container\"></div>";
                body += "</div>";
                body += "<div class=\"break\"></div>";
            
            Dialog.renderEvent.subscribe( function() {
                var calendarContainer = Dom.get("shedule-calendar-container");

                // Create Calendar instance
                Calendar = new YAHOO.widget.Calendar("shedule-calendar", calendarContainer);

                // Subscribe to select event for Calendar object and fill up input fields
                Calendar.selectEvent.subscribe( function( type, args ) {
                    var dates = args[0];
                    var date = dates[0];
                    var year = date[0], month = date[1], day = date[2];

                    // Set year, month and day to text input fields
                    Dom.get("schedule-dialog-year").value = year;
                    Dom.get("schedule-dialog-month").value = month;
                    Dom.get("schedule-dialog-day").value = day;

                }, Calendar, true );
                
                Calendar.hide();
                Calendar.render();

                // Subscribe to show event for Dialog object and fill up input fields with data
                this.showEvent.subscribe( function() {
                    // Search for input and span elements which are time holders for queue items
                    CurrentHandlerInput = Dom.getElementsBy(function(el) { return true;}, "input", CurrentHandler.parentNode)[0];
                    CurrentHandlerLabel = Dom.getElementsBy(function(el) { return true;}, "span", CurrentHandler.parentNode)[0];
                    
                    var date = new Date();
                    var hasTimestamp = false;
                    // Check if CurrentHandlerInput exists and has a correct value
                    if ( CurrentHandlerInput 
                            && !isNaN( parseInt( CurrentHandlerInput.value ) ) ) {
                        date = new Date( parseInt( CurrentHandlerInput.value * 1000 ) );
                        hasTimestamp = true;
                    }
                    
                    var year = date.getFullYear();
                    var month = ( date.getMonth() + 1 );
                    var day = date.getDate();
                    var hour = date.getHours();
                    var minutes = date.getMinutes();

                    // Set year, month and day to text input fields
                    Dom.get("schedule-dialog-year").value = year;
                    Dom.get("schedule-dialog-month").value = month;
                    Dom.get("schedule-dialog-day").value = day;
                    
                    // Set hour, minute to text input fields
                    Dom.get("schedule-dialog-hour").value = hour;
                    Dom.get("schedule-dialog-minute").value = minutes;

                    this.cfg.setProperty("pagedate", date);
                    if( hasTimestamp ) {
                        this.cfg.setProperty("selected", month + "/" + day + "/" + year);
                    }
                    this.reset();
                    this.show();
                }, Calendar, true);
            } );

            Dialog.setBody(body);
            Dialog.body.id = "schedule-dialog-container";
            var datatypeContainer = Dom.get('page-datatype-container');
            Dialog.render(datatypeContainer);
        }

        var handlers = getHandlers();
        
        var handlersCount = handlers.length;
        
        for(var i = 0; i < handlersCount; i++) {
            var handler = handlers[i];

            Event.on(handler, "click", function() {
                // Assign clicked handler element to CurrentHandler variable
                CurrentHandler = this;
                Dialog.setHeader(CurrentHandler.title);
                
                Dialog.hide();
                Dialog.show();
            }, handler, true);
        }
    };
    
    // Public
    
    return {
        
        init: function() {
            initDialog();
        },
        
        cfg: function() {
            
        }
        
    }
}();
