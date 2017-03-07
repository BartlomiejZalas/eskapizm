<section class="section_offset animated transparent simplecategory" data-animation="fadeInDown">
	{if $display_name == '1'}<h3 class="widget_title" style="color: {$module_color}">{$module_name}</h3>{/if}
	

	<!-- - - - - - - - - - - - - - Carousel of today's deals - - - - - - - - - - - - - - - - -->
	{if isset($module_products) && $module_products|@count}
	<div class="owl_carousel widgets_carousel">

		<!-- - - - - - - - - - - - - - Product - - - - - - - - - - - - - - - - -->
		{foreach from=$module_products item=product name=products}
		{assign var=data_time value= SimpleCategory::toItemDateTime($product.specific_prices.to)}
		<div class="product_item" itemscope itemtype="http://schema.org/Product">

			<!-- - - - - - - - - - - - - - Thumbnail - - - - - - - - - - - - - - - - -->
			
			<div class="image_wrap">
				<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
					<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
				</a>				
				<!-- - - - - - - - - - - - - - Product actions - - - - - - - - - - - - - - - - -->

				<div class="actions_wrap">

					<div class="centered_buttons">
						{if isset($quick_view) && $quick_view}							
							<a href="{$product.link|escape:'html':'UTF-8'}" data-rel="{$product.link|escape:'html':'UTF-8'}" class="button_dark_grey middle_btn quick_view quick-view" data-modal-url="modals/quick_view.html">{l s='Quick view' mod='simplecategory'}</a>
						{/if}
						{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
							{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
								{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
								<a class="ajax_add_to_cart_button button_blue middle_btn" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" data-rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity > 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
									{l s='Add to cart'}
								</a>
							{else}
								<a class="ajax_add_to_cart_button button_blue middle_btn disabled">
									{l s='Add to cart'}
								</a>
							{/if}
						{/if}

						<!-- <a href="#" class="button_blue middle_btn add_to_cart">Add to Cart</a> -->

					</div><!--/ .centered_buttons -->
					<a class="wishlistProd_{$product.id_product|intval} button_dark_grey middle_btn def_icon_btn add_to_wishlist tooltip_container" href="#" data-rel="{$product.id_product|intval}" onclick="WishlistCart('wishlist_block_list', 'add', '{$product.id_product|intval}', false, 1); return false;">
						<span class="tooltip right">{l s="Add to Wishlist" mod='simplecategory'}</span>
					</a>			
					{if isset($comparator_max_item) && $comparator_max_item}
                        {if $product.id_product|in_array:$compareProductIds}
                            <a class="button_dark_grey middle_btn def_icon_btn add_to_compare tooltip_container compare-checked" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}"><span class="tooltip left">{l s='Add to Compare'}</span></a>
                        {else}
                            <a class="button_dark_grey middle_btn def_icon_btn add_to_compare tooltip_container" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}"><span class="tooltip left">{l s='Add to Compare'}</span></a>
                        {/if}										
																
					{/if}
					<!--
					<a href="#" class="button_dark_grey middle_btn def_icon_btn add_to_wishlist tooltip_container"><span class="tooltip right">Add to Wishlist</span></a>

					<a href="#" class="button_dark_grey middle_btn def_icon_btn add_to_compare tooltip_container"><span class="tooltip left">Add to Compare</span></a>
					-->
				</div><!--/ .actions_wrap-->
				
				<!-- - - - - - - - - - - - - - End of product actions - - - - - - - - - - - - - - - - -->

			</div><!--/. image_wrap-->

			<!-- - - - - - - - - - - - - - End thumbnail - - - - - - - - - - - - - - - - -->

			<!-- - - - - - - - - - - - - - Label - - - - - - - - - - - - - - - - -->
			{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
				<div class="label_offer percentage">
					{if $product.specific_prices.reduction_type == 'percentage'}
						<div>{$product.specific_prices.reduction * 100}%</div>{l s='OFF' mod='simplecategory'}
					{/if}
					
				</div>											
			{/if}

			<!-- - - - - - - - - - - - - - End label - - - - - - - - - - - - - - - - -->

			<!-- - - - - - - - - - - - - - Countdown - - - - - - - - - - - - - - - - -->

			<div class="countdown" data-year="{$data_time.year}" data-month="{$data_time.month}" data-day="{$data_time.day}" data-hours="{$data_time.hour}" data-minutes="{$data_time.minute}" data-seconds="{$data_time.second}"></div>

			<!-- - - - - - - - - - - - - - End countdown - - - - - - - - - - - - - - - - -->

			<!-- - - - - - - - - - - - - - Product description - - - - - - - - - - - - - - - - -->

			<div class="description">

				<p>
					<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
						{$product.name|escape:'html':'UTF-8'}
					</a>
				</p>

				<div class="clearfix product_info">

					<!-- - - - - - - - - - - - - - Product rating - - - - - - - - - - - - - - - - -->

					{*}<ul class="rating alignright">{*}
						{hook h='displayProductListReviews' product=$product}
					{*}</ul>{*}

					<!-- - - - - - - - - - - - - - End product rating - - - - - - - - - - - - - - - - -->

					<p class="product_price alignleft">
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}												
							{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								{hook h="displayProductPriceBlock" product=$product type="old_price"}
								<s>{displayWtPrice p=$product.price_without_reduction}</s>
							{/if}
							<b>{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</b>														
							{hook h="displayProductPriceBlock" product=$product type="price"}
							{hook h="displayProductPriceBlock" product=$product type="unit_price"}
						{/if}	
						
					</p>

				</div><!--/ .clearfix.product_info-->

			</div>

			<!-- - - - - - - - - - - - - - End of product description - - - - - - - - - - - - - - - - -->

		</div>	
		{/foreach}
		
		
		<!-- - - - - - - - - - - - - - End of product - - - - - - - - - - - - - - - - -->

	</div><!--/ .widgets_carousel-->
	{/if}
	

	<!-- - - - - - - - - - - - - - End of carousel of today's deals - - - - - - - - - - - - - - - - -->

	<!-- - - - - - - - - - - - - - View all deals of the day - - - - - - - - - - - - - - - - -->


	<!-- - - - - - - - - - - - - - End of view all deals of the day - - - - - - - - - - - - - - - - -->

</section>