<form name="editform" id="editform" enctype="multipart/form-data" method="post" action={concat( '/content/edit/', $object.id, '/', $edit_version, '/', $edit_language|not|choose( concat( $edit_language, '/' ), '/' ), $is_translating_content|not|choose( concat( $from_language, '/' ), '' ) )|ezurl}>

{* This is to force form to use publish action instead of 'Manage version' button on enter key press in input and textarea elements. *}
<input class="defaultbutton hide" type="submit" id="ezedit-default-button" name="PublishButton" value="{'Send for publishing'|i18n( 'design/admin/content/edit' )}" />

{* Current gui locale, to be used for class [attribute] name & description fields *}
{def $content_language = ezini( 'RegionalSettings', 'Locale' )}

<div id="leftmenu">
<div id="leftmenu-design">

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

{*
<div class="block">
    <label>{'Section to search'|i18n( 'design/admin/content/edit' )}</label>
    <select name="SearchSectionID" multiple="multiple">
        {foreach fetch( 'content', 'section_list' ) as $section}
            <option value="{$section.id}">{$section.name}</option>
        {/foreach}
    </select>
</div>

<div class="block date-range">
    <label>{'Date range'|i18n( 'design/admin/content/edit' )}</label>
    <input name="SearchDate" type="radio" value="1" onclick="javascript:showDateRange(this);" /> {'Past day'|i18n( 'design/admin/content/edit' )} <input name="SearchDate" type="radio" value="2" onclick="javascript:showDateRange(this);" /> {'Past week'|i18n( 'design/admin/content/edit' )} <br />
    <input name="SearchDate" type="radio" value="3" onclick="javascript:showDateRange(this);" /> {'Past month'|i18n( 'design/admin/content/edit' )} <input name="SearchDate" type="radio" value="4" onclick="javascript:showDateRange(this);" /> {'Past 3 months'|i18n( 'design/admin/content/edit' )} <br />
    <input name="SearchDate" type="radio" value="5" onclick="javascript:showDateRange(this);" /> {'Past year'|i18n( 'design/admin/content/edit' )}
</div>

<div class="block date-range-selection">
    <label>From:</label>
        <select>
            <option>September</option>
        </select>
        <select>
            <option>23</option>
        </select>
        <select>
            <option>2007</option>
        </select>
    <label>To:</label>
        <select>
            <option>October</option>
        </select>
        <select>
            <option>12</option>
        </select>
        <select>
            <option>2007</option>
        </select>
</div>
*}

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

{include uri='design:content/edit_menu.tpl'}

{ezscript_require( array( 'ezjsc::yui3', 'ezjsc::yui3io', 'ezajaxsearch.js' ) )}

<script type="text/javascript">
eZAJAXSearch.cfg = {ldelim}
                        searchstring: '#search-string-{$object.id}',
                        searchbutton: '#search-button-{$object.id}',
                        searchresults: '#search-results-{$object.id}',
                        dateformattype: 'shortdatetime',
                        resulttemplate: '<div class="block"><div class="item-title">{ldelim}title{rdelim}<\/div><div class="item-published-date">[{ldelim}class_name{rdelim}] {ldelim}date{rdelim}<\/div><div class="item-selector"><input type="checkbox" value="{ldelim}node_id{rdelim}" name="SelectedNodeIDArray[]" /></div></div>'
                   {rdelim};
eZAJAXSearch.init();
</script>

<!-- SEARCH BOX: END -->

</div>
</div>

<div id="maincontent">
<div id="maincontent-design" class="float-break"><div id="fix">


<div id="controlbar-top" class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="button-left">
    <input class="defaultbutton" type="submit" name="PublishButton" value="{'Send for publishing'|i18n( 'design/admin/content/edit' )}" title="{'Publish the contents of the draft that is being edited. The draft will become the published version of the object.'|i18n( 'design/admin/content/edit' )}" />
    <input class="button" type="submit" name="StoreButton" value="{'Store draft'|i18n( 'design/admin/content/edit' )}" title="{'Store the contents of the draft that is being edited and continue editing. Use this button to periodically save your work while editing.'|i18n( 'design/admin/content/edit' )}" />
    <input class="button" type="submit" name="StoreExitButton" value="{'Store draft and exit'|i18n( 'design/admin/content/edit' )}" title="{'Store the draft that is being edited and exit from edit mode. Use when you need to exit your work and return later to continue.'|i18n( 'design/admin/content/edit' )}" />
    <input class="button" type="submit" name="DiscardButton" value="{'Discard draft'|i18n( 'design/admin/content/edit' )}" onclick="return confirmDiscard( '{'Are you sure you want to discard the draft?'|i18n( 'design/admin/content/edit' )|wash(javascript)}' );" title="{'Discard the draft that is being edited. This will also remove the translations that belong to the draft (if any).'|i18n( 'design/admin/content/edit' ) }" />
</div>
<div class="button-right">
    <a href="JavaScript:void(0);" onclick="jQuery('#page').toggleClass('main-column-only');" class="controlbar-top-full-screen-toggle" title="{'Toggle fullscreen editing!'|i18n( 'design/admin/content/edit' )}">&nbsp;</a>
</div>
<div class="float-break"></div>
{* DESIGN: Control bar END *}</div></div>
</div>

<!-- Maincontent START -->

{include uri='design:content/edit_validation.tpl'}

<div class="content-edit">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header">

