{def $root_node = ezini('PushToBlock', 'RootSubtree', 'block.ini')
     $classes = ezini('PushToBlock', 'ContentClasses', 'block.ini')
     $frontpage_list = fetch( 'content', 'tree', hash( 'parent_node_id', $root_node,
                                                       'class_filter_type', 'include',
                                                       'class_filter_array', $classes ))}
<div id="page-datatype-container" class="yui-skin-sam yui-skin-ezflow">

<form method="post" action="{concat('ezflow/push/', $node.node_id)|ezurl('no')}">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Choose placement for "%node_name"'|i18n('design/admin/page/push', , hash( '%node_name', $node.name|wash() ) )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<div class="block">

<input type="button" id="select-frontpage-button" name="SelectFrontpageButton" value="{'Select frontpage'|i18n('design/admin/page/push')}" />
<select id="select-frontpage-list" name="SelectFrontpageList">
    {foreach $frontpage_list as $frontpage}
    <option value="{$frontpage.node_id}">{$frontpage.name|wash()}</option>
    {/foreach}
</select>

<input type="button" id="select-zone-button" name="SelectZoneButton" value="{'Select zone'|i18n('design/admin/page/push')}" />
<select id="select-zone-list" name="SelectZoneList">
</select>

<input type="button" id="select-block-button" name="SelectBlockButton" value="{'Select block'|i18n('design/admin/page/push')}" />
<select id="select-block-list" name="SelectBlockList">
</select>

<input type="button" id="placement-button" name="PlacementButton" value="{'Add'|i18n('design/admin/page/push')}" /> 

<h2>Placement list</h2>

<table id="placement-list">
    <tbody>
        
    </tbody>
</table>
<p> </p>
<input type="button" id="placement-remove-button" name="PlacementRemoveButton" value="{'Remove'|i18n('design/admin/page/push')}" /> 

<input type="submit" id="placement-store-button" name="PlacementStoreButton" value="{'Store'|i18n('design/admin/page/push')}" /> 

</div>

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>

</form>

</div>

{ezscript_require(array( 'ezjsc::yui2' ) )}

<script type="text/javascript">
    YUILoader.onSuccess = function() {ldelim}
        eZPushToBlock.cfg = {ldelim}
            requesturl: "{'ezflow/get'|ezurl('no')}",
            nodename: "{$node.name|wash()|shorten( '50' )}",
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