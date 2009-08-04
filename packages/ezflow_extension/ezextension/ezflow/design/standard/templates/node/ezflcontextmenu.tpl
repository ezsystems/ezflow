<script type="text/javascript">
menuArray['eZFlow'] = new Array();
menuArray['eZFlow']['depth'] = 1; // this is a first level submenu of ContextMenu
menuArray['eZFlow']['elements'] = new Array();
</script>

<hr />

<a id="menu-ezflow" class="more" href="#" onmouseover="ezpopmenu_showSubLevel( event, 'eZFlow', 'menu-ezflow' ); return false;">{'eZ Flow'|i18n( 'design/admin/node/contextmenu' )}</a>

<form id="menu-form-push-to-block" method="post" action={concat("ezflow/push/", $module_result.content_info.node_id)|ezurl}>
  <input type="hidden" name="NodeID" value="%nodeID%" />
  <input type="hidden" name="ObjectID" value="%objectID%" />
  <input type="hidden" name="CurrentURL" value="%currentURL%" />
</form>