<h1 class="context-title">{$object.class_identifier|class_icon( normal, $object.class_name )}&nbsp;{'Edit <%object_name> (%class_name)'|i18n( 'design/admin/content/edit',, hash( '%object_name', $object.name, '%class_name', first_set( $class.nameList[$content_language], $class.name ) ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div>

{* DESIGN: Content START *}<div class="box-content">

<div class="context-information">
{if $object.content_class.description}
<p class="left class-description">
    {first_set( $class.descriptionList[$content_language], $class.description )|wash}
</p>
{/if}
<p class="right translation">
{let language_index=0
     from_language_index=0
     translation_list=$content_version.translation_list}

{section loop=$translation_list}
  {if eq( $edit_language, $item.language_code )}
    {set language_index=$:index}
  {/if}
{/section}

{if $is_translating_content}

    {let from_language_object=$object.languages[$from_language]}

    {'Translating content from %from_lang to %to_lang'|i18n( 'design/admin/content/edit',, hash(
        '%from_lang', concat( $from_language_object.name, '&nbsp;<img src="', $from_language_object.locale|flag_icon, '" style="vertical-align: middle;" alt="', $from_language_object.locale, '" />' ),
        '%to_lang', concat( $translation_list[$language_index].locale.intl_language_name, '&nbsp;<img src="', $translation_list[$language_index].language_code|flag_icon, '" style="vertical-align: middle;" alt="', $translation_list[$language_index].language_code, '" />' ) ) )}

    {/let}

{else}

    {$translation_list[$language_index].locale.intl_language_name}&nbsp;<img src="{$translation_list[$language_index].language_code|flag_icon}" style="vertical-align: middle;" alt="{$translation_list[$language_index].language_code}" />

{/if}

{/let}
</p>
<div class="break"></div>
</div>

{if $is_translating_content}
<div class="content-translation">
{/if}

<div class="context-attributes">
    {include uri='design:content/edit_attribute.tpl' view_parameters=$view_parameters}
</div>

{if $is_translating_content}
</div>
{/if}

{* DESIGN: Content END *}</div>
<div class="controlbar">
{* DESIGN: Control bar START *}
<div class="block">
    {if ezpreference( 'admin_edit_show_re_edit' )}
        <input type="checkbox" name="BackToEdit" />{'Back to edit'|i18n( 'design/admin/content/edit' )}
    {/if}
    <input class="defaultbutton" type="submit" name="PublishButton" value="{'Send for publishing'|i18n( 'design/admin/content/edit' )}" title="{'Publish the contents of the draft that is being edited. The draft will become the published version of the object.'|i18n( 'design/admin/content/edit' )}" />
    <input class="button" type="submit" name="StoreButton" value="{'Store draft'|i18n( 'design/admin/content/edit' )}" title="{'Store the contents of the draft that is being edited and continue editing. Use this button to periodically save your work while editing.'|i18n( 'design/admin/content/edit' )}" />
    <input class="button" type="submit" name="StoreExitButton" value="{'Store draft and exit'|i18n( 'design/admin/content/edit' )}" title="{'Store the draft that is being edited and exit from edit mode. Use when you need to exit your work and return later to continue.'|i18n( 'design/admin/content/edit' )}" />
    <input class="button" type="submit" name="DiscardButton" value="{'Discard draft'|i18n( 'design/admin/content/edit' )}" onclick="return confirmDiscard( '{'Are you sure you want to discard the draft?'|i18n( 'design/admin/content/edit' )|wash(javascript)}' );" title="{'Discard the draft that is being edited. This will also remove the translations that belong to the draft (if any).'|i18n( 'design/admin/content/edit' ) }" />
    <input type="hidden" name="DiscardConfirm" value="1" />
</div>
{* DESIGN: Control bar END *}
</div>

</div>


{include uri='design:content/edit_relations.tpl'}


{* Locations window. *}
{* section show=eq( ezini( 'EditSettings', 'EmbedNodeAssignmentHandling', 'content.ini' ), 'enabled' ) *}
{if or( ezpreference( 'admin_edit_show_locations' ),
                  count( $invalid_node_assignment_list )|gt(0) )}
    {* We never allow changes to node assignments if the object has been published/archived.
       This is controlled by the $location_ui_enabled variable. *}
    {include uri='design:content/edit_locations.tpl'}
{else}
    {* This disables all node assignment checking in content/edit *}
    <input type="hidden" name="UseNodeAssigments" value="0" />
{/if}

</div>

<!-- Maincontent END -->
</div>
<div class="break"></div>
</div></div>

</form>




{literal}
<script language="JavaScript" type="text/javascript">
jQuery(function( $ )//called on document.ready
{
    var docScrollTop = 0, el = $('#editform input:text:enabled:first');

    if ( document.body.scrollTop !== undefined ) 
    	docScrollTop = document.body.scrollTop;// DOM compliant
    else if ( document.documentElement.scrollTop  !== undefined )
    	docScrollTop = document.documentElement.scrollTop;// IE6 standards mode;

    // Do not set focus if user has scrolled
    if ( docScrollTop < 10 )
    {
    	window.scrollTo(0, Math.max( el.offset().top - 180, 0 ));
        el.focus();
    }
});

function confirmDiscard( question )
{
    // Disable/bypass the reload-based (plain HTML) confirmation interface.
    document.editform.DiscardConfirm.value = "0";

    // Ask user if she really wants do it, return this to the handler.
    return confirm( question );
}
</script>
{/literal}