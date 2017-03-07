{if $manufacturers}
<!-- Brands slider module -->
<div id="brands_slider" class="row">
    <div id="brand_list" >
        {foreach from=$manufacturers item=manufacturer name=manufacturer_list}
            <div class="item">
                <a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'html'}">
                    <img src="{$img_manu_dir}{$manufacturer.image}" alt="{$manufacturer.name}"/></a>
            </div>
        {/foreach}
    </div>
</div>
<!-- /Brands slider module -->
{/if}