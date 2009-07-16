<div class="block-type-online-users block-view-{$block.view}">

<div class="attribute-header"><h2>{$block.name|wash()}</h2></div>

{def $logged_in_count = fetch( 'user', 'logged_in_count' )}
{'There are currently %logged_in_count active users on the system.'|i18n( 'design/ezflow/block/online_users', , hash( '%logged_in_count', concat( '<span id="logged-in-count-', $block.id, '">', $logged_in_count, '</span>' ) ) )}

<br />
{def $anonymous_count = fetch( 'user', 'anonymous_count' )}
{'There are %anonymous_count anonymous users accessing the site.'|i18n( 'design/ezflow/block/online_users', , hash( '%anonymous_count', concat( '<span id="anonymous-count-', $block.id, '">', $anonymous_count, '</span>' ) ) )}

</div>

{ezscript( array('ezyui::ez') )}

<script type="text/javascript">
<!--
{literal}
YUI( YUI3_config ).use('node', 'event', 'io-ez', 'json', function( Y )
{
   Y.on( 'domready', function( e )
   {
       Y.io.ez( 'ezflow::onlineusers', { on: { success: _callBack }, data: 'http_accept=json' } );
   });
   function _callBack( id, o )
   {
       if ( o.responseText && o.responseText.length > 0 )
       {
            var response = Y.JSON.parse(o.responseText);
{/literal}
            var blockID = '{$block.id}';
{literal}
            Y.get( '#logged-in-count-' + blockID ).set( 'innerHTML', response.content.logged_in_count );
            Y.get( '#anonymous-count-' + blockID ).set( 'innerHTML', response.content.anonymous_count );
       }
   }
});
{/literal}
-->
</script>