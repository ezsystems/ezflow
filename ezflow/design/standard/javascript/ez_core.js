/*
    eZ Core : tiny javascript library for ajax and stuff
    
    Copyright (c) 2007 eZ Systems AS & André Rømcke
    Licensed under the MIT License:
    http://www.opensource.org/licenses/mit-license.php

    Inspired/based on the work of:
    prototype.conio.net        simon.incutio.com    jquery.com
    moofx.mad4milk.net        dean.edwards.name    erik.eae.ne
    
*/

var ez = {
    version: 0.7,
    handlers: [],
    string: {
        trim: function( s )
        {
            // Trims leading and ending whitespace
            return s.replace(/^\s+|\s+$/g,'');
        },
        stripTags: function( s )
        {
            // Strip html tags
			return s.replace(/<\/?[^>]+>/gi, '');
        },
        normalize: function( s )
        {
            // Replaces multiple whitespace by one
            return s.replace(/\s+/g,' ');
        },
        jsCase: function( s )
        {
            // Transforms string to jsCase: margin-right -> marginRight
            return s.replace(/-\D/g, function(m){
                return m.charAt(1).toUpperCase();
            });
        },
        cssStyle: function( s )
        {
            // Transforms string to css style: marginRight -> margin-right
            return s.replace(/\w[A-Z]/g, function(m){
                return (m.charAt(0)+'-'+m.charAt(1).toLowerCase());
            });
        }
    },
    fn: {
        bind: function()
        {
            // Binds arguments to a function, so when you call the returned wrapper function
            // first argument is function, second is 'this' and the rest is arguments
            var args = ez.$c(arguments), __fn = args.shift(), __object = args.shift();
            return function(){ return __fn.apply(__object, args.concat( ez.$c(arguments) ))};
        },
        bindEvent: function()
        {
            // Same as above, but includes the arguments to the wrapper function first(ie events)
            var args = ez.$c(arguments), __fn = args.shift(), __object = args.shift();
            return function(){ return __fn.apply(__object, ez.$c(arguments).concat( args ))};
        },
        stripFn: function( fn )
        {
            // Strips anonymous wrapper functions
            var str = (fn || '').toString();
            if (str.indexOf('anonymous') === -1) return fn;
            else return str.match(/function anonymous\(\)\n\{\n(.*)\n\}/gi) ?  RegExp.$1 : str;
        }
    },
    array: {
        make: function( obj, s )
        {
            // Makes a array out of anything or nothing, split strings by s if present and extends array with native extensions
            var r = [], el;
            if ( ez.val(obj) )
            {
                if (typeof obj != 'object' && obj.constructor != Object && obj != '[object NodeList]') obj = [obj];
                for (var i=0; ez.set(el = obj[i]); i++){
                    ( s !== undefined && typeof el == 'string' ) ? r = r.concat( el.split( s ) ) : r.push( el );
                }
                r = (r.length != 0 || obj.length == 0) ? r : [obj];
            }
            return ez.array.nativeExtend( r );
        },
        extend: function( arr )
        {
            // function for extending array with extensions defined in ez.array.nativExtensions and ez.array.ezExtensions
            if ( arr.ez ) return arr;
            arr = ez.array.nativeExtend( arr );
            arr = ez.object.extend( arr, new ez.array.eZextensions );
            return arr;
        },
        nativeExtend: function( arr )
        {
            // function for extending array with native extensions defined in ez.array.nativExtensions
            arr = ez.object.extend( arr, ez.array.nativExtensions );
            return arr;
        },
        nativExtensions: {
            indexOf: function(o, s){
                // javascript 1.6: finds the first index that is like object o, s is optional start index
                for (var i=s || 0, l = this.length; i < l; i++) if (this[i]==o) return i;
                return -1;
            },
            forEach: function(fn, t){
                // javascript 1.6: iterate true an array and calls fn on each iteration, t optionally overrides 'this'
                for (var i = 0, l = this.length; i < l; i++) fn.call(t,this[i],i,this);
            },
            filter: function(fn, t){
                // javascript 1.6: filter return values of fn that evaluates to true, t optionally overrides 'this'
                for(var i = 0, r = ez.$c(), l = this.length; i < l; i++) fn.call(t,this[i],i,this) && r.push(this[i]);
                return r;
            },
            map: function(fn, t){
                // javascript 1.6: maps the return value of a callback function fn, t optionally overrides 'this'
                for(var i = 0, r = ez.$c(), l = this.length; i < l; i++) r[i] = fn.call(t,this[i],i,this);
                return r;
            }
        },
        eZextensions: function(){
			// Init function for array eZ extensions
        }
    },
    object: {
        extend: function( destObj, sourceObj, force, clean )
        {
                // extends destination object with methods from source object
                // set force to true to overwrite existing methods
                // object will be nulled if clean and force is true
                for ( key in sourceObj || {} )
                {
                    if (force || destObj[key] === undefined)
                        destObj[key] = (!clean) ? sourceObj[key] : null;
                }
                return destObj;
        }
    },
    cookie: {
        set: function( name, value, days )
        {
            // Set cookie value by name, if days are not defined cookie will be removed! 
            var date = new Date();
            date.setTime( date.getTime() + ( ( days || -1 ) * 86400 ) );
            document.cookie = name + '=' + value + '; expires=' + date.toUTCString() + '; path=/';
        },
        get: function ( name )
        {
            // Get cookie value by name, or empty string if not found
            var r = '', name = name + '=', cookArr = document.cookie.split(';'), t;
            for ( var i = 0, l = cookArr.length; i < l; i++ ){
                t = cookArr[i].trim();
                if ( t.indexOf(name) == 0 ) r = t.substring( name.length, t.length );
            }
            return r;
        },
        remove: function( name )
        {
            // Wrapper function for cookie.set with value = ''
			ez.cookie.set( name, '' );
        }
    },
    element: {
        addEvent: function( el, trigger, handler, t )
        {
            // Method for setting element event
			// Supports w3c, ie and dom0 event handling
            trigger = trigger.replace(/^on/i,'');
            handler = ez.fn.bindEvent( handler, t || el, el );
            if ( el.addEventListener ) el.addEventListener( trigger, handler, false );
            else if ( el.attachEvent ) el.attachEvent( 'on' + trigger, handler );
            else {
                var c = el['on' + trigger];
                if ( typeof c != 'function' ) el['on' + trigger] = handler;
                else el['on' + trigger] = function(){ handler(); c()};
            }
            ez.handlers.push( arguments );
        },
        removeEvent: function( el, trigger, handler )
        {
            // Method for removing element event if w3c or ie event model is supported
            trigger = trigger.replace(/^on/i,'');
            if ( el.removeEventListener ) el.removeEventListener( trigger, handler, false );
            else if ( el.detachEvent ) try {el.detachEvent( trigger, handler )} catch ($i){};
        },
        clean: function( el )
        {
            // For deleting every function reference before deleting nodes to avoid mem leak
            if (!el || !el.attributes) return;
            var a = el.attributes, n;
            for (var i = 0, l = a.length; i < l; i += 1)
            {
                n = a[i].name;
                if (el[n].test(/^on/i)) el[n] = null;
            }
            ez.$c( el.childNodes ).forEach( ez.element.clean );
        },
        extend: function( el )
        {
            // Function for extending element with native extensions and ez extensions
            if ( el.ez ) return el;
            el = ez.object.extend( el, ez.element.nativExtensions );
            return new ez.element.eZextensions( el );;
        },
        getByCSS: function()
        {
            // CSS2 query function, returns a extended array of extended elements
            // Example: arr = ez.$$('div.my_class, input[type=text], img[alt~=went]');
            var args = ez.$c(arguments, ','), d = [document], r = [], atr, ati;
            if ( args.length === 1 && args[0].ez === 'array') return args[0];
            else if (typeof args[args.length -1] == 'object') d = args.pop();
            args.forEach(function(el){
                if (typeof el=='string'){
                    var par = ez.$c( (d.ez ? d.el : d) ); 
                    ez.$c( ez.string.trim( el ), /\s+/ ).forEach(function(str)
                    {
                        var temp = ez.$c(), tag = (str.match(/^(\w+)([.#\[]?)\s?/)) ? RegExp.$1 : '*', id = 0, cn = 0, at = 0;
                        if (str.match(/([\#])([a-zA-Z0-9_\-]+)([.#\[]?)\s?/)) id = RegExp.$2;
                        if (str.match(/([\.])([a-zA-Z0-9_\-]+)([.#\[]?)\s?/)) cn = ' ' + RegExp.$2 + ' ';
                        if (str.match(/\[(\w+)([~\|\^\$\*]?)=?"?([^\]"]*)"?\]/)) at = [RegExp.$1 , RegExp.$2, RegExp.$3];
                        par.forEach(function(doc)
                        {
                            ez.$c(doc.getElementsByTagName(tag)).forEach(function(i)
                            {
                                if (id && (!i.getAttribute('id') || i.getAttribute('id')!=id)) return;
                                if (at)
                                {
                                  if (atr = ez.fn.stripFn( i.getAttribute(at[0]) ))
                                  {
                                    ati = atr.indexOf(at[2]);
                                    if (at[2]=='');
                                    else if (at[1] == '' && atr == at[2]);
                                    else if (at[1] == '*' && ati != -1);
                                    else if (at[1] == '~' && (' '+atr+' ').indexOf(' '+at[2]+' ') != -1);
                                    else if (at[1] == '^' && ati == 0);
                                    else if (at[1] == '$' && ati == (atr.length-at[2].length) && ati != -1);
                                    else return;
                                  } else return;
                                }
                                if (cn && (' '+i.className+' ').indexOf(cn)==-1) return;
                                temp.push(i); 
                            });
                        });
                        par = temp;
                    });
                    r = r.concat(par);
                } else r.push(el);
            }, this);
            r = ez.$c(r).map( ez.element.extend );
            return ez.array.extend( r );
        },
        getById: function(a)
        {
            // Element id query function, returns a ez extended array of extended elements
            // returns only element if only one is found, and returns false if none
            if (a.ez) return a;
            var r = [];
            ez.$c(arguments).forEach(function(el){
                el = typeof el=='string' ? document.getElementById(el) :  el;
                if (el) r.push( el.ez ? el : ez.element.extend( el ) );
            });
            return r.length > 1  ? ez.array.extend( r ) : r[0] || false;
        },
        getScroll: function( side, el ) 
        {
            // Get element scroll, and fallback to document if el is not passed
            var r = 0, el = (el || document.documentElement || document.body), w = (el || window);
            if ( el.scrollTop ) r = side == 'left' ? el.scrollLeft : el.scrollTop;
            else if ( typeof w.pageYOffset == 'number' ) r = side == 'left' ? w.pageXOffset : w.pageYOffset;
            return r;
        },
        nativExtensions: {},
        eZextensions: function( el ){
            // Init function for element eZextensions
            this.step = 0;
            this.el = el;
            this.settings = { duration:20, fps:35, selectedClass:'selected', transition: function(p){return {def:p}}, target: {}, origin: {} };
            return this;
        }
    },
    ajax: function( o, uri, postBack )
    {
        // Init function for ez.ajax, if uri is specified the call will be done immediately
        this.o = ez.object.extend({method: 'GET'}, o || {}, true);
        if ( uri ) this.load( uri, this.o.postBody || null, postBack );
    },
    fx: {
        // Included set of transition fx's to use with animations
        cubic: function(p){p = Math.pow(p,3); return {def:p}},
        circ: function(p){p = Math.sqrt(p); return {def:p}},
        sinoidal: function(p){p = ((-Math.cos(p*Math.PI)/2) + 0.5); return {def:p}}
    },
    script: function( str, oC )
    {
        // Script loading function
        // str (string) handles both script url and script string
        // if str is url oC (function) is added as load event on the tag
        var scr = document.createElement('script');
        scr.type = 'text/javascript';
        if (str.indexOf('.js') != -1)
        {
            scr.src = str;
            if (oC) ez.element.addEvent(scr, 'load', oC);
        } else scr.text = str;
        document.getElementsByTagName('head')[0].appendChild(scr);
    },
    activeX: function( arr, fb )
    {
       // Function for testing activeX objects and return the one that works or return fb (any) || null
       // Example: ieXhr = ez.activeX(['MSXML2.XMLHTTP.6.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP']);
       for (var i = 0, x, l = arr.length; i < l; i++)
       {
           try
           {
               x = new ActiveXObject(arr[i]);
               return x;
           } catch (ex){}
       }
       return fb || null;
    },
    set: function(o)
    {
       // Returns true if the object is defined
       return (o !== undefined);
    },
    val: function(o)
    {
       // Returns true if the value evaluates to true or 0
       return !!(o || o == 0);
    },
    pick: function()
    {
       // Returns the first defined argument
       for (var i = 0, a = arguments, l = a.length; i < l; i++)
               if (ez.set( a[i] )) return a[i];
       return null;
    },
    num: function(v, f, t)
    {
        // Checks if v is a number, if not f is returned (or 0 if !f)
        // t (string) [float|int] specifies if v should be parsed as int or float
        v = t == 'int' ? parseInt( v ) : parseFloat( v );
        return isNaN( v ) ? f || 0 : v;
    },
    min: function()
    {
       // Returns the lowest number
       var min = null;
       for (var i = 0, a = arguments, l = a.length; i < l; i++)
               if (min === null || min > a[i]) min = a[i];
       return min;
    },
    max: function()
    {
       // Returns the highest number
       var max = null;
       for (var i = 0, a = arguments, l = a.length; i < l; i++)
               if (max === null || max < a[i]) max = a[i];
       return max;
    },
    animationAttributes:
    {
        // List of supported animation element styles an their default unit
        width: 'px',
        height: 'px',
        top: 'px',
        left: 'px',
        marginLeft: 'px',
        marginTop: 'px',
        scrollLeft: 'px',
        scrollTop: 'px',
        opacity: '%',
        fontSize: '%',
        display: ''
    },
    xpath: !!document.evaluate
};//ez


//Shortcuts:
ez.$$ = ez.element.getByCSS;
ez.$  = ez.element.getById;
ez.$c = ez.array.make;


//Properties for the ez.array.eZextensions constructor
ez.array.eZextensions.prototype = {
    ez: 'array',
    callEach: function(){
        // Shortcut function to call functions on the array elements, takes unlimited number of arguments
        // Example: arr.callEach( 'addEvent', 'click', function(){ alert('Hi! Did you just click me?')} );
        // returns an array of the return values
        var args = ez.$c(arguments), __fn = args.shift(), r = ez.$c();
        this.forEach( function( el ){ 
            r.push( el[__fn].apply( el, args ) );
        });
        return r;
    }
}//ez.array.eZExtension.prototype


//Properties for the ez.element.eZextensions constructor
ez.element.eZextensions.prototype = {
    ez: 'element',
    addClass: function( c )
    {
        // Add c (string) to element className list
        // removes the class first so it's not set twice
        this.removeClass( c );
        this.el.className += this.el.className.length > 0 ? ' ' + c : c;
        return this;
    },
    addEvent: function(trigger, handler)
    {
        ez.element.addEvent( this.el, trigger, handler, this );
    },
    check: function( check )
    {
        // function for toggling check on checkboxes
        // set check to true or false to force value
        if ( ez.set( this.el.checked ) )
            this.el.checked = ez.set( check ) ? !!check : !this.el.checked;
        return this;
    
    },
    getPos: function( s )
    {
        // Gets the element position for s (string) [top|left]
        // Example: ez.$('my_el').getPos('top');
        var t = 0, l = 0, el = this.el;
        do{    t = t + el.offsetTop || 0;
            l = l + el.offsetLeft || 0;
            el = el.offsetParent;
        } while (el);
        return s == 'left' ? l : t;
    },
    getSize: function( s, offset )
    {
        // Gets the element size for s (string) [width|height]
        // offset (boolean) determines if you want offset or scroll size
        // Example: ez.$('my_el').getSize('width');
         if (s == 'width') s = (offset) ? this.el.offsetWidth : this.el.scrollWidth;
         else s = (offset) ? this.el.offsetHeight : this.el.scrollHeight;
        return s;
    },
    getStyle: function( s )
    {
       // Gets the element calculated style by string s
       // Has multiple fallbacks for various browser differences
       // But make sure you are using the eZ Core Styles to make the
       // different browsers fully agree on the style.. (todo)
       // Example: ez.$('my_el').getStyle('margin');
       s = s == 'float' ? 'cssFloat' : ez.string.jsCase(s);
       var el = this.el, r = (document.defaultView) ? document.defaultView.getComputedStyle(el, null) : el.currentStyle;
       r = (r === null) ? el.style[s] : r[s];
       if (!ez.val( r ) || r == 'auto')
       {
          switch ( s )
          {
            case 'opacity':
                r = 0;
                break;
            case 'display':
                r = 'none';
                break;
            case 'width':
            case 'height':
                r = this.getSize(s);
                break;
            case 'top':
            case 'left':
                r = this.getStyle('position') == 'relative' ? 0 : this.getPos(s);
          }
       }
       return r;
    },
    removeClass: function( c )
    {
        // Removes c (string) from className on this element
        if (this.el.className)
            this.el.className = ez.$c(this.el.className, ' ').filter(function( cn ){ return (cn != c) ? 1: 0; }).join(' ');
        return this;
    },
    removeEvent: function(trigger, handler)
    {
        ez.element.removeEvent( this.el, trigger, handler );
    },
    setSettings:  function( s )
    {
        // Settings function, specify settings in the s (object) parameter
        // Example: ez.$('my_el').setSettings( {duration: 200} );
        this.settings = ez.object.extend( this.settings, s || {}, true );
        return this.settings;
    },
    setStyles: function( styles )
    {
        // Shortcut to set multiple styles with object, will also fix case on style names
        // Example: ez.$('my_el').setStyles( {color: '#ff00ff', marginLeft: '10px', 'margin-top': '10px'} );
        for ( style in styles )
        {
            jsStyle = ez.string.jsCase( style );
            this.el.style[jsStyle] = styles[style];
        }
        return this;
    },
    postData: function( ommitName )
    {
        var el = this.el, val = '', ty = el.type;
        if ( ty === undefined || !el.name ) return '';
        if (ty == 'radio' || ty == 'checkbox')
            val = el.checked ? el.value : '';
        else if (ty == 'select-one')
            val = ( el.selectedIndex != -1 ) ? el.options[el.selectedIndex].value : '';
        else if (ty == 'select-multiple')
            ez.$c( el.options ).forEach(function(o){ if ( o.selected ) val = o.value; });
        else if ( el.value !== undefined )
            val = el.value;
        return ( ommitName ) ? val : el.name + '=' + val;
    },
    show: function(s, t, oc)
    {
        // Wrapper function for animate, will animate back to the initial style (element origin)
        // show is a bit miss leading unless you have specified display/opacity as target values
        // see animate for description on parameters s, t, oc
        return this.animate(false, s, t, oc);
    },
    hide: function(s, t, oc)
    {
        // Wrapper function for animate, will animate in the target direction
        // hide is a bit miss leading unless you have specified display/opacity as target values
        // see animate for description on parameters s, t, oc
        return this.animate(true, s, t, oc);
    },
    remove: function()
    {
        this.el.parentNode.removeChild( this.el );
        this.el = null;
    },
    toggle: function(s, t, oc)
    {
        // Wrapper function for animate, will toggle animation direction based on previous target value
        // see animate for description on parameters s, t, oc
        return this.animate(!(this.target || false), s, t, oc);
    },
    animate:  function(target, settings, targetSettings, onComplete, clear)
    {
        // Animates the element, all parameters are optional
        // Example: ez.$('my_el').animate( true, {duration: 400}, {height:300}, function(){ alert('done')} );
        /* parameter:
           target (boolean): specifies the direction you want to go, if target is false it
                             means you want to animate the element back to it's initial style
           settings (object): see setSettings for more info
           targetSettings (object): See animationTarget for more info
           onComplete (function): see animationStep for more info
           clear (boolean): see animationTarget for more info
        */
        target = target || false;
        if ( this.target != target || !this.timeout )
        {
            this.animationStop();
            settings = this.setSettings( settings );
            this.settings.frametime = Math.round(1000 / settings.fps);
            steps = Math.round( settings.duration / this.settings.frametime );
            this.step = ez.set(this.target) ? (this.target == target ? this.step : ez.max(steps - this.step, 0)) : 0;
            this.target = target;
            this.animationTarget( targetSettings || {}, clear );
            this.animationStep( steps, target, onComplete || false );
        }
        return this;
    },
    animationStop: function()
    {
        // Stops any current animation or does nothing if no active animations exists
        clearTimeout(this.timeout);
        this.timeout = null;
        return this;
    },
    animationTarget: function( t, clear )
    {
        // Function to prepare the target object and calculating the elements current style
        // It's also possible to recalculate the current style by setting clear = true
        // Example  el.animationTarget({height:400,opacity:0.2});
        var o = this.settings.origin, a = ez.animationAttributes, v;
        if (clear) this.settings.target = {};
        for (i in t)
        {
            if ( !ez.set(a[i]) ) continue;
            if ( !ez.set(o[i]) || clear )
            {
                v = this.getStyle( i );
                o[i] = !a[i] ? v : ez.num( v, 0);
            }
            this.settings.target[i] = t[i];
        }
        return this;
    },
    animationStep: function(steps, target, oc)
    {
        // Private
        // This is the animation 'engine' and it's is called from .animate()
        // For customcallback pass a function in onComplete (oc) when you call animate
        if ( target != this.target ) return;
        var s = this.settings, els = this.el.style, t = s.target, o = s.origin;
        if (this.step == steps + 1)
        {
            if (target)
            {
                els.display = t.display || '';
                if (s.onTarget) s.onTarget(this, this.el);
            }
            if ( oc ) oc(this, this.el, target);
        }
        else
        {
            var ot = this.step/steps, pos = s.transition((!target ? ot : 1-ot)), u, x, y;
            for ( an in t)
            {
                u = ez.animationAttributes[an] || 0;
                if (!u) continue;
                x = u == '%' ? 1 : o[an];
                y = ((t[an] - x) * (1 - ( pos[an] || pos['def'] )) + x) * (u == '%' ? 100 : 1);
                switch(an)
                {
                case 'opacity':
                    if (els.opacity != undefined) els[an] = (y >= 100) ? '' : y/100;
                    else if (els.filter != undefined) els.filter = (y >= 100) ? '' : 'alpha(opacity=' + y + ')';
                    break;
                case 'scrollTop':
                case 'scrollLeft':
                    this.el[an] = y;
                    break;
                default:
                    els[an] = y + u;
                }
            }
            if (this.step == 0 && !target)
            {
                els.display = o.display || '';
                if (s.onOrigin) s.onOrigin(this, this.el);
            }
            ++this.step;
            this.timeout = setTimeout( ez.fn.bind( this.animationStep, this, steps, target, oc ),  s.frametime);
        }
    }//animationStep
}//ez.element.eZExtension.prototype


// Properties for the ez.ajax constructor
ez.ajax.prototype = {
    load: function( uri, post, pB )
    {
        // Function for re calling same ajax object with different url (string) and post(string) values
        if (!this.xhr) this.xhr = new XMLHttpRequest();
        this.pb = pB || this.o.postBack;
        if (0 < this.xhr.readyState < 4)
        {
            this.xhr.onreadystatechange = function(){};
            this.xhr.abort();
        }
        this.xhr.open( (ez.set( post ) ? 'POST' : 'GET'), uri, true);
        this.xhr.onreadystatechange = ez.fn.bind(function()
        {
            if ( this.xhr.readyState != 4 ) return;
            if ( this.xhr.status == 200 || this.xhr.status == 0 ) this.done();
            else if ( this.o.onError ) this.o.onError( this.xhr.status, this.xhr.statusText );
            this.xhr.onreadystatechange = function(){};
        }, this);
        if ( ez.set( post ) )
        {
            this.xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=' + (this.o.charset || 'iso-8859-1' ));
            if (this.xhr.overrideMimeType) this.xhr.setRequestHeader('Connection', 'close');
        }
        this.xhr.setRequestHeader('X-Requested-With', this.o.requestedWith || 'XMLHttpRequest');
        this.xhr.setRequestHeader('Accept', this.o.accept || 'application/json,application/xml,application/xhtml+xml,text/javascript,text/xml,text/html,*/*');
        this.xhr.send( post || null );
    },

    done: function()
    {
        // Private function called when ajax call is done. Optional update element, preUpdate and onLoad callBacks.
        var r = this.xhr, o = this.o, el = ((o.update) ? ez.$(o.update) : 0);
        if (el) el.innerHTML = (o.preUpdate)? o.preUpdate(r): r.responseText;
        if (this.pb) this.pb(r, el);
    }
};//ez.ajax.prototype


// Some IE specific functionality
if ( window.detachEvent && !window.opera && /MSIE [56]/.test( navigator.userAgent ) )
{
    window.attachEvent('onload',function(){
        // Adds png alpha transparency to older ie browsers
        // just add pngfix to the class on the images / inputs you want fixed
        ez.$$(['img.pngfix','input[type=image].pngfix']).forEach(function(o){
            if ( !/.png$/i.test( o.el.src ) ) return;
            var el = o.el, w = el.width, h = el.height;
            el.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + el.src + "', sizingMethod='scale')";
            el.src = '';
            el.height = h;
            el.width = w;
        });
    });    
    window.attachEvent('onunload',function(){
        // Automatic cleaning of events in ie 5 and 6 to avoid some event related memory leaks
        for (var i = 0, l = ez.handlers.length; i < l; i++) ez.handlers[i][0].detachEvent('on'+ez.handlers[i][1], ez.handlers[i][2]);
        ez.handlers = null;
    });
}

if (!window.XMLHttpRequest && window.ActiveXObject) var XMLHttpRequest = function(){
    // XMLHttpRequest wrapper for ie browsers that do not support XMLHttpRequest natively
    return ez.activeX(['MSXML2.XMLHTTP.6.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP']);
};