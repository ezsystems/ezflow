  <!-- Footer area: START -->
  <div id="footer">
  <div id="page-width7">
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
    </address>

  </div>
  </div></div></div>
  <div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
  </div>
  </div>
  </div>
  <!-- Footer area: END -->