{if $description_products && $description_products|@count >0}
<div class="home-product-single">
    
        {foreach from=$description_products item=product name=products}
        
        
        
            {assign var=data_time value= SimpleCategory::toItemDateTime($product.specific_prices.to)}
            
            
            
            
            {if (bool)Configuration::get('PS_DISABLE_OVERRIDES')}
				{assign  var='over' value=0}
			{else}
				{assign  var='over' value=1}
				{assign  var='rate' value=Product::getRatings($product.id_product)}
			{/if}
			{$imginfo = Image::getImages(Language::getIdByIso($lang_iso),$product.id_product)}
		    {assign var='new_idimg' value=''}
		    {foreach from=$imginfo item=imgitem}
		        {if !$imgitem['cover']}
		            {assign var='new_idimg' value="`$imgitem['id_product']`-`$imgitem['id_image']`"}
		            {break}
		        {/if}
		    {/foreach}
									
			<div class="product clearfix" itemscope itemtype="http://schema.org/Product">
		        <figure class="product-image-container">
		        	<a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
		        		<img class="product-image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
		        		{if !empty($new_idimg)}
		                    <img class="product-image-hover" src="{$link->getImageLink($product.link_rewrite, $new_idimg, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
		                {else}
		                    <img class="product-image-hover" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
		                {/if}				            		
		        	</a>
		        	<!-- - - - - - - - - - - - - - Countdown - - - - - - - - - - - - - - - - -->
        			<div class="countdown timer-list" data-time={$data_time.untilTime} data-year="{$data_time.year}" data-month="{$data_time.month}" data-day="{$data_time.day}" data-hours="{$data_time.hour}" data-minutes="{$data_time.minute}" data-seconds="{$data_time.second}"></div>
        			<!-- - - - - - - - - - - - - - End countdown - - - - - - - - - - - - - - - - -->
		        </figure>
		        <div class="product-content">
		        	<div class="ratings-container">
		           		{if $over == 1}
		                  	<div class="ratings" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
			                	<div class="ratings-result" data-result="{$rate.avg}"></div>
			                	<meta itemprop="worstRating" content="{$rate.min}">
			                	<meta itemprop="ratingValue" content="{$rate.avg}">
			                	<meta itemprop="bestRating" content="{$rate.max}">
			                	<meta itemprop="reviewCount" content="{$rate.review}">
			              	</div>
			              	{if isset($view_rate_total) && $view_rate_total == 1}
			              	<div class="ratings-amount">{$rate.review} review(s)</div>
			              	{/if}
		              	{else}
		              		{hook h='displayProductListReviews' product=$product}
		              	{/if}              	
					</div>
		           	<h3 class="product-name" itemprop="name"><a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">{$product.name|escape:'html':'UTF-8'}</a></h3>				               
		           	
					<div class="product-price-container" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
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
					
					<div class="actions-container clearfix" style="margin-top: 18px;">
		             	{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
							{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
								{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
								<a class="ajax_add_to_cart_button product-add-btn button_111_hover" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity > 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
									{l s='Add to Cart' mod='simplecategory'}
								</a>
							{else}
								<span class="ajax_add_to_cart_button product-add-btn disabled">
									{l s='Add to Cart' mod='simplecategory'}
								</span>
							{/if}
						{/if}
						<div class="links-container links-visibled">					
							{hook h='displayProductListFunctionalButtons' product=$product}	                        
							{if isset($comparator_max_item) && $comparator_max_item}
								<a href="{$product.link|escape:'html':'UTF-8'}" title="{l s='Add to compare'}" class="add_to_compare link-compare product-btn product-compare second_button_111_hover" data-id-product="{$product.id_product}"></a>
							{/if}	
						</div>
		             </div>
					
		        </div>
		     </div>
            
            
            
            
            
            
            {*}
            
            
            {$imginfo = Image::getImages(Language::getIdByIso($lang_iso),$product.id_product)}
            {assign var='new_idimg' value=''}
            {foreach from=$imginfo item=imgitem}
                {if !$imgitem['cover']}
                    {assign var='new_idimg' value="`$imgitem['id_product']`-`$imgitem['id_image']`"}
                    {break}
                {/if}
            {/foreach}
            
                
                <div itemscope itemtype="http://schema.org/Product">                    
                    <div class="product-image-wrapper">
                        {if isset($product.on_sale) && $product.on_sale == 1}
                            <span class="label-icon sale-label">{l s='Sale'}</span>
                        {/if}
                        {if isset($product.new) && $product.new == 1}
                            <span class="label-icon new-label{if isset($product.on_sale) && $product.on_sale == 1} second-label{/if}">{l s='New'}</span>
    					{/if}
                        <a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" class="product-image alt-image-effect">
                            <img class="cat-main-img" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                            {if !empty($new_idimg)}
                                <img class="alt-image" src="{$link->getImageLink($product.link_rewrite, $new_idimg, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                            {else}
                                <img class="alt-image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                            {/if}
                        </a>
                        <!-- - - - - - - - - - - - - - Countdown - - - - - - - - - - - - - - - - -->
            			<div class="countdown timer-list" data-time={$data_time.untilTime} data-year="{$data_time.year}" data-month="{$data_time.month}" data-day="{$data_time.day}" data-hours="{$data_time.hour}" data-minutes="{$data_time.minute}" data-seconds="{$data_time.second}"></div>
            			<!-- - - - - - - - - - - - - - End countdown - - - - - - - - - - - - - - - - -->
                    </div>
                    
                    <h2 itemprop="name" class="product-name">
                        {if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
        				<a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
        					{$product.name|truncate:32:''|escape:'html':'UTF-8'}
        				</a>
                   </h2>
                   <div class="price-box" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
    						<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
                            {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
    							{hook h="displayProductPriceBlock" product=$product type="old_price"}
                                <p class="old-price">
                                    <span class="price">
                                        {displayWtPrice p=$product.price_without_reduction}
                                    </span>
                                </p>							
                                <p class="special-price" itemprop="price">
                                    <span class="price">
                                        {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                    </span>
                                </p>
                            {else}
                                <span class="regular-price" itemprop="price">
                                    <span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>                                    
                                </span>
                            {/if}
    					{/if}
                    </div> 
                    <div class="actions-container">
                        {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
    						{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
    							{if isset($static_token)}
    								<a class="button ajax_add_to_cart_button btn-cart product-add-btn" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}">
    									<span><span>{l s='Add to cart'}</span></span>
    								</a>
    							{else}
    								<a class="button ajax_add_to_cart_button btn-cart product-add-btn" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}">
    									<span><span>{l s='Add to cart'}</span></span>
    								</a>
    							{/if}
    						{else}
    							<span class="button ajax_add_to_cart_button btn-cart product-add-btn disabled">
    								<span><span>{l s='Add to cart'}</span></span>
    							</span>
    						{/if}
    					{/if}
                        <div class="links-container links-visibled">
                        {hook h='displayProductListFunctionalButtons' product=$product}
                        {if isset($comparator_max_item) && $comparator_max_item}
    					  <a class="add_to_compare product-btn link-compare" title="{l s="Add to compare"}" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}">{l s='Add to compare'}</a>
    					{/if}
                        </div>
                    </div>                    
               </div>
            	{*}
            
            {/foreach}
        
    
</div>
{/if}