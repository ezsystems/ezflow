  <!-- Path content: START -->
  <p>
  {foreach $module_result.path as $path}
  {if $path.url}
    <a href={cond( is_set( $path.url_alias ), $path.url_alias,
                                        $path.url )|ezurl}>{$path.text|wash}</a>
  {else}
    {$path.text|wash}
  {/if}
  {delimiter}<span class="path-separator">&#8250;&#8250;</span>{/delimiter}
  {/foreach}
  </p>
  <!-- Path content: END -->