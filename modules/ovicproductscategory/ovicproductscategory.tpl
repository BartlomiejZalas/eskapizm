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
{if count($categoryProducts) > 0 && $categoryProducts !== false}
<section class="page-product-box blockproductscategory">
	<h3 class="productscategory_h3 page-product-heading">{$categoryProducts|@count} {l s='Other products in the same category:' mod='ovicproductscategory'}</h3>
    <div id="scroll_group">
        {if count($categoryProducts) > 4}<a id="productscategory_scroll_right" class="next_slide btnbgcolorhoveronly" title="{l s='Next' mod='ovicproductscategory'}" href="javascript:{ldelim}{rdelim}">{l s='Next' mod='ovicproductscategory'}</a>{/if}
        {if count($categoryProducts) > 4}<a id="productscategory_scroll_left" class="prev_slide btnbgcolorhoveronly" title="{l s='Previous' mod='ovicproductscategory'}" href="javascript:{ldelim}{rdelim}">{l s='Previous' mod='ovicproductscategory'}</a>{/if}
    </div>
	<div id="productscategory_list" class="clearfix">
	   {include file="$tpl_dir./product-list.tpl" products=$categoryProducts id='productscategory_list_ul'}
 	</div>
</section>
{/if}