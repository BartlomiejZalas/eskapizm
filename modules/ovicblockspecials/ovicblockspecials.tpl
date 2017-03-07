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

<!-- MODULE Block specials -->
<div id="special_block_right" class="block">
	<p class="title_block">
        <a href="{$link->getPageLink('prices-drop')|escape:'html':'UTF-8'}" title="{l s='Specials' mod='ovicblockspecials'}">
            {l s='Specials' mod='ovicblockspecials'}
        </a>
        <span class="sub_title">{l s='Collection by Designer' mod='ovicblockspecials'}</span>
    </p>
	<div class="block_content products-block">
    {if $specials}
		<div id="special_block_right_slide" class="owl-carousel">
        {foreach from=$specials item=special name=specials}
            {if $special@iteration == 1}
            	<div class="clearfix slide-item">
            {/if}
            <div class="clearfix product-item">
            	<a class="products-block-image" href="{$special.link|escape:'html':'UTF-8'}">
                    <img
                    class="replace-2x img-responsive"
                    src="{$link->getImageLink($special.link_rewrite, $special.id_image, 'medium_default')|escape:'html':'UTF-8'}"
                    alt="{$special.legend|escape:'html':'UTF-8'}"
                    title="{$special.name|escape:'html':'UTF-8'}" />
                </a>
                <div class="product-content">
                	<h5>
                        <a class="product-name" href="{$special.link|escape:'html':'UTF-8'}" title="{$special.name|escape:'html':'UTF-8'}">
                            {$special.name|escape:'html':'UTF-8'}
                        </a>
                    </h5>
                    <div class="price-box">
                    	{if !$PS_CATALOG_MODE}
                        	<span class="price special-price">
                                {if !$priceDisplay}
                                    {displayWtPrice p=$special.price}{else}{displayWtPrice p=$special.price_tax_exc}
                                {/if}
                            </span>
                             <span class="old-price">
                                {if !$priceDisplay}
                                    {displayWtPrice p=$special.price_without_reduction}{else}{displayWtPrice p=$priceWithoutReduction_tax_excl}
                                {/if}
                            </span>
                        {/if}
                    </div>
                </div>
             </div>
             {if $special@iteration%3 == 0}
                </div><div class="clearfix slide-item">
             {/if}
             {if $smarty.foreach.specials.last}
                </div>
             {/if}
        {/foreach}
		</div>
    {else}
		<div>{l s='No specials at this time.' mod='ovicblockspecials'}</div>
    {/if}
	</div>
</div>
<!-- /MODULE Block specials -->
