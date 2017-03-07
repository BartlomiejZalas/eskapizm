<div class="simplecategory">
	<div class="list-title"><div class="cat-list-name sub-title {$custom_class}" style="border-top:2px solid {$module_color};color:{$module_color};">{$module_name}</div></div>
	<div class="category-home-list-wrapper cms_include">		
	    {$simplecategory_item.description}
		<div class="category-home-list ">
	    <div class="row">
	    	{if $simplecategory_item.products &&  $simplecategory_item.products|@count >0}
	    	<ul class="66 products-grid grid-type-1 grid-type-4" style="opacity: 1; display: block;">
	    		{foreach from=$simplecategory_item.products item=product name=products}
		    		{$imginfo = Image::getImages(Language::getIdByIso($lang_iso),$product.id_product)}
		            {assign var='new_idimg' value=''}
		            {foreach from=$imginfo item=imgitem}
		                {if !$imgitem['cover']}
		                    {assign var='new_idimg' value="`$imgitem['id_product']`-`$imgitem['id_image']`"}
		                    {break}
		                {/if}
		            {/foreach}
		            
		            
		            <li class="item">		            	
		            	<div class="product-container" itemscope itemtype="http://schema.org/Product">		            		
		            		<div class="product-image-wrapper">
		            			{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
									<span class="label-icon sale-label">Sale</span>
								{/if}								
								<a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" class="product-image alt-image-effect">
									<img class="cat-main-img" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
									{if !empty($new_idimg)}
										<img class="alt-image" src="{$link->getImageLink($product.link_rewrite, $new_idimg, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
		                            {else}
		                                <img class="alt-image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
		                            {/if}									
								</a>
								{if isset($quick_view) && $quick_view}								
								<a class="sw-product-quickview product-btn" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}">
									<span>{l s='Quick view'}</span>
								</a>
								{/if}								
							</div>		            		
		            		<!-- ratings -->
							{hook h='displayProductListReviews' product=$product}							
		            		<!-- end ratings -->
		            		<h2 class="product-name">
		            			<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}</a>
		            		</h2>		            		
		            		{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							<div class="price-box">
								{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}									
									{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}										
										{hook h="displayProductPriceBlock" product=$product type="old_price"}
										<p class="old-price">
											<span class="price-label">Regular Price:</span>
											<span class="price" id="old-price-55">{displayWtPrice p=$product.price_without_reduction}</span>
										</p>										
										{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}										
										{if $product.specific_prices.reduction_type == 'percentage'}
											<!-- <span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span> -->
										{/if}
										<p class="special-price">
											<span class="price-label">Special Price</span>
											<span class="price" id="product-price-55">
												{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
											</span>
										</p>
									{else}										
										<span class="regular-price">
											{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
										</span>
									{/if}
									{hook h="displayProductPriceBlock" product=$product type="price"}
									{hook h="displayProductPriceBlock" product=$product type="unit_price"}
								{/if}
							</div>
							{/if}		            		
		        			<div class="actions-container">		        				
								{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
									{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
										{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
										<a class="ajax_add_to_cart_button button btn-cart product-add-btn" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity > 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
											<span><span>{l s='Add to cart'}</span></span>
										</a>
									{else}
										<span class="ajax_add_to_cart_button button btn-cart product-add-btn disabled">
											<span><span>{l s='Add to cart'}</span></span>
										</span>
									{/if}
								{/if}
								<div class="links-container links-visibled">
									{hook h='displayProductListFunctionalButtons' product=$product}                    
									{if isset($comparator_max_item) && $comparator_max_item}										
										<a class="add_to_compare product-btn link-compare"  title="{l s='Add to compare'}" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}"><span class="visible-list">{l s='Add to compare'}</span></a>										
									{/if}									
								</div>
		        			</div>
		            	</div>		            	
	    			</li>
		            
		            
	    		{/foreach}
				
			</ul>
	    	{/if}
	    </div>
	</div>
	    <script type="text/javascript">
	        //<![CDATA[
            
	        //]]>
	    </script>
	</div>
</div>
