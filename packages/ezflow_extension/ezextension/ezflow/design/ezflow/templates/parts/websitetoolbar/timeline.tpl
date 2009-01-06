{def $available_for_classes = ezini( 'TimelineSettings', 'AvailableForClasses', 'timeline.ini' )}

{if $available_for_classes|contains( $content_object.content_class.identifier )}
    <a href={concat( "/ezflow/timeline/", $current_node.node_id )|ezurl} title="{'Timeline'|i18n( 'design/ezwebin/parts/website_toolbar' )}"><img src={"websitetoolbar/ezwt-icon-timeline.gif"|ezimage()} alt="{'Timeline'|i18n( 'design/ezwebin/parts/website_toolbar' )}" /></a>
{/if}