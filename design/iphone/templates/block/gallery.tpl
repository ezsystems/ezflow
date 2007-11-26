<script src={"javascript/prototype.js"|ezdesign} type="text/javascript"></script>

<script type="text/javascript">
{literal}
var Utils={
  holdOn: false,
  tInterval: 0,
  getTInterval: function() {
    return 1000 * Utils.tInterval++;
  },
  getParams: function(node) {
        var params=new Object();
        var elems=node.getElementsByClassName("params");
        for(var i=0; i<elems.length; i++) {
        var el=elems[i].getElementsByTagName("div");
                for (var j=0; j<el.length; j++) {
                        params[el[j].className]=el[j].innerHTML;
                }
        }
        return params;
  },
  suffix: function(num) {
    if (num ==1) return 'a';
    num%=100;
    if (num == 12 || num == 13 || num == 14) return '';
    num%=10;
    if (num == 2 || num == 3 || num == 4) return 'y';
    return '';
  },
  processDt: function(clazzName,maxMinutes,eff,fmt, elem) {
    DatesProcessor.process({
        clazzName: clazzName, 
        maxMinutes: maxMinutes, 
        effFunc: eff==true?DatesProcessor.defEffFunc:null, 
        fmt: fmt, 
        elem: elem,
        newerElemFunc: DatesProcessor.timeAgoReplFunc
        });
  },
  processDtGosp: function(clazzName,maxMinutes,eff,fmt, elem) {
    DatesProcessor.process({
        clazzName: clazzName, 
        maxMinutes: maxMinutes, 
        effFunc: eff==true?DatesProcessor.defEffFunc:null, 
        fmt: fmt, 
        elem: elem,
        newerElemFunc: DatesProcessor.hhmiReplFunc,
        olderElemFunc: DatesProcessor.ddmReplFunc
        });
  },
  logP: function(params) {
    var el = $("logP");
    if (! el) {
        el = document.createNode("div");
        el.setAttribute("id","logP");
        el.setAttribute("style","opacity:0");
        document.body.appendChild(el);
    }
    el.innerHTML="<img src='http://p.gazeta.pl?"
        +params
        +"&t="
        +(new Date()).getTime()
        +"&u="
        +getCookie('GazetaPlUser')
        +"' width='1px' height='1px'/>";
  }
}


DailyPhoto = Class.create();

