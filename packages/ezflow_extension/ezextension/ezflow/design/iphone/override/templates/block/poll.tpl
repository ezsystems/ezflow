<div id="address-{$block.zone_id}-{$block.id}">

<div class="block">
    <div class="class-poll">
    {def $poll_node = fetch( 'content', 'node', hash( 'node_id', $block.custom_attributes.poll_node_id ) )
         $object = $poll_node.object}

        <form method="post" action={"content/action"|ezurl}>
        <input type="hidden" name="ContentNodeID" value="{$object.main_node_id}" />
        <input type="hidden" name="ContentObjectID" value="{$object.id}" />
        <input type="hidden" name="ViewMode" value="full" />

        {let attribute=$object.data_map.question
             option_id=cond( is_set( $#collection_attributes[$attribute.id]), $#collection_attributes[$attribute.id].data_int,false() )}

        <h2>{$attribute.content.name|wash}</h2>

        {section name=OptionList loop=$attribute.content.option_list sequence=array(bglight,bgdark)}
            <label for="poll_option_{$attribute.id}_{$OptionList:item.id}"><input type="radio" name="ContentObjectAttribute_data_option_value_{$attribute.id}" value="{$OptionList:item.id}"
           {section show=$OptionList:item.id|eq($option_id)}checked="checked"{/section}
            id="poll_option_{$attribute.id}_{$OptionList:item.id}"
            />&nbsp;{$OptionList:item.value|wash}</label>
        {/section}

        {/let}
        <input class="button" type="submit" name="ActionCollectInformation" value="{"Vote"|i18n("design/iphone/embed/poll")}" />

        </form>
</div>
</div>
</div>