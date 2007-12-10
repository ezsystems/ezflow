<div class="block-container">

<div class="block-header float-break">
    <div class="left">
    {ezini( $block.type, 'Name', 'block.ini' )}
    </div>
    <div class="right">
        <input type="image" src="{'ezpage/block_up.gif'|ezimage(no)}" name="CustomActionButton[{$attribute.id}_move_block_up-{$zone_id}-{$block_id}]" /> <input type="image" src="{'ezpage/block_down.gif'|ezimage(no)}" name="CustomActionButton[{$attribute.id}_move_block_down-{$zone_id}-{$block_id}]" /> <input type="image" src="{'ezpage/block_del.gif'|ezimage(no)}" name="CustomActionButton[{$attribute.id}_remove_block-{$zone_id}-{$block_id}]" value="Remove" onclick="return confirmDiscard( 'Are you sure you want to remove this block?' );" />
    </div>
</div>
<div class="block-content">

<div class="block-controls float-break">
    <div class="left blockname">
    <input class="textfield" type="text" name="ContentObjectAttribute_ezpage_block_name_array_{$attribute.id}[{$zone_id}][{$block_id}]" value="{$block.name}" size="35" />
    </div>
    <div class="right">
        <select class="list" name="ContentObjectAttribute_ezpage_block_overflow_{$attribute.id}[{$zone_id}][{$block_id}]">
            <option value="">Overflow</option>
            {foreach $zone.blocks as $index => $overflow_block}
                {if eq( $overflow_block.id, $block.id )}
                    {skip}
                {/if}
            <option value="{$overflow_block.id}" {if eq( $overflow_block.id, $block.overflow_id )}selected="selected"{/if}>{$index|inc}. {if is_set( $overflow_block.name )}{$overflow_block.name}{else}{ezini( $overflow_block.type, 'Name', 'block.ini' )}{/if}</option>
            {/foreach}
        </select>
        <select class="list" name="ContentObjectAttribute_ezpage_block_view_{$attribute.id}[{$zone_id}][{$block_id}]">
        {def $view_name = ezini( $block.type, 'ViewName', 'block.ini' )}
        {foreach ezini( $block.type, 'ViewList', 'block.ini' ) as $view}
            <option value="{$view}" {if eq( $block.view, $view )}selected="selected"{/if}>{$view_name[$view]}</option>
        {/foreach}
        </select>
    </div>
</div>

<div class="block-parameters float-break">
    <div class="left">

    {def $is_dynamic = false()
         $fetch_params = unserialize( $block.fetch_params )}

    {if eq( ezini( $block.type, 'ManualAddingOfItems', 'block.ini' ), 'disabled' )}
        {set $is_dynamic = true()}
    {/if}

    {if $is_dynamic}
        {foreach ezini( $block.type, 'FetchParameters', 'block.ini' ) as $fetch_parameter => $value}
        {if eq( $fetch_parameter, 'Source' )}
            <input class="button" name="CustomActionButton[{$attribute.id}_new_source_browse-{$zone_id}-{$block_id}]" type="submit" value="Choose source" />
        {else}
        <label>{$fetch_parameter}:</label> <input class="textfield" type="text" name="ContentObjectAttribute_ezpage_block_fetch_param_{$attribute.id}[{$zone_id}][{$block_id}][{$fetch_parameter}]" value="{$fetch_params[$fetch_parameter]}" />
        {/if}
        {/foreach}

        {foreach ezini( $block.type, 'CustomAttributes', 'block.ini' ) as $custom_attrib}

            {def $use_browse_mode = ezini( $block.type, 'UseBrowseMode', 'block.ini' )}

            {if eq( $use_browse_mode[$custom_attrib], 'true' )}
                <input class="button" name="CustomActionButton[{$attribute.id}_custom_attribute_browse-{$zone_id}-{$block_id}-{$custom_attrib}]" type="submit" value="Choose source" />
            {else}

            {/if}

            {undef $use_browse_mode}

        {/foreach}
    {else}
        <input class="button" name="CustomActionButton[{$attribute.id}_new_item_browse-{$zone_id}-{$block_id}]" type="submit" value="Add item" />
    {/if}
    </div>
    <div class="right source">
    {if $is_dynamic}

    {if is_set( $fetch_params['Source'] )}
    {if is_array( $fetch_params['Source'] )}
        {foreach $fetch_params['Source'] as $source}
            {$source}
        {/foreach}
    {else}
        {def $source_node = fetch( 'content', 'node', hash( 'node_id', $fetch_params['Source'] ) )}

        {$source_node.name} [{$source_node.object.content_class.name}]

        {undef $source_node}
    {/if}
    {/if}

    {if is_set( $block.custom_attributes )}
        {foreach $block.custom_attributes as $custom_attrib => $value}
            {$custom_attrib} : {$value}
        {/foreach}
    {/if}
    {/if}
    </div>
</div>

