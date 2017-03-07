<div class="product-group">
	<div class="container">
		<h2 class="small-title text-center">{$simplecategory_item.name}</h2>    
	    {if $simplecategory_item.products|@count >0}    
	    <div class="row">
			<div class="col-sm-6">
	        	{$simplecategory_item.description}                	                  
	        </div>
	        <div class="col-md-6">
	            <div class="row">
	            	<div class="col-sm-6">    
	                {foreach from=$simplecategory_item.products item=product name=products}	     
	                	{if (bool)Configuration::get('PS_DISABLE_OVERRIDES')}
							{assign  var='over' value=0}
						{else}
							{assign  var='over' value=1}
							{assign  var='rate' value=Product::getRatings($product.id_product)}
						{/if}
						{*}
						{$imginfo = Image::getImages(Language::getIdByIso($lang_iso),$product.id_product)}
					    {assign var='new_idimg' value=''}
					    {foreach from=$imginfo item=imgitem}
					        {if !$imgitem['cover']}
					            {assign var='new_idimg' value="`$imgitem['id_product']`-`$imgitem['id_image']`"}
					            {break}
					        {/if}
					    {/foreach}
					    {*}               
	                    <div class="product product4 dark" itemscope itemtype="http://schema.org/Product">
	                        <figure class="product-image-container">
	                        	<a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
	                        		<img class="product-image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
	                        	</a>
	                        </figure>
	                        <div class="product-meta">
	                           <div class="product-meta-inner">
	                              <h3 class="product-name text-left"><a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a></h3>
	                              <div class="product-price-container text-left">
	                              	{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
										<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
						                {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
											{hook h="displayProductPriceBlock" product=$product type="old_price"}
						                    <span class="product-old-price">{displayWtPrice p=$product.price_without_reduction}</span>
						                    <span class="product-price" itemprop="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>                                            
						                {else}
						                    <span class="product-price" itemprop="price">
						                        {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}                                    
						                    </span>
						                {/if}
									{/if}
	                              </div>
	                              <div class="product-action-container clearfix">
	                                 {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
										{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
											{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
											<a class="ajax_add_to_cart_button product-add-btn" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity > 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
												<span class="add-btn-text">{l s='Add to Cart' mod='simplecategory'}</span> <span class="product-btn product-cart">{l s='Cart' mod='simplecategory'}</span>
											</a>
										{else}
											<span class="ajax_add_to_cart_button product-add-btn disabled">
												<span class="add-btn-text">{l s='Add to Cart' mod='simplecategory'}</span> <span class="product-btn product-cart">{l s='Cart' mod='simplecategory'}</span>
											</span>
										{/if}
									{/if}
	                                 <div class="product-action-inner">
	                                 	{hook h='displayProductListFunctionalButtons' product=$product}	                        
										{if isset($comparator_max_item) && $comparator_max_item}
											<a href="{$product.link|escape:'html':'UTF-8'}" title="{l s='Add to compare'}" class="add_to_compare link-compare product-btn product-compare" data-id-product="{$product.id_product}"></a>								  
										{/if}
	                                 	
	                                 	
	                                 	</div>
	                              </div>
	                           </div>
	                        </div>
	                     </div>
	                     
	                     
					    {if ($smarty.foreach.products.index % 2 ) && !$smarty.foreach.products.last} 
							</div>
							<div class="col-sm-6">
						{/if}	
	                     
	                     
	                     
	                {/foreach}
	                </div>                        
	            </div>
	        </div>
	     </div> 
	    {/if}
	</div>
</div>
