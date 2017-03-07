<section class="home-category hook_home row  clearfix clearBoth">
    <div class="container">
        <div class="heading-title mainFont clearfix">
            <span class="cate_name">{$cate_name}</span>
            <ul class="pull-right nav nav-tabs" role="tablist">
                {if (isset($newproducts)&& $newproducts)}
                    <li class="active"><a href="#newproducts_{$hook_position}" role="tab" data-toggle="tab">{l s='New Arrivals' mod='homecategories'}</a></li>
                {else}
                    {assign var='no_new' value=true}
                {/if}
                {if (isset($bestsellers)&& $bestsellers)}
                    <li {if isset($no_new)&& $no_new}class="active"{/if}><a href="#bestsellers_{$hook_position}" role="tab" data-toggle="tab">{l s='Best Seller' mod='homecategories'}</a></li>
                {else}
                    {assign var='no_bets' value=true}
                {/if}
                {if (isset($mostreviews)&& $mostreviews)}
                    <li {if isset($no_new)&& $no_new && isset($no_bets) && $no_bets}class="active"{/if}><a href="#mostreviews_{$hook_position}" role="tab" data-toggle="tab">{l s='Most Reviews' mod='homecategories'}</a></li>
                {/if}
            </ul>
        </div>
    </div>
    <div class="tab-content">
        {if (isset($newproducts)&& $newproducts)}
            <div role="tabpanel" class="tab-pane active newproducts" id="newproducts_{$hook_position}">
                {include file="$tpl_dir./product-list-carousel.tpl" products=$newproducts id="newproducts_list_{$hook_position}" c_class="effect"}
            </div>
            <script type="text/javascript">
                $(document).ready(function(){
                    $("#newproducts_list_{$hook_position}").owlCarousel({
                      itemsCustom : [
                        [0, 1],
                        [463, 2],
                        [750, 3],
                        [974, 4]
                      ],
                      navigation : true,
                      addClassActive: true,
                      slideSpeed: 500
                  });
                });
            </script>
        {/if}
        {if (isset($bestsellers)&& $bestsellers)}
            <div role="tabpanel" class="tab-pane {if isset($no_new)&& $no_new}active{/if}" id="bestsellers_{$hook_position}">
                {include file="$tpl_dir./product-list-carousel.tpl" products=$bestsellers id="bestsellers_list_{$hook_position}"}
            </div>
             <script type="text/javascript">
                $(document).ready(function(){
                    $("#bestsellers_list_{$hook_position}").owlCarousel({
                      itemsCustom : [
                        [0, 1],
                        [463, 2],
                        [750, 3],
                        [974, 4]
                      ],
                      navigation : true,
                      addClassActive: true,
                      slideSpeed: 500
                  });
                });
            </script>
        {/if}
        {if (isset($mostreviews)&& $mostreviews)}
            <div role="tabpanel" class="tab-pane {if isset($no_new)&& $no_new && isset($no_bets) && $no_bets}active{/if}" id="mostreviews_{$hook_position}">
                {include file="$tpl_dir./product-list-carousel.tpl" products=$mostreviews id="mostreviews_list_{$hook_position}"}
            </div>
            <script type="text/javascript">
                $(document).ready(function(){
                    $("#mostreviews_list_{$hook_position}").owlCarousel({
                      itemsCustom : [
                        [0, 1],
                        [463, 2],
                        [750, 3],
                        [974, 4]
                      ],
                      navigation : true,
                      addClassActive: true,
                      slideSpeed: 500
                  });
                });
            </script>
        {/if}
    </div>
</section>
<script type="text/javascript">
$(document).ready(function(){
    $('.home-category.hook_home .tab-pane.active .owl-item.active ').each(function(i){
        var elem_width = $(this).width();
        $(this).attr("style",
              "width: " + elem_width + "px;"
            + "-webkit-animation-delay:" + i * 300 + "ms;"
            + "-moz-animation-delay:" + i * 300 + "ms;"
            + "-o-animation-delay:" + i * 300 + "ms;"
            + "animation-delay:" + i * 300 + "ms;").addClass('slideInTop animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                $(this).removeClass('slideInTop animated');
                var elem_width = $(this).width();
                $(this).attr("style","width: " + elem_width + "px;" );
        });
    });
})
</script>