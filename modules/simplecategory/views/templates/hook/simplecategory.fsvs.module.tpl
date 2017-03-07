{if $simplecategory_item.products &&  $simplecategory_item.products|@count >0}
	<div id="vslider_body">	
		{foreach from=$simplecategory_item.products item=product name=products}    
			{if (bool)Configuration::get('PS_DISABLE_OVERRIDES')}
				{assign  var='over' value=0}
			{else}
				{assign  var='over' value=1}
				{assign  var='rate' value=Product::getRatings($product.id_product)}
			{/if}
			{if $product.specific_prices.to != '0000-00-00 00:00:00'}
				{assign var=count_down value= true}
				{assign var=data_time value= SimpleCategory::toItemDateTime($product.specific_prices.to)}
			{else}
				{assign var=data_time value= ''}
				{assign var=count_down value= false}
			{/if}  
			{assign var='specific' value=false}
	        <div class="slide">
	        	<div class="row">
	        		<div class="product-image col-md-6">
	        			<img class="img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
	        			{if isset($product.specific_prices.reduction) && $product.specific_prices.reduction}
		                    {if $product.specific_prices.reduction_type == 'percentage'}   
		                    	{assign var='specific' value=true}
		                    	<span class="label-icon sale-label">-{$product.specific_prices.reduction * 100}%</span>
		                    {else}	
		                    	<span class="label-icon sale-label">-{$product.specific_prices.reduction|intval}<span>&nbsp;{$currency->sign}</span></span>
		                    {/if}		                
		                {/if}
	        		</div>
	        		<div class="product-shop col-md-6">
	        			{if $specific == true}<div class="timer-caption sub-title">{l s='Deal Of The Day' mod='simplecategory'}</div>{/if}
	        			{if $count_down == true}	        				
	        				<div class="countdown timer-list" data-time={$data_time.untilTime} data-year="{$data_time.year}" data-month="{$data_time.month}" data-day="{$data_time.day}" data-hours="{$data_time.hour}" data-minutes="{$data_time.minute}" data-seconds="{$data_time.second}"></div>
	        			{/if}
	        			<h2 class="product-name"><a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a></h2>
	        			
	        			
	        			<div class="price-box">
                        	<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
			                {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								{hook h="displayProductPriceBlock" product=$product type="old_price"}
			                    <p class="old-price">
			                    	<span class="price-label">{l s='Regular Price:' mod='simplecategory'}</span>
			                    	<span class="price" id="old-price-84">{displayWtPrice p=$product.price_without_reduction}</span>			                    	
			                    </p>
								<p class="special-price">
				                    <span class="price-label">{l s='Special Price:' mod='simplecategory'}</span>
				                	<span class="price" itemprop="price" id="product-price-84">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>
				                </p>                                            
			                {else}
			                    <p class="special-price">
				                    <span class="price-label">{l s='Regular Price:' mod='simplecategory'}</span>
				                	<span class="price" itemprop="price" id="product-price-84">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>
				                </p>
			                {/if}                    
				           <div class="product-information">
					           	<div class="ratings-container">
					                <div class="ratings">
					                   {if $over == 1}
					                  	<div class="ratings" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
						                	<div class="ratings-result" data-result="{$rate.avg}"></div>
						                	<meta itemprop="worstRating" content="{$rate.min}">
						                	<meta itemprop="ratingValue" content="{$rate.avg}">
						                	<meta itemprop="bestRating" content="{$rate.max}">
						                	<meta itemprop="reviewCount" content="{$rate.review}">
						              	</div>
					              	{else}
					              		{hook h='displayProductListReviews' product=$product}
					              	{/if} 
					                </div>
					             </div>
							   	<div class="sku-wrapper">SKU:{$product.ean13}</div>
							</div>             
				            <div class="short-description">
	                            <div class="std">{$product.description_short}</div>
	                        </div>       
				    		<div class="actions-wrapper">
				    			{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
									{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
										{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
										<a class="ajax_add_to_cart_button button btn-cart product-add-btn second_button_111_hover" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity > 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
											{l s='Add to Cart' mod='simplecategory'}
										</a>
									{else}
										<span class="ajax_add_to_cart_button button btn-cart product-add-btn disabled">
											{l s='Add to Cart' mod='simplecategory'}
										</span>
									{/if}
								{/if}
								{hook h='displayProductListFunctionalButtons' product=$product}	                        
								{if isset($comparator_max_item) && $comparator_max_item}
									<a href="{$product.link|escape:'html':'UTF-8'}" title="{l s='Add to compare'}" class="add_to_compare link-compare product-btn product-compare second_button_111_hover" data-id-product="{$product.id_product}"></a>
								{/if}								
	                        </div>
				        </div>
	        		</div>
	        	</div>
	        </div>
	    {/foreach}
    </div>
    {addJsDefL name=txt_years}{l s='years' mod='simplecategory' js=1}{/addJsDefL}
	{addJsDefL name=txt_month}{l s='month' mod='simplecategory' js=1}{/addJsDefL}
	{addJsDefL name=txt_weeks}{l s='weeks' mod='simplecategory' js=1}{/addJsDefL}
	{addJsDefL name=txt_days}{l s='days' mod='simplecategory' js=1}{/addJsDefL}
	{addJsDefL name=txt_hours}{l s='hours' mod='simplecategory' js=1}{/addJsDefL}
	{addJsDefL name=txt_min}{l s='min' mod='simplecategory' js=1}{/addJsDefL}
	{addJsDefL name=txt_sec}{l s='sec' mod='simplecategory' js=1}{/addJsDefL}
{/if}