<table border="0" cellspacing="1" class="items queue" id="z:{$zone_id}_b:{$block_id}_q">
    {if $block.waiting|count()}
    {foreach $block.waiting as $item sequence array( 'bglight', 'bgdark') as $style}
    <tr id="z:{$zone_id}_b:{$block_id}_i:{$item.object_id}" class="{if $item.ts_publication|lt($current_time)}tbp{/if}">
        <td class="tight"><input type="checkbox" value="{$item.object_id}" name="DeleteItemIDArray[]" /></td>
        <td id="z:{$zone_id}_b:{$block_id}_i:{$item.object_id}_h" class="handler">{fetch( 'content', 'object', hash( 'object_id', $item.object_id ) ).name|wash}</td>
        <td class="time">{$item.ts_publication|l10n( 'shortdatetime' )} ( {if $item.ts_publication|lt( $current_time )}0{else}{$item.ts_publication|sub( $current_time )|datetime( 'custom', '%i:%s' )}{/if} min left ) <input class="textfield" type="text" name="ContentObjectAttribute_ezpage_item_ts_published_value_{$attribute.id}[{$zone_id}][{$block_id}][{$item.object_id}]" value="" size="3" /> <img src="{'ezpage/clock_ico.gif'|ezimage(no)}" /></td>
    </tr>
    {/foreach}
    {else}
     <tr class="empty">
         <td colspan="3">Queue: no items.</td>
     </tr>
     {/if}
</table>
<table border="0" cellspacing="1" class="items online" id="z:{$zone_id}_b:{$block_id}_o">
    {if $block.valid|count()}
    {foreach $block.valid as $item sequence array( 'bglight', 'bgdark') as $style}
    <tr id="z:{$zone_id}_b:{$block_id}_i:{$item.object_id}">
        <td class="tight"><input type="checkbox" value="{$item.object_id}" name="DeleteItemIDArray[]" /></td>
        <td id="z:{$zone_id}_b:{$block_id}_i:{$item.object_id}_h" colspan="2">{fetch( 'content', 'object', hash( 'object_id', $item.object_id ) ).name|wash}</td>
    </tr>
    {/foreach}
    {else}
    <tr class="empty">
        <td colspan="3">Online: no items.</td>
    </tr>
    {/if}
    <tr class="rotation">
        <td colspan="3">Rotation: <input class="textfield" type="text" name="RotationValue_{$block_id}" value="{$block.rotation.value}" size="5" />
            <select class="list" name="RotationUnit_{$block_id}">
                <option value="1" {if eq( $block.rotation.unit, 1 )}selected="selected"{/if}>sec</option>
                <option value="2" {if eq( $block.rotation.unit, 2 )}selected="selected"{/if}>min</option>
                <option value="3" {if eq( $block.rotation.unit, 3 )}selected="selected"{/if}>hour</option>
                <option value="4" {if eq( $block.rotation.unit, 4 )}selected="selected"{/if}>day</option>
            </select>

        Shuffle <input type="checkbox" {if eq( $block.rotation.type, 2 )}checked="checked"{/if} name="RotationShuffle_{$block_id}" /> <input class="button" type="submit" name="CustomActionButton[{$attribute.id}_set_rotation-{$zone_id}-{$block_id}]" value="Set" /></td>
    </tr>
</table>
<table border="0" cellspacing="1" class="items history" id="z:{$zone_id}_b:{$block_id}_h">
    {if $block.archived|count()}
    {foreach $block.archived as $item sequence array( 'bglight', 'bgdark') as $style}
    <tr>
        <td class="tight"><input type="checkbox" value="{$item.object_id}" name="DeleteItemIDArray[]" /></td>
        <td>{fetch( 'content', 'object', hash( 'object_id', $item.object_id ) ).name|wash}</td>
        <td class="status">
            {if ne( $item.moved_to , '' )}
                Moved to:

                {foreach $zone.blocks as $index => $dest_block}
                {if eq( $dest_block.id, $item.moved_to )}
                    {if ne( $dest_block.name, '' )}
                        {$dest_block.name}
                    {else}
                        {ezini( $dest_block.type, 'Name', 'block.ini' )}
                    {/if}
                {/if}
                {/foreach}
            {else}
                Not visible
            {/if}
        </td>
    </tr>
    {/foreach}
    {else}
    <tr class="empty">
        <td colspan="3">History: no items.</td>
    </tr>
    {/if}
</table>

<div class="block-controls float-break">
    <div class="left">
        <input class="button" type="submit" name="CustomActionButton[{$attribute.id}_remove_item-{$zone_id}-{$block_id}]" value="Remove selected" />
    </div>
    <div class="right legend">
        <div class="queue">&nbsp;</div> Queue: {$block.waiting|count()} <div class="online">&nbsp;</div> Online: {$block.valid|count()} <div class="history">&nbsp;</div> History: {$block.archived|count()}
    </div>
</div>

</div>
</div>