{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($products) && $products}
	{*define numbers of product per line in other page for desktop*}
	{if $page_name !='index' && $page_name !='product'}
        {if $hide_left_column xor $hide_right_column}
            {assign var='nbol' value=3}
        {else}
            {assign var='nbol' value=4}
        {/if}
		{assign var='nbItemsPerLine' value=$nbol}
		{assign var='nbItemsPerLineTablet' value=($nbol-1)}
		{assign var='nbItemsPerLineMobile' value=($nbol-2)}
	{else}
        {if $page_name =='index' && ($hide_left_column xor $hide_right_column)}
            {assign var='nbol' value=3}
        {else}
            {assign var='nbol' value=4}
        {/if}
		{assign var='nbItemsPerLine' value=$nbol}
		{assign var='nbItemsPerLineTablet' value=($nbol-1)}
		{assign var='nbItemsPerLineMobile' value=($nbol-2)}
	{/if}
	{*define numbers of product per line in other page for tablet*}
	{assign var='nbLi' value=$products|@count}
	{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
	{math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}
    <input type="hidden" class="case-width" value="{if ($hide_left_column and $hide_right_column)}full-width{elseif !$hide_left_column and !$hide_right_column}both-width{else}normal-width{/if}" />
	<!-- Products list -->
	<ul{if isset($id) && $id} id="{$id}"{/if} class="product_list grid row{if isset($class) && $class} {$class}{/if}{if isset($active) && $active == 1} active{/if}">
	{foreach from=$products item=product name=products}
		{math equation="(total%perLine)" total=$smarty.foreach.products.total perLine=$nbItemsPerLine assign=totModulo}
		{math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
		{math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
		{if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
		{if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
		{if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}
        {if $page_name !='index' && $page_name !='product'}
            {*assign var='liwidth' value="col-xs-12 col-sm-6 col-md-4"*}
            {if $hide_left_column xor $hide_right_column}
                {assign var='liwidth' value="col-xs-12 col-sm-6 col-md-4"}
            {else}
                {if $hide_left_column and $hide_right_column}
                    {assign var='liwidth' value="col-xs-12 col-sm-4 col-md-3"}
                {else}
                    {assign var='liwidth' value="col-xs-12 col-sm-12 col-md-6"}
                {/if}
            {/if}
        {else}
            {if $page_name =='index' && ($hide_left_column xor $hide_right_column)}
                {assign var='liwidth' value="col-xs-12 col-sm-6 col-md-4"}
            {else}
                {if $hide_left_column and $hide_right_column}
                    {assign var='liwidth' value="col-xs-12 col-sm-4 col-md-3"}
                {else}
                    {assign var='liwidth' value="col-xs-12 col-sm-12 col-md-6"}
                {/if}
            {/if}
        {/if}
		<li class="ajax_block_product{if ($hide_left_column and $hide_right_column)} full-width{/if} {$liwidth}{if $smarty.foreach.products.iteration%$nbItemsPerLine == 0} last-in-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLine == 1} first-in-line{/if}{if $smarty.foreach.products.iteration > ($smarty.foreach.products.total - $totModulo)} last-line{/if}{if $smarty.foreach.products.iteration%$nbItemsPerLineTablet == 0} last-item-of-tablet-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLineTablet == 1} first-item-of-tablet-line{/if}{if $smarty.foreach.products.iteration%$nbItemsPerLineMobile == 0} last-item-of-mobile-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLineMobile == 1} first-item-of-mobile-line{/if}{if $smarty.foreach.products.iteration > ($smarty.foreach.products.total - $totModuloMobile)} last-mobile-line{/if} {if !$hide_left_column and !$hide_right_column} both-width{/if}">
            {$imginfo = Image::getImages(Language::getIdByIso($lang_iso),$product.id_product)}
            {assign var='new_idimg' value=''}
            {foreach from=$imginfo item=imgitem}
                {if !$imgitem['cover']}
                    {assign var='new_idimg' value="`$imgitem['id_product']`-`$imgitem['id_image']`"}
                    {break}
                {/if}
            {/foreach}
			<div class="product-container" itemscope itemtype="http://schema.org/Product">
				<div class="left-block">
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
							<a title="{l s='Quick view'}" class="quick-view" href="{$product.link|escape:'html':'UTF-8'}" data-rel="{$product.link|escape:'html':'UTF-8'}">
								<span title="{l s='Quick view'}">{l s='Quick view'}</span>
							</a>
						{/if}
						{*if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							<div class="content_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
								{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
									<span itemprop="price" class="price product-price">
										{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
									</span>
									<meta itemprop="priceCurrency" content="{$priceDisplay}" />
									{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0} 
										<span class="old-price product-price">
											{displayWtPrice p=$product.price_without_reduction}
										</span>
										{if $product.specific_prices.reduction_type == 'percentage'}
											<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
										{/if}
									{/if}
								{/if}
							</div>
						{/if*}
						{if isset($product.new) && $product.new == 1}
							<span class="new-box">
								<span class="btnbgcolorhover new-label">{l s='New'}</span>
							</span>
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
							<span class="sale-box {if isset($product.new) && $product.new != 1}has-not-new{/if}">
								<span class="btnbgcolorhover sale-label">{l s='Sale!'}</span>
							</span>
						{/if}
					</div>
				</div>
				<div class="right-block">
					<h5 itemprop="name">
						{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
						<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
							{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
						</a>
					</h5>
					{hook h='displayProductListReviews' product=$product}
					<p class="product-desc" itemprop="description">
						{$product.description_short|strip_tags:'UTF-8'|truncate:360:'...'}
					</p>
					{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="content_price">
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							<span itemprop="price" class="price product-price">
								{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
							</span>
							<meta itemprop="priceCurrency" content="{$priceDisplay}" />
							{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
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
					<div class="button-container">
						{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
							{if ($product.allow_oosp || $product.quantity > 0)}
								{if isset($static_token)}
									<a class="btnbgcolorhover button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}">
										<span>{l s='Add to cart'}</span>
									</a>
								{else}
									<a class="btnbgcolorhover button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}">
										<span>{l s='Add to cart'}</span>
									</a>
								{/if}
							{else}
								<span class="btnbgcolorhover button ajax_add_to_cart_button btn btn-default disabled">
									<span>{l s='Add to cart'}</span>
								</span>
							{/if}
						{/if}
						<a itemprop="url" class="button lnk_view btn btn-default" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View'}">
							<span>{l s='More'}</span>
						</a>
					</div>
					{if isset($product.color_list)}
						<div class="color-list-container">{$product.color_list} </div>
					{/if}
					<div class="product-flags">
						{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							{if isset($product.online_only) && $product.online_only}
								<span class="online_only">{l s='Online only'}</span>
							{/if}
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
							{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
								<span class="discount">{l s='Reduced price!'}</span>
							{/if}
					</div>
					{if (!$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
						{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
							<span itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="availability">
								{if ($product.allow_oosp || $product.quantity > 0)}
									<span class="available-now">
										<link itemprop="availability" href="http://schema.org/InStock" />{l s='In Stock'}
									</span>
								{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
									<span class="available-dif">
										<link itemprop="availability" href="http://schema.org/LimitedAvailability" />{l s='Product available with different options'}
									</span>
								{else}
									<span class="out-of-stock">
										<link itemprop="availability" href="http://schema.org/OutOfStock" />{l s='Out of stock'}
									</span>
								{/if}
							</span>
						{/if}
					{/if}
				</div>
				{*}{if $page_name != 'index'}{*}
	 				<div class="btnbgcolor functional-buttons clearfix">

                        {if isset($quick_view) && $quick_view}
                             <div class="qv_container">
    							<a title="{l s='Quick view'}" class="btnbglightcolorhoveronly quick-view" href="{$product.link|escape:'html':'UTF-8'}" data-rel="{$product.link|escape:'html':'UTF-8'}">
    								{l s='Quick view'}
    							</a>
                            </div>
						{/if}
						{hook h='displayProductListFunctionalButtons' product=$product}
						{if isset($comparator_max_item) && $comparator_max_item}
							<div class="compare">
								<a class="btnbglightcolorhoveronly add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to Compare'}">{l s='Add to Compare'}</a>
							</div>
						{/if}
					</div>
				{*}{/if}{*}
			</div><!-- .product-container> -->
		</li>
	{/foreach}
	</ul>
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{/if}