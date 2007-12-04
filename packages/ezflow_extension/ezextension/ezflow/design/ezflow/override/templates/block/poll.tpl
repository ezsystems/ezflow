{def $poll_node = fetch( 'content', 'node', hash( 'node_id', $block.custom_attributes.poll_node_id ) )
     $object = $poll_node.object}

<!-- BLOCK: START -->
<div class="block-type-poll">

<div class="border-box block-style2-box-outside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BLOCK BORDER INSIDE: START -->

<div class="border-box block-style2-box-inside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<div class="class-poll">
<!-- BLOCK CONTENT: START -->

<h2>{$block.name}</h2>

        <form method="post" action={"content/action"|ezurl}>
        <input type="hidden" name="ContentNodeID" value="{$object.main_node_id}" />
        <input type="hidden" name="ContentObjectID" value="{$object.id}" />
        <input type="hidden" name="ViewMode" value="full" />

        {let attribute=$object.data_map.question
             option_id=cond( is_set( $#collection_attributes[$attribute.id]), $#collection_attributes[$attribute.id].data_int,false() )}

        <h3>{$attribute.content.name|wash}</h3>

        {section name=OptionList loop=$attribute.content.option_list sequence=array(bglight,bgdark)}
            <label for="poll_option_{$attribute.id}_{$OptionList:item.id}"><input type="radio" name="ContentObjectAttribute_data_option_value_{$attribute.id}" value="{$OptionList:item.id}"
           {section show=$OptionList:item.id|eq($option_id)}checked="checked"{/section}
            id="poll_option_{$attribute.id}_{$OptionList:item.id}"
            />&nbsp;{$OptionList:item.value|wash}</label>
        {/section}

        {/let}
        <input class="button" type="submit" name="ActionCollectInformation" value="{"Vote"|i18n("design/ezwebin/embed/poll")}" />

        </form>
</div>
<!-- BLOCK CONTENT: END -->

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- BLOCK BORDER INSIDE: END -->


</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

</div>
<!-- BLOCK: END -->