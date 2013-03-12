<div id="ajaxsearchbox" class="tab-container">

<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h4>{'Quick search'|i18n( 'design/admin/content/edit' )}</h4>

</div></div></div></div></div></div>

<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-br"><div class="box-bl"><div class="box-content">


<div class="block">
    <label>{'Search phrase'|i18n( 'design/admin/content/edit' )}</label>
    <input id="search-string-{$object.id}" class="textfield" type="text" name="SearchStr" value="" />
    <input name="SearchOffset" type="hidden" value="0"  />
    <input name="SearchLimit" type="hidden" value="10"  />
</div>

<div class="block">
    <input id="search-button-{$object.id}" class="button" type="button" name="SearchButton" value="{'Search'|i18n( 'design/admin/content/edit' )}" />
</div>

<div class="block search-results">
    <div id="search-results-{$object.id}" style="overflow: hidden">
</div>

{foreach $content_attributes as $content_attribute}
{if eq( $content_attribute.data_type_string, 'ezpage' )}
<script type="text/javascript">
{literal}
function addBlock( object, id )
{
    var $select = object;
    var $id = id;
    var addToBlock = document.getElementById( 'addtoblock' );
    addToBlock.name = 'CustomActionButton[' + $id  +'_new_item' + '-' + $select.value + ']';
}
{/literal}
</script>

<div class="block">
<label>{'Select block'|i18n( 'design/admin/content/edit' )}</label>
<select name="zonelist" onchange="addBlock( this, {$content_attribute.id} );">
<option>{'Select:'|i18n( 'design/admin/content/edit' )}</option>
{def $zone_id = ''
     $zone_name = ezini( $content_attribute.content.zone_layout, 'ZoneName', 'zone.ini' )}
    {foreach $content_attribute.content.zones as $index => $zone}
    {if and( is_set( $zone.action ), eq( $zone.action, 'remove' ) )}
        {skip}
    {/if}
    {set $zone_id = $index}
    <optgroup label="{$zone_name[$zone.zone_identifier]}">
        {foreach $zone.blocks as $index => $block}
        <option value="{$zone_id}-{$index}">{$index|inc}: {ezini( $block.type, 'Name', 'block.ini' )}</option>
        {/foreach}
    </optgroup>
    {/foreach}
</select>
</div>

<div class="block">
    <input id="addtoblock" class="button" type="submit" name="CustomActionButton[{$content_attribute.id}_new_item]" value="{'Add to block'|i18n( 'design/admin/content/edit' )}" />
</div>
{/if}
{/foreach}

</div>


</div></div></div></div></div></div>

</div>

{ezscript_require( array( 'ezjsc::yui3', 'ezjsc::yui3io', 'ezajaxsearch.js' ) )}

<script type="text/javascript">
<!--
eZAJAXSearch.cfg = {ldelim}
                        searchstring: '#search-string-{$object.id}',
                        searchbutton: '#search-button-{$object.id}',
                        searchresults: '#search-results-{$object.id}',
                        dateformattype: 'shortdatetime',
                        resulttemplate: '<div class="block"><div class="item-title">{ldelim}title{rdelim}<\/div><div class="item-published-date">[{ldelim}class_name{rdelim}] {ldelim}date{rdelim}<\/div><div class="item-selector"><input type="checkbox" value="{ldelim}node_id{rdelim}" name="SelectedNodeIDArray[]" /></div></div>'
                   {rdelim};
eZAJAXSearch.init();
-->
</script>

<!-- SEARCH BOX: END -->

