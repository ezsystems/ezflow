<h2>{$block.name|wash()}</h2>
<form id="search-form-{$block.id}" action="{'ezajax/search'|ezurl('no')}" method="post">
    <input id="search-string-{$block.id}" type="text" name="SearchStr" value="" />
    <input id="search-button-{$block.id}" class="button" type="submit" name="SearchButton" value="{'Search'|i18n( 'design/ezflow/block/search' )}" />
    <input name="SearchOffset" type="hidden" value="0"  />
    <input name="SearchLimit" type="hidden" value="10"  />
</form>

<div id="search-results-{$block.id}"></div>
{ezscript_require( array( 'ezjsc::yui3', 'ezjsc::yui3io', 'ezajaxsearch.js' ) )}

<script type="text/javascript">
eZAJAXSearch.cfg = {ldelim}
                        searchstring: '#search-string-{$block.id}',
                        searchbutton: '#search-button-{$block.id}',
                        searchresults: '#search-results-{$block.id}',
                        dateformattype: 'shortdatetime',
                        noresulttemplate: '<p>{'No search results found for:'|i18n( 'design/ezflow/block/search' )} "{ldelim}search_string{rdelim}"</p>',
                        resulttemplate: '<div class="result-item float-break"><div class="item-title"><a href="{ldelim}url_alias{rdelim}">{ldelim}title{rdelim}</a></div><div class="item-published-date">[{ldelim}class_name{rdelim}] {ldelim}date{rdelim}</div></div>'
                   {rdelim};
eZAJAXSearch.init();
</script>
