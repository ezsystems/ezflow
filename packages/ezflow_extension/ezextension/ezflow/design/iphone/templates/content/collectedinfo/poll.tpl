{set-block scope=global variable=title}{'Poll %pollname'|i18n( 'design/iphone/collectedinfo/poll', , hash( '%pollname', $node.name|wash() ) )}{/set-block}

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">

<div class="content-view-full">
    <div class="class-poll">
        <div class="poll-result">

    <div class="attribute-header">
        <h1>{'Results'|i18n( 'design/iphone/collectedinfo/poll' )}</h1>
    </div>

        {if $error}

        {if $error_anonymous_user}
        <div class="warning">
            <h2>{'Please log in to vote on this poll.'|i18n( 'design/iphone/collectedinfo/poll' )}</h2>
        </div>
        {/if}

        {if $error_existing_data}
        <div class="warning">
            <h2>{'You have already voted for this poll.'|i18n( 'design/iphone/collectedinfo/poll' )}</h2>
        </div>
        {/if}

        {/if}

        {foreach $object.contentobject_attributes as $contentobject_attribute_item}
            {if $contentobject_attribute_item.contentclass_attribute.is_information_collector}
            {def  $attribute=$contentobject_attribute_item
                  $contentobject_attribute_id=cond( $attribute|get_class|eq( 'ezinformationcollectionattribute' ),$attribute.contentobject_attribute_id,
                                                   $attribute|get_class|eq( 'ezcontentobjectattribute' ),$attribute.id )
                  $contentobject_attribute=cond( $attribute|get_class|eq( 'ezinformationcollectionattribute' ),$attribute.contentobject_attribute,
                                                $attribute|get_class|eq( 'ezcontentobjectattribute' ),$attribute )
                  $total_count=fetch( 'content', 'collected_info_count', hash( 'object_attribute_id', $contentobject_attribute_id ) )
                  $item_counts=fetch( 'content', 'collected_info_count_list', hash( 'object_attribute_id', $contentobject_attribute_id  ) )}

                <table class="poll-resultlist">
                <tr>

                {foreach $contentobject_attribute.content.option_list as $option}
                    {def $item_count=0}
                    {if is_set( $item_counts[$option.id] )}
                        {set $item_count=$item_counts[$option.id]}
                    {/if}
                    <td class="poll-resultname">
                        <p>
                            {$option.value}
                        </p>
                    </td>

                    {def $percentage=cond( $total_count|gt( 0 ), round( div( mul( $item_count, 100 ), $total_count ) ), 0 )
                         $tenth=cond( $total_count|gt( 0 ), round( div( mul( $item_count, 10 ), $total_count ) ), 0 )}
                    <td class="poll-resultbar">
                        <table class="poll-resultbar">
                        <tr>
                            <td class="poll-percentage">
                                <i>{$percentage}%</i>
                            </td>
                            <td class="poll-votecount">
                                {'Votes'|i18n( 'design/iphone/collectedinfo/poll' )}: {$item_count}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            <div class="chart-bar-edge-start">
                                <div class="chart-bar-edge-end">
                                    <div class="chart-bar-resultbox">
                                        <div class="chart-bar-resultbar chart-bar-{$percentage}-of-100 chart-bar-{$tenth}-of-10" style="width: {$percentage}%;">
                                            <div class="chart-bar-result-divider"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </td>
                        </tr>
                        </table>
                    </td>
                    {delimiter}
                </tr>
                <tr>
                    {/delimiter}
                    {undef $item_count $percentage $tenth}
                {/foreach}
                </tr>
                </table>
            {else}
                {if $attribute_hide_list|contains( $contentobject_attribute_item.contentclass_attribute.identifier )|not}
                    <div class="attribute-short">{attribute_view_gui attribute=$contentobject_attribute_item}</div>
                {/if}
            {/if}
        {/foreach}

        <br/>

        {'%count total votes'|i18n( 'design/iphone/collectedinfo/poll' ,,
                                     hash( '%count', fetch( content, collected_info_count, hash( object_id, $object.id ) ) ) )}

            <div class="content-results">
                <div class="attribute-link">
                    <p><a href={$node.url_alias|ezurl}>{'Back to poll'|i18n( 'design/iphone/collectedinfo/poll' )}</a></p>
                </div>
            </div>

        </div>
    </div>
</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>
