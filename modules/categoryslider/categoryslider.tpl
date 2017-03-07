{if isset($categoryslider_slides)}
</div>
</div>
<div class="category-slide-container">
    <div class="container">
        <div id="responsive_slides">
            <div class="callbacks_container">
              <ul id="categoryslider">
                {foreach from=$categoryslider_slides item=slide}
        	       {if $slide.active}
                        <li><img class="img-responsive" src="{$smarty.const._MODULE_DIR_}/categoryslider/images/{$slide.image|escape:'htmlall':'UTF-8'}" alt="{$slide.legend|escape:'htmlall':'UTF-8'}"  /></li>
                   {/if}
                {/foreach}
              </ul>
            </div>
        </div>
{/if}
