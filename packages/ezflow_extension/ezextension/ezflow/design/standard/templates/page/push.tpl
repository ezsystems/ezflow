{def $root_node = ezini('PushToBlock', 'RootSubtree', 'block.ini')
     $classes = ezini('PushToBlock', 'ContentClasses', 'block.ini')
     $frontpage_list = fetch( 'content', 'tree', hash( 'parent_node_id', $root_node,
                                                       'class_filter_type', 'include',
                                                       'class_filter_array', $classes ))}
<div id="page-datatype-container" class="yui-skin-sam yui-skin-ezflow">

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<form method="post" action="{concat('ezflow/push/', $node.node_id)|ezurl('no')}">

<h1>{'Choose placement for "%node_name"'|i18n('design/standard/page/push', , hash( '%node_name', $node.name|wash() ) )}</h1>

<input type="button" id="select-frontpage-button" name="SelectFrontpageButton" value="{'Select frontpage'|i18n('design/standard/page/push')}" />
<select id="select-frontpage-list" name="SelectFrontpageList">
    {foreach $frontpage_list as $frontpage}
    <option value="{$frontpage.node_id}">{$frontpage.name|wash()}</option>
    {/foreach}
</select>

<input type="button" id="select-zone-button" name="SelectZoneButton" value="{'Select zone'|i18n('design/standard/page/push')}" />
<select id="select-zone-list" name="SelectZoneList">
</select>

<input type="button" id="select-block-button" name="SelectBlockButton" value="{'Select block'|i18n('design/standard/page/push')}" />
<select id="select-block-list" name="SelectBlockList">
</select>

<input type="button" id="placement-button" name="PlacementButton" value="{'Add'|i18n('design/standard/page/push')}" /> 
<br />
<br />

<h2>{'Placement list'|i18n('design/standard/page/push')}</h2>

<table id="placement-list">
    <tbody>
        
    </tbody>
</table>
<p> </p>
<input type="button" id="placement-remove-button" name="PlacementRemoveButton" value="{'Remove'|i18n('design/standard/page/push')}" /> 

<input type="submit" id="placement-store-button" name="PlacementStoreButton" value="{'Store'|i18n('design/standard/page/push')}" /> 

</form>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

</div>

{ezscript_require(array( 'ezjsc::yui2' ) )}

<script type="text/javascript">
    YUILoader.onSuccess = function() {ldelim}
        eZPushToBlock.cfg = {ldelim}
            requesturl: "{'ezflow/get'|ezurl('no')}",
            nodename: "{$node.name|wash()|shorten( '50' )}",
            nodeid: "{$node.node_id}",
            imagepath: "{'ezpage/clock_ico.gif'|ezimage('no')}"
        {rdelim}
        
        eZPushToBlock.init();
    {rdelim}

    YUILoader.addModule({ldelim}
        name: 'ezpushtoblock',
        type: 'js',
        fullpath: '{"javascript/ezpushtoblock.js"|ezdesign( 'no' )}'
    {rdelim});

    YUILoader.addModule({ldelim}
        name: 'scheduledialog',
        type: 'js',
        fullpath: '{"javascript/scheduledialog.js"|ezdesign( 'no' )}'
    {rdelim});

    YUILoader.addModule({ldelim}
        name: 'scheduledialog-css',
        type: 'css',
        fullpath: '{"stylesheets/scheduledialog.css"|ezdesign( 'no' )}'
    {rdelim});

    YUILoader.require(["button","menu","calendar","container","json","utilities","scheduledialog","scheduledialog-css", "ezpushtoblock"]);
    YUILoader.insert();
</script>
