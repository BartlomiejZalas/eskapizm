<section id="discountproducts">
    <h2 class="heading-title">
        <span class="mainFont">{l s='Deals of the day' mod='discountproducts'}</span>
        <div class="owl-carousel-button">
            <a href="javascript:void(0)" id="discount_prev" class="btnbgcolorhoveronly prev_slide">Prev</a>
            <a href="javascript:void(0)" id="discount_next" class="btnbgcolorhoveronly next_slide">Next</a>
        </div>
    </h2>
        {if isset($products)}
    	   {*}{include file="$tpl_dir./product-list-carousel.tpl" products=$products id='discountproducts_list'}{*}
           <div id="discountproducts_list" class="row" >
           {foreach from=$products item=product name=products}
            {$imginfo = Image::getImages(Language::getIdByIso($lang_iso),$product.id_product)}
            {assign var='new_idimg' value=''}
            {foreach from=$imginfo item=imgitem}
                {if !$imgitem['cover']}
                    {assign var='new_idimg' value="`$imgitem['id_product']`-`$imgitem['id_image']`"}
                    {break}
                {/if}
            {/foreach}
            <ul  class="product_list list">
                <li class="ajax_block_product row clearfix">
                    <div class="product-container">
                        <div class="row">
                            <div class="left-block col-xs-6 col-md-6">
                                <div class="image-wrapper">
                                    <div class="product-image-container">
                                        <a class="product_img_link"	href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
                							<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                            {if !empty($new_idimg)}
                                                <img class="replace-2x img-responsive second-img" src="{$link->getImageLink($product.link_rewrite, $new_idimg, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                            {else}
                                                <img class="replace-2x img-responsive second-img" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                            {/if}
                						</a>
                						{if isset($quick_view) && $quick_view}
                							<a title="{l s='Quick view' mod='discountproducts'}" class="quick-view" href="{$product.link|escape:'html':'UTF-8'}" data-rel="{$product.link|escape:'html':'UTF-8'}">
                								<span title="{l s='Quick view' mod='discountproducts'}">{l s='Quick view' mod='discountproducts'}</span>
                							</a>
                						{/if}
                                        {if isset($product.new) && $product.new == 1}
                							<span class="new-box">
                								<span class="btnbgcolorhover new-label">{l s='New' mod='discountproducts'}</span>
                							</span>
                						{/if}
                						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                							<span class="sale-box {if isset($product.new) && $product.new != 1}has-not-new{/if}">
                								<span class="btnbgcolorhover sale-label">{l s='Sale!' mod='discountproducts'}</span>
                							</span>
                						{/if}
                                    </div>
                                </div>
                            </div>
                            <div class="right-block col-xs-6 col-md-6">
                                <h5 itemprop="name">
            						{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
            						<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
            							{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
            						</a>
            					</h5>
                                {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                					<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="content_price">
                						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                							<span itemprop="price" class="price product-price">
                								{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                							</span>
                							<meta itemprop="priceCurrency" content="{$priceDisplay}" />
                							{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction}
                								<span class="old-price product-price">
                									{displayWtPrice p=$product.price_without_reduction}
                								</span>
                								{if $product.specific_prices.reduction_type == 'percentage'}
                									<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
                								{/if}
                							{/if}
                						{/if}
                					</div>
            					{/if}
                                {*}<span id="deals_day_1" class="is-countdown clearfix"><span class="countdown-row countdown-show4"><span class="countdown-section"><span class="countdown-amount">16</span><span class="countdown-period">Days</span></span><span class="countdown-section"><span class="countdown-amount">12</span><span class="countdown-period">Hrs</span></span><span class="countdown-section"><span class="countdown-amount">48</span><span class="countdown-period">Mins</span></span><span class="countdown-section"><span class="countdown-amount">18</span><span class="countdown-period">Secs</span></span></span></span>
                                {*}<span id="deals_day_{$product.id_product}"></span>
                                <script type="text/javascript">
                                    $(function () {ldelim}
                                    	var austDay = new Date();
                                        austDay = new Date({$deals_day.y},{$deals_day.m -1 },{$deals_day.d},{$deals_day.h},{$deals_day.i},{$deals_day.s});
                                        var endtext = '{$expiryText}';
                                    	//austDay = new Date('$product.specific_prices.to');
                                    	$('#deals_day_'+{$product.id_product}).countdown({ldelim}until: austDay, padZeroes: true,description: '', expiryText: endtext, labels: ['Years', 'Months', 'Weeks', 'Days', 'Hrs', 'Mins', 'Secs']{rdelim});
                                    {rdelim});
                                </script>
            					{hook h='displayProductListReviews' product=$product}

                                <p class="product-desc" itemprop="description">
            						{$product.description_short|strip_tags:'UTF-8'|truncate:180:'...'}
            					</p>

                                <div class="button-container">
            						{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
            							{if ($product.allow_oosp || $product.quantity > 0)}
            								{if isset($static_token)}
            									<a class="btnbgcolorhover button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='discountproducts'}" data-id-product="{$product.id_product|intval}">
            										<span>{l s='Add to cart' mod='discountproducts'}</span>
            									</a>
            								{else}
            									<a class="btnbgcolorhover button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='discountproducts'}" data-id-product="{$product.id_product|intval}">
            										<span>{l s='Add to cart' mod='discountproducts'}</span>
            									</a>
            								{/if}
            							{else}
            								<span class="btnbgcolorhover button ajax_add_to_cart_button btn btn-default disabled">
            									<span>{l s='Add to cart' mod='discountproducts'}</span>
            								</span>
            							{/if}
            						{/if}
                                    <div class="btnbgcolor functional-buttons clearfix">
            						{hook h='displayProductListFunctionalButtons' product=$product}
            						{if isset($comparator_max_item) && $comparator_max_item}
            							<div class="compare">
            								<a class="btnbglightcolorhoveronly add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to Compare'}">{l s='Add to Compare'}</a>
            							</div>
            						{/if}
                                    </div>
            					</div>
                            </div>
                        </div>
                    </div>
                </li>
                </ul>
           {/foreach}
        {elseif (isset($expired_warning) && $expired_warning|count_characters > 0)}
            <div id="discountproducts_list">
                <p class="alert alert-warning">{$expired_warning}</p>
        {/if}
     </div>
</section>
