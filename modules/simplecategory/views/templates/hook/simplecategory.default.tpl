<section class="section_offset animated transparent simplecategory-default" data-animation="fadeInDown">
	{if $display_name == '1'}<h3 class="module-name">{$module_name}</h3>{/if}	
	{if isset($groups) && $groups|@count >0}
	<div class="tabs type_2 products">
		<ul class="tabs_nav clearfix">
			{foreach from=$groups item=group name=groups}
				{if $smarty.foreach.groups.first}
					<li><a href="#tab-{$hook_id}-{$module_id}-{$group.id}">{$group.name}</a></li>
				{else}
					<li><a href="#tab-{$hook_id}-{$module_id}-{$group.id}">{$group.name}</a></li>
				{/if}
			{/foreach}
		</ul>
		<div class="tab_containers_wrap">
			{foreach from=$groups item=group name=groups}
				<div id="tab-{$hook_id}-{$module_id}-{$group.id}" class="tab_container">
					<div class="owl_carousel carousel_in_tabs">
						{if isset($group.products) && $group.products|@count}
						
							{foreach from=$group.products item=product name=products}
							<div class="product_item type_2" itemscope itemtype="http://schema.org/Product" >
								<div class="image_wrap">
									<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
										<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
									</a>
									{if isset($quick_view) && $quick_view}
										<div class="actions_wrap">
											<div class="centered_buttons">
												<a href="{$product.link|escape:'html':'UTF-8'}" data-rel="{$product.link|escape:'html':'UTF-8'}" class="button_dark_grey middle_btn quick_view quick-view" data-modal-url="modals/quick_view.html">{l s='Quick view' mod='simplecategory'}</a>
											</div>			
										</div>										
									{/if}		
								</div>		
								{if isset($product.new) && $product.new == 1}
									<div class="label_new">{l s='New'}</div>
								{/if}
								{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
									<div class="label_hot new-{$product.new}">{l s='Sale'}</div>
								{/if}
								<div class="description" >
									<h5 itemprop="name">
										{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
										<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
											{$product.name|escape:'html':'UTF-8'}
										</a>
									</h5>
									<div class="clearfix product_info">
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
										<div class="rating alignright">
											{hook h='displayProductListReviews' product=$product}
										</div>
									</div>
								</div>
								<div class="buttons_row">
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
									<a class="wishlistProd_{$product.id_product|intval} button_dark_grey middle_btn def_icon_btn add_to_wishlist tooltip_container" href="#" data-rel="{$product.id_product|intval}" onclick="WishlistCart('wishlist_block_list', 'add', '{$product.id_product|intval}', false, 1); return false;">
										<span class="tooltip top">{l s="Add to Wishlist" mod='simplecategory'}</span>
									</a>									
									{if isset($comparator_max_item) && $comparator_max_item}
                                        {if $product.id_product|in_array:$compareProductIds}
                                            <a class="button_dark_grey middle_btn def_icon_btn add_to_compare tooltip_container compare-checked" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}"><span class="tooltip top">{l s='Add to Compare'}</span></a>
                                        {else}
                                            <a class="button_dark_grey middle_btn def_icon_btn add_to_compare tooltip_container" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}"><span class="tooltip top">{l s='Add to Compare'}</span></a>
                                        {/if}										
																				
									{/if}
									<!--
									<button class="button_dark_grey middle_btn def_icon_btn add_to_wishlist tooltip_container"><span class="tooltip top">Add to Wishlist</span></button>									
									<button class="button_dark_grey middle_btn def_icon_btn add_to_compare tooltip_container"><span class="tooltip top">Add to Compare</span></button>
									-->
								</div>
							</div>
							{/foreach}
							
						{else}
							<div>{l s='Sorry! There are no products' mod='simplecategory'}</div>
						{/if}
					</div>		
					<footer class="bottom_box">
						<a href="{$link->getCategoryLink({$group.category_id})}" class="button_grey middle_btn">{l s='View All Products' mod='simplecategory'}</a>
					</footer>
				</div>
			{/foreach}
		</div>
	</div>
	{/if}
</section>






