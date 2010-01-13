  <div id="footer">

  <div class="border-box border-box-style2">
  <div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
  <div class="border-ml"><div class="border-mr"><div class="border-mc">
  <div class="border-content">

    <address>
    {if $pagedesign.data_map.footer_text.has_content}
        {$pagedesign.data_map.footer_text.content} 
    {/if}
    {if $pagedesign.data_map.hide_powered_by.data_int|not}
    Powered by <a href="http://ez.no" title="eZ Publish Content Management System">eZ Publish&#8482;</a> Content Management System.
    {/if}
    <div class="page-view-type">
        {'Visit:'|i18n('design/iphone/page_footer')} <span class="mobile-site">{'mobile site'|i18n('design/iphone/page_footer')}</span> | <a href="{'/'|ezroot('no')}" class="full-site">{'full site'|i18n('design/iphone/page_footer')}</a>
    </div>
    </address>

  </div>
  </div></div></div>
  <div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
  </div>
  
  </div>