DailyPhoto.prototype = {
  elemsI: new Array(),
  elemsIC: new Array(),
  initialize: function(node) {
    var params=Utils.getParams(node);
    this.prefix = node.id;
    Element.getElementsByClassName(node,"bg").each(function(el){
        Element.setStyle(el,{opacity: 0.5});
              el.style.zIndex = 2;
    });
    this.paused=false;
    this.speed = 1*params["speed"]/1000;
    this.foto = 1*params["photo"];
    this.fotoCount = 1*params["photoCount"];
    this.borderColor=$(this.prefix+"_content").getStyle("borderColor");
    this.surColor=$(this.prefix+"_content").parentNode.getStyle("backgroundColor");
    this.content=$(this.prefix+"_content");
    this.content.setStyle({borderColor:this.surColor});
    $(this.prefix+"_content").setStyle({borderWidth:'2px'});
    Event.observe(node, 'mouseover', this.mouseoverHandler.bindAsEventListener(this));
    Event.observe(node, 'mouseout', this.mouseoutHandler.bindAsEventListener(this));
    var fc=this.fotoCount;
    for (var i = 0; i < fc; i++) {
      var elem = $(this.prefix + '_i' + i);
      this.elemsI[i]=elem;
      elem.style.zIndex = -1*i;
      if(i>0) {
    elem.setStyle({opacity: 0.0})
          Element.removeClassName(elem,"hidden");
      }
      elem = $(this.prefix + '_ic' + i);
      this.elemsIC[i]=elem;
      elem.style.zIndex = -1*i;
      if(i>0) {
    elem.setStyle({opacity: 0.0})
          Element.removeClassName(elem,"hidden");
      }
    }
    this.timer=setTimeout(this.next.bind(this), (this.speed*1000) + Utils.getTInterval());
    this.working=false;
    Event.observe(window,"unload",this.cleanup.bind(this));
  },
  cleanup: function() {
    this.content=null;
    clearTimeout(this.timer);
  },
  mouseoverHandler: function(){
    this.content.setStyle({borderColor:this.borderColor});
    this.pause();
  },
  mouseoutHandler: function(){
    this.content.setStyle({borderColor:this.surColor});
        //$(this.prefix+"_content").setStyle({borderWidth:'2px'});
    this.release();
  },
  next: function() {
    if (this.working) return;
    this.working=true;
    this.timer=setTimeout(this.next.bind(this), this.speed*1000);
    if (!this.paused) this.forceChange(this.foto + 1);
    this.working=false;
  },
  pause: function(event) { this.paused=true; },
  release: function(event) { this.paused=false; },
  changePhoto: function(event) {
    this.forceChange(Event.element(event).index);
    return false;
  },
  forceChange: function(f) {
    var fr=20;
    var elemsI=this.elemsI;
    var elemsIC=this.elemsIC;
    new Effect.Opacity(elemsI[this.foto], {duration:1.0, from:1.0, to:0.0, fps:fr});
    new Effect.Opacity(elemsIC[this.foto], {duration:1.0, from:1.0, to:0.0, fps:fr});
    elemsIC[this.foto].style.zIndex = -1*this.foto;
    elemsI[this.foto].style.zIndex = -1*this.foto;
    this.foto = f;
    if (this.foto >= this.fotoCount) this.foto = 0;
    new Effect.Opacity(elemsI[this.foto], {duration:1.0, from:0.0, to:1.0, fps:fr});
    new Effect.Opacity(elemsIC[this.foto], {duration:1.0, from:0.0, to:1.0, fps:fr});
    elemsI[this.foto].style.zIndex = 1;
    elemsIC[this.foto].style.zIndex = 3;
  }
}
Event.observe(window,'load',function () {
        document.getElementsByClassName('dailyPhoto').each(function (node, ind){
            new DailyPhoto(node);
        });
  });



{/literal}
</script>
<div id="address-{$block.zone_id}-{$block.id}">

<div class="block">

{def $block_name = ''}
{if is_set( $block.name )}
    {set $block_name = $block.name}
{else}
    {set $block_name = ezini( $block.type, 'Name', 'block.ini' )}
{/if}

<h2 class="grey_background">{$block_name}</h2>

<div id="{$block.id}" class="dailyPhoto">
    <div id="{$block.id}_content" class="content">
    {foreach $block.valid_nodes as $index => $item}
    
        {def $link=$item.parent.url_alias}
        {if $item.parent.class_identifier|ne('gallery')}
            {set $link=$item.url_alias}
        {/if}
    
        {if eq( $index, 0 )}
        <div style="z-index: 1; opacity: 0.999999;" id="{$block.id}_i{$index}"><a href="{$link|ezurl(no)}"><img alt="" src="{$item.data_map.image.content[iphonelarge].full_path|ezroot(no)}" /></a></div>
        {else}
        <div style="z-index: -{$index}; opacity: 0;" id="{$block.id}_i{$index}" class=""><a href="{$link|ezurl(no)}"><img alt="" src="{$item.data_map.image.content[iphonelarge].full_path|ezroot(no)}" /></a></div>
        {/if}
    {/foreach}
    </div>
    <div style="opacity: 0.5; z-index: 2;" class="bg"> </div>
    <div id="{$block.id}_captions" class="captions">
    {foreach $block.valid_nodes as $index => $item}
        {if eq( $index, 0 )}
        <div style="z-index: 1; opacity: 0.999999;" id="{$block.id}_ic{$index}">
            <div><a href="{$link|ezurl(no)}">{$item.data_map.caption.content.output.output_text}</a></div>
        </div>
        {else}
        <div style="z-index: -{$index}; opacity: 0;" id="{$block.id}_ic{$index}">
            <div><a href="{$link|ezurl(no)}">{$item.data_map.caption.content.output.output_text}</a></div>
        </div>
        {/if}
    {/foreach}
    </div>
    <div style="display: none;" class="params">
        <div class="speed">5000</div>
        <div class="photo">0</div>
        <div class="photoCount">{$block.valid_nodes|count()}</div>
    </div>
</div>

</div>

</div>