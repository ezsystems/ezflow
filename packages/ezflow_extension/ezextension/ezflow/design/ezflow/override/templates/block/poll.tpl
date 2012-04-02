{def $poll_node = fetch( 'content', 'node', hash( 'node_id', $block.custom_attributes.poll_node_id ) )
     $poll_object = $poll_node.object
     $question_attribute = $poll_object.data_map.question
     $index = 0
     $option = false()}

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

<h2>{$block.name|wash()}</h2>

        <form method="post" action={"content/action"|ezurl}>
        <input type="hidden" name="ContentNodeID" value="{$poll_object.main_node_id}" />
        <input type="hidden" name="ContentObjectID" value="{$poll_object.id}" />
        <input type="hidden" name="ViewMode" value="full" />

        <h3>{$question_attribute.content.name|wash()}</h3>

        {foreach $question_attribute.content.option_list as $index => $option}
            <label for="poll_option_{$question_attribute.id}_{$option.id}"><input type="radio" name="ContentObjectAttribute_data_option_value_{$question_attribute.id}" value="{$option.id}"
           {if eq( $index, '0' )}checked="checked"{/if}
            id="poll_option_{$question_attribute.id}_{$option.id}" />&nbsp;{$option.value|wash()}</label>
        {/foreach}

        <input class="button" type="submit" name="ActionCollectInformation" value="{"Vote"|i18n("design/ezflow/embed/poll")}" />
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

{undef $poll_node $poll_object $question_attribute $index $option}
