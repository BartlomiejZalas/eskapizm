{if $groups && $groups|@count > 0} 
    <div class="flex-default clearfix">
        <nav class="navbar" role="navigation">
            <div class="navbar-header">
                <button class="navbar-toggle" data-toggle="collapse" data-target="#navbarCollapse{$moduleId}" type="button">
                    <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
                </button>
                <span class="navbar-brand">{$moduleName}</span>
            </div>
            <div class="collapse navbar-collapse" id="navbarCollapse{$moduleId}">
                <ul class="nav navbar-nav">
                    {foreach from=$groups item=group name=ojb}
                            {if $smarty.foreach.ojb.first}
                            <li data-group="{$group.id}" data-module="{$moduleId}" class="active">
                                <a href="#section-{$moduleId}-{$group.id}" data-toggle="tab">
                                    {$group.title}
                                </a>
                            </li>
                            {else}
                            <li data-group="{$group.id}" data-module="{$moduleId}">
                                <a href="#section-{$moduleId}-{$group.id}" data-toggle="tab">
                                    {$group.title}
                                </a>
                            </li>
                            {/if}        
                        {/foreach}
                </ul>
            </div>
        </nav>
        <!-- Module content -->        
        <!-- Module Right Content -->
        <div class="tab-content">
            {foreach from=$groups item=group name=ojb7}
            {if $smarty.foreach.ojb7.first}
            <div data-module="{$moduleId}" data-group="{$group.id}" data-h-per-w="{$h_per_w}" id="section-{$moduleId}-{$group.id}" class="row tab-pane fade in active clearfix default-section">
                <div id="section-inner-{$moduleId}-{$group.id}" data-last-page="1" data-last-left="0" data-loaded="1" data-current-page="1" data-total-item="{$group.products|@count}" class="clearfix {if isset($current_id_option)&& $current_id_option ==5}option5-inner{else}option2-inner{/if}" data-h-per-w="{$h_per_w}">
                    {if $group.products|@count >0}
                        {if isset($current_id_option)&& $current_id_option ==5}
                            {include file="$tpl_dir./product-list5-owl.tpl" products=$group.products}
                        {else}
                            {foreach from=$group.products item=product name=products}                    
                            {$imginfo = Image::getImages(Language::getIdByIso($lang_iso),$product.id_product)}
                            {assign var='new_idimg' value=''}
                            {foreach from=$imginfo item=imgitem}
                                {if !$imgitem['cover']}
                                    {assign var='new_idimg' value="`$imgitem['id_product']`-`$imgitem['id_image']`"}
                                    {break}
                                {/if}
                            {/foreach}                    
                            <div class="item col-lg-3 col-md-4 col-sm-6 col-xs-12" itemscope itemtype="http://schema.org/Product">
                                <div class="item-avatar">
                                    <div class="item-avatar-inner">
                                        {if $product.on_sale == '1'}
                                            <i class="img-circle inew">{l s='Sale' mod='flexiblecustom'}</i>
                                        {/if}
                                        <a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
                                            <img class="replace-2x img-responsive img-first" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                            {if !empty($new_idimg)}
                                                <img class="replace-2x img-responsive img-second" src="{$link->getImageLink($product.link_rewrite, $new_idimg, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                            {else}
                                                <img class="replace-2x img-responsive img-second" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                            {/if}                                
                                        </a>                            
                                        {if isset($quick_view) && $quick_view}
                                            <a class="quick-view item-quick-view" href="{$product.link|escape:'html':'UTF-8'}" data-rel="{$product.link|escape:'html':'UTF-8'}">
                                                <span>{l s='Quick view' mod='flexiblecustom'}</span>
                                            </a>
                                        {/if}                                        
                                    </div>
                                </div>
                                <div class="item-other-info">                    
                                    <div class="item-info">
                                        <div class="item-name" itemprop="name">
                                            <a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
                                                {$product.name|truncate:20:''|escape:'html':'UTF-8'}
                                            </a>
                                        </div>
                                        {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                        <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="item-price">
                                            {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                                <meta itemprop="priceCurrency" content="{$currency->iso_code}" />
                                                  <span itemprop="price" class="item-new-price">
                                                        {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                                  </span>
                                                {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
                                                    {hook h="displayProductPriceBlock" product=$product type="old_price"}
                                                    <span class="item-old-price">
                                                        {displayWtPrice p=$product.price_without_reduction}
                                                    </span>
                                                    {hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
                                                {/if}
                                                {hook h="displayProductPriceBlock" product=$product type="price"}
                                                {hook h="displayProductPriceBlock" product=$product type="unit_price"}
                                            {/if}
                                        </div>
                                        {/if}
                                    </div>
                                    <div class="item-rate text-center">
                                        <div class="star_content clearfix">
                                            {hook h='displayProductListReviews' product=$product}
                                        </div>
                                        <div class="add-to-cart">
                                            {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
                                                {if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
                                                    {if isset($static_token)}
                                                        <a class="ajax_add_to_cart_button" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='flexiblecustom'}" data-id-product="{$product.id_product|intval}">
                                                            <span>{l s='Add to cart' mod='flexiblecustom'}</span>
                                                        </a>
                                                    {else}
                                                        <a class="ajax_add_to_cart_button" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='flexiblecustom'}" data-id-product="{$product.id_product|intval}">
                                                            <span>{l s='Add to cart' mod='flexiblecustom'}</span>
                                                        </a>
                                                    {/if}
                                                {else}
                                                    <span class="ajax_add_to_cart_button disabled">
                                                        <span>{l s='Add to cart' mod='flexiblecustom'}</span>
                                                    </span>
                                                {/if}
                                            {/if}                                                 
        									<a title="{l s='Add to Wishlist' mod='flexiblecustom'}" class="addToWishlist" href="javascript:void(0)" data-rel="{$product.id_product}" onclick="javascript: WishlistCart('wishlist_block_list', 'add', '{$product.id_product}', false, 1); return false;"><i class="fa fa-add-to-wishlist"></i></a>
                                            {if isset($comparator_max_item) && $comparator_max_item}
                                                {if $product.id_product|@in_array:$compareProductIds}
                                                    <a class="add_to_compare compare-checked" title="{l s='Add to compare' mod='flexiblecustom'}" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}"><i class="fa fa-exchange"></i></a>
                                                {else}
                                                    <a class="add_to_compare" title="{l s='Add to compare' mod='flexiblecustom'}" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}"><i class="fa fa-exchange"></i></a>
                                                {/if}
                                            {/if}                                                    
                                        </div>
                                    </div>
                                </div>                
                            </div>
                            {/foreach}
                        {/if}
                    {else}
                    <div class="option2-noproduct col-xs-12">{l s='Sorry! There are no products' mod='flexiblecustom'}</div>
                    {/if}
                </div>
                {if isset($current_id_option)&& $current_id_option !=5}
                    {if $group.products|@count >0}
                    <div class="loadMore text-center clearfix">
                        <a data-module="{$moduleId}" data-group="{$group.id}" class="img-circle link-load-more" data-total-pages="0" href="javascript:void(0)">
                            Load more<br>
                            <i class="fa fa-load-more"></i>
                        </a>
                        <div class="no-load" style="display: none">{l s='There is no more products to load' mod='flexiblecustom'}</div>
                   </div>
                   {/if}
               {/if}
            </div>
            {else}
            <div data-module="{$moduleId}" data-group="{$group.id}" data-h-per-w="{$h_per_w}" id="section-{$moduleId}-{$group.id}" class="row tab-pane fade clearfix default-section">
                <div id="section-inner-{$moduleId}-{$group.id}" data-last-page="1" data-last-left="0" data-loaded="1" data-current-page="1" data-total-item="{$group.products|@count}" class="clearfix {if isset($current_id_option)&& $current_id_option ==5}option5-inner{else}option2-inner{/if}" data-h-per-w="{$h_per_w}">
                    {if $group.products|@count >0}
                    {if isset($current_id_option)&& $current_id_option ==5}
                            {include file="$tpl_dir./product-list5-owl.tpl" products=$group.products}
                        {else}
                        {foreach from=$group.products item=product name=products}
                        {$imginfo = Image::getImages(Language::getIdByIso($lang_iso),$product.id_product)}
                        {assign var='new_idimg' value=''}
                        {foreach from=$imginfo item=imgitem}
                            {if !$imgitem['cover']}
                                {assign var='new_idimg' value="`$imgitem['id_product']`-`$imgitem['id_image']`"}
                                {break}
                            {/if}
                        {/foreach}
                        <div class="item col-lg-3 col-md-4 col-sm-6 col-xs-12" itemscope itemtype="http://schema.org/Product">
                            <div class="item-avatar">
                                <div class="item-avatar-inner">
                                    {if $product.on_sale == '1'}
                                        <i class="img-circle inew">{l s='Sale' mod='flexiblecustom'}</i>
                                    {/if}
                                    <a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
                                        <img class="replace-2x img-responsive img-first" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                        {if !empty($new_idimg)}
                                            <img class="replace-2x img-responsive img-second" src="{$link->getImageLink($product.link_rewrite, $new_idimg, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                        {else}
                                            <img class="replace-2x img-responsive img-second" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                        {/if}                                
                                    </a>                            
                                    {if isset($quick_view) && $quick_view}
                                        <a class="quick-view item-quick-view" href="{$product.link|escape:'html':'UTF-8'}" data-rel="{$product.link|escape:'html':'UTF-8'}">
                                            <span>{l s='Quick view' mod='flexiblecustom'}</span>
                                        </a>
                                    {/if}                                        
                                </div>
                            </div>
                            <div class="item-other-info">                    
                                <div class="item-info">
                                    <div class="item-name" itemprop="name">
                                        <a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
                                            {$product.name|truncate:20:''|escape:'html':'UTF-8'}
                                        </a>
                                    </div>
                                    {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                    <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="item-price">
                                        {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                            <meta itemprop="priceCurrency" content="{$currency->iso_code}" />
                                              <span itemprop="price" class="item-new-price">
                                                    {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                              </span>
                                            {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
                                                {hook h="displayProductPriceBlock" product=$product type="old_price"}
                                                <span class="item-old-price">
                                                    {displayWtPrice p=$product.price_without_reduction}
                                                </span>
                                                {hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
                                            {/if}
                                            {hook h="displayProductPriceBlock" product=$product type="price"}
                                            {hook h="displayProductPriceBlock" product=$product type="unit_price"}
                                        {/if}
                                    </div>
                                    {/if}
                                </div>
                                <div class="item-rate text-center">
                                    <div class="star_content clearfix">
                                        {hook h='displayProductListReviews' product=$product}
                                    </div>
                                    <div class="add-to-cart">
                                        {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
                                            {if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
                                                {if isset($static_token)}
                                                    <a class="ajax_add_to_cart_button" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='flexiblecustom'}" data-id-product="{$product.id_product|intval}">
                                                        <span>{l s='Add to cart' mod='flexiblecustom'}</span>
                                                    </a>
                                                {else}
                                                    <a class="ajax_add_to_cart_button" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='flexiblecustom'}" data-id-product="{$product.id_product|intval}">
                                                        <span>{l s='Add to cart' mod='flexiblecustom'}</span>
                                                    </a>
                                                {/if}
                                            {else}
                                                <span class="ajax_add_to_cart_button disabled">
                                                    <span>{l s='Add to cart' mod='flexiblecustom'}</span>
                                                </span>
                                            {/if}
                                        {/if}                                                 
    									<a title="{l s='Add to Wishlist' mod='flexiblecustom'}" class="addToWishlist" href="javascript:void(0)" data-rel="{$product.id_product}" onclick="javascript: WishlistCart('wishlist_block_list', 'add', '{$product.id_product}', false, 1); return false;"><i class="fa fa-add-to-wishlist"></i></a>
                                        {if isset($comparator_max_item) && $comparator_max_item}
                                            {if $product.id_product|@in_array:$compareProductIds}
                                                <a class="add_to_compare compare-checked" title="{l s='Add to compare' mod='flexiblecustom'}" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}"><i class="fa fa-exchange"></i></a>
                                            {else}
                                                <a class="add_to_compare" title="{l s='Add to compare' mod='flexiblecustom'}" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}"><i class="fa fa-exchange"></i></a>
                                            {/if}
                                        {/if}                                                    
                                    </div>
                                </div>
                            </div>                
                        </div>
                        {/foreach}
                        {/if}
                    {else}
                    <div class="option2-noproduct col-xs-12">{l s='Sorry! There are no products' mod='flexiblecustom'}</div>
                    {/if}
                </div>
                {if isset($current_id_option)&& $current_id_option !=5}
                    {if $group.products|@count >0}
                    <div class="loadMore text-center clearfix">
                        <a data-module="{$moduleId}" data-group="{$group.id}" class="img-circle link-load-more" data-total-pages="0" href="javascript:void(0)">
                            {l s='Load more' mod='flexiblecustom'}<br>
                            <i class="fa fa-load-more"></i>
                        </a>
                        <div class="no-load" style="display: none">{l s='There is no more products to load' mod='flexiblecustom'}</div>
                   </div>
                   {/if}
               {/if}
            </div>
            {/if}
            {/foreach}                
        </div>
        <!-- END Module Right Content -->
        <!-- END Module content -->
    </div>
{/if}
<script type="text/javascript">
    var baseModuleUrl = "{$baseModuleUrl}";    
    var h_per_w = parseFloat("{$h_per_w}");
</script>