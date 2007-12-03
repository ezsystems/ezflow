{def $global_layout_class = fetch( 'content', 'class', hash( 'class_id', 'global_layout' ) )
     $global_layout_object = $global_layout_class.object_list[0]}

<!-- ZONE CONTENT: START -->

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

{attribute_view_gui attribute=$global_layout_object.data_map.page}

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- ZONE CONTENT: END -->