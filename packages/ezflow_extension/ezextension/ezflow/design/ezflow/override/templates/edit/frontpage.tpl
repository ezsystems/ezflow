<form enctype="multipart/form-data" id="editform" name="editform" method="post" action={concat("/content/edit/",$object.id,"/",$edit_version,"/",$edit_language|not|choose(concat($edit_language,"/"),''))|ezurl}>

{include uri='design:parts/website_toolbar_edit.tpl'}

<!-- ZONE CONTENT: START -->

<div class="content-edit-frontpage">

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<div class="norightcol">

<div class="content-columns float-break">
<div class="leftcol-position">
<div class="leftcol">

<!-- SEARCH BOX: START -->

<div id="ajaxsearchbox" class="tab-container">

<div class="block">
    <label>{'Search phrase'|i18n( 'design/ezflow/edit/frontpage' )}</label>
    <input class="textfield" type="text" id="search-string-{$object.id}" name="SearchStr" value="" />
    <input name="SearchOffset" type="hidden" value="0"  />
    <input name="SearchLimit" type="hidden" value="10"  />
    <input id="search-button-{$object.id}" class="serach-button" type="image" src={"search_button.gif"|ezimage} name="SearchButton" value="" alt="{'Search'|i18n( 'design/ezflow/edit/frontpage' )}" title="{'Search'|i18n( 'design/ezflow/edit/frontpage' )}" />
</div>
{*
<div class="block">
    <label>{'Section to search'|i18n( 'design/ezflow/edit/frontpage' )}</label>
    <select name="SearchSectionID" multiple="multiple">
        {foreach fetch( 'content', 'section_list' ) as $section}
            <option value="{$section.id}">{$section.name}</option>
        {/foreach}
    </select>
</div>

<div class="block date-range">
    <label>{'Date range'|i18n( 'design/ezflow/edit/frontpage' )}</label>
    <input name="SearchDate" type="radio" value="1" onclick="javascript:showDateRange(this);" /> {'Past day'|i18n( 'design/ezflow/edit/frontpage' )} <input name="SearchDate" type="radio" value="2" onclick="javascript:showDateRange(this);" /> {'Past week'|i18n( 'design/ezflow/edit/frontpage' )} <br />
    <input name="SearchDate" type="radio" value="3" onclick="javascript:showDateRange(this);" /> {'Past month'|i18n( 'design/ezflow/edit/frontpage' )} <input name="SearchDate" type="radio" value="4" onclick="javascript:showDateRange(this);" /> {'Past 3 months'|i18n( 'design/ezflow/edit/frontpage' )} <br />
    <input name="SearchDate" type="radio" value="5" onclick="javascript:showDateRange(this);" /> {'Past year'|i18n( 'design/ezflow/edit/frontpage' )}
</div>
*}
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

<div class="block search-results">
    <span class="header">{'Results'|i18n( 'design/ezflow/edit/frontpage' )}</span>
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
<p>
<select name="zonelist" onchange="addBlock( this, {$content_attribute.id} );">
<option>{'Select:'|i18n( 'design/ezflow/edit/frontpage' )}</option>
{def $zone_id = ''
     $zone_name = ezini( $content_attribute.content.zone_layout, 'ZoneName', 'zone.ini' )}
    {foreach $content_attribute.content.zones as $index => $zone}
    {if and( is_set( $zone.action ), eq( $zone.action, 'remove' ) )}
        {skip}
    {/if}
    {set $zone_id = $index}
    <optgroup label="{$zone_name[$zone.zone_identifier]}">
        {if and( is_set( $zone.blocks ), $zone.blocks|count() )}
        {foreach $zone.blocks as $index => $block}
        <option value="{$zone_id}-{$index}">{$index|inc}: {ezini( $block.type, 'Name', 'block.ini' )}</option>
        {/foreach}
        {/if}
    </optgroup>
    {/foreach}
