<div class="zone-layout-{$zone_layout|downcase()} norightcol">

<div class="content-columns float-break">
<div class="leftcol-position">
<div class="leftcol">

<!-- ZONE CONTENT: START -->

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

{if and( is_set( $zones[0].blocks ), $zones[0].blocks|count() )}
{foreach $zones[0].blocks as $block}
    {include uri='design:parts/zone_block.tpl' zone=$zones[0]}
{/foreach}
{/if}

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- ZONE CONTENT: END -->

<!-- COLUMNS TWO: START -->

<div class="columns-two">
<div class="col-1">

<!-- ZONE CONTENT: START -->

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

{if and( is_set( $zones[2].blocks ), $zones[2].blocks|count() )}
{foreach $zones[2].blocks as $block}
    {include uri='design:parts/zone_block.tpl' zone=$zones[2]}
{/foreach}
{/if}

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- ZONE CONTENT: END -->

</div>
<div class="col-2">

<!-- ZONE CONTENT: START -->

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

{if and( is_set( $zones[3].blocks ), $zones[3].blocks|count() )}
{foreach $zones[3].blocks as $block}
    {include uri='design:parts/zone_block.tpl' zone=$zones[3]}
{/foreach}
{/if}

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- ZONE CONTENT: END -->

</div>
</div>

<!-- COLUMNS TWO: END -->

</div>
</div>

<div class="maincol-position">
<div class="maincol">

<!-- ZONE CONTENT: START -->

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

{if and( is_set( $zones[1].blocks ), $zones[1].blocks|count() )}
{foreach $zones[1].blocks as $block}
    {include uri='design:parts/zone_block.tpl' zone=$zones[1]}
{/foreach}
{/if}

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- ZONE CONTENT: END -->

</div>
</div>

<div class="rightcol-position">
<div class="rightcol">

</div>
</div>

</div>

</div>