</select>
</p>
<input id="addtoblock" class="button" type="submit" name="CustomActionButton[{$content_attribute.id}_new_item]" value="{'Add to block'|i18n( 'design/ezflow/edit/frontpage' )}" />
{/if}
{/foreach}

</div>

</div>

{ezscript_require( array( 'ezjsc::yui3', 'ezjsc::yui3io', 'ezajaxsearch.js' ) )}

<script type="text/javascript">
eZAJAXSearch.cfg = {ldelim}
                        searchstring: '#search-string-{$object.id}',
                        searchbutton: '#search-button-{$object.id}',
                        searchresults: '#search-results-{$object.id}',
                        resulttemplate: '<div class="result-item float-break"><div class="item-title"><img src={"item-bullet.gif"|ezimage} />&nbsp;{ldelim}title{rdelim}</div><div class="item-published-date">[{ldelim}class_name{rdelim}] {ldelim}date{rdelim}</div><div class="item-selector"><input type="checkbox" value="{ldelim}object_id{rdelim}" name="SelectedObjectIDArray[]" /></div></div>'
                   {rdelim};
eZAJAXSearch.init();
</script>

<!-- SEARCH BOX: END -->

</div>
</div>

<div class="maincol-position">
<div class="maincol">



<div class="content-edit">

    <div class="attribute-header">
        <h1 class="long">{'Edit %1 - %2'|i18n( 'design/ezflow/edit/frontpage', , array( $class.name|wash, $object.name|wash ) )}</h1>
    </div>

    <div class="attribute-language">
    {def $language_index = 0
         $from_language_index = 0
         $translation_list = $content_version.translation_list}

    {foreach $translation_list as $index => $translation}
       {if eq( $edit_language, $translation.language_code )}
          {set $language_index = $index}
       {/if}
    {/foreach}

    {if $is_translating_content}

        {def $from_language_object = $object.languages[$from_language]}

        {'Translating content from %from_lang to %to_lang'|i18n( 'design/ezflow/edit/frontpage',, hash(
            '%from_lang', concat( $from_language_object.name, '&nbsp;<img src="', $from_language_object.locale|flag_icon, '" style="vertical-align: middle;" alt="', $from_language_object.locale, '" />' ),
            '%to_lang', concat( $translation_list[$language_index].locale.intl_language_name, '&nbsp;<img src="', $translation_list[$language_index].language_code|flag_icon, '" style="vertical-align: middle;" alt="', $translation_list[$language_index].language_code, '" />' ) ) )}

    {else}

        {'Content in %language'|i18n( 'design/ezflow/edit/frontpage',, hash( '%language', $translation_list[$language_index].locale.intl_language_name ))}&nbsp;<img src="{$translation_list[$language_index].language_code|flag_icon}" style="vertical-align: middle;" alt="{$translation_list[$language_index].language_code}" />

    {/if}
    </div>

    {include uri='design:content/edit_validation.tpl'}

    {include uri='design:content/edit_attribute.tpl'}

    <div class="buttonblock">
    <input class="defaultbutton" type="submit" name="PublishButton" value="{'Send for publishing'|i18n( 'design/ezflow/edit/frontpage' )}" />
    <input class="button" type="submit" name="StoreButton" value="{'Store draft'|i18n( 'design/ezflow/edit/frontpage' )}" />
    <input class="button" type="submit" name="DiscardButton" value="{'Discard draft'|i18n( 'design/ezflow/edit/frontpage' )}" />
    <input type="hidden" name="DiscardConfirm" value="0" />
    <input type="hidden" name="RedirectIfDiscarded" value="{ezhttp( 'LastAccessesURI', 'session' )}" />
    <input type="hidden" name="RedirectURIAfterPublish" value="{ezhttp( 'LastAccessesURI', 'session' )}" />
    </div>
</div>


</div>
</div>

<div class="rightcol-position">
<div class="rightcol">

</div>
</div>
</div>

</div>

</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

</div>

<!-- ZONE CONTENT: END -->


</form>