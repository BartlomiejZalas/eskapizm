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

{capture name=path}{l s='Price drop'}{/capture}

<h1 class="page-heading product-listing">{l s='Price drop'}</h1>

{if $products}
	<div class="content_sortPagiBar">
    	<div class="sortPagiBar clearfix">
            {if isset($orderby) AND isset($orderway)}
            <ul class="display hidden-xs">
            	<li class="display-title">{l s='View as:'}</li>
                <li id="grid"><a rel="nofollow" href="#" title="{l s='Grid'}"><i class="icon-th-large"></i>{l s='Grid'}</a></li>
                <li id="list"><a rel="nofollow" href="#" title="{l s='List'}"><i class="icon-th-list"></i>{l s='List'}</a></li>
            </ul>
            {/if}
			{include file="./product-sort.tpl"}
			{include file="./nbr-product-page.tpl"}
		</div>
    	<div class="top-pagination-content clearfix">
        	{include file="./product-compare.tpl"}
            {include file="$tpl_dir./pagination.tpl"}
        </div>
	</div>

	{include file="./product-list.tpl" products=$products}

	<div class="content_sortPagiBar">
        <div class="bottom-pagination-content clearfix">
        	{include file="./product-compare.tpl"}
			{include file="./pagination.tpl" paginationId='bottom'}
        </div>
	</div>
	{else}
	<p class="alert alert-warning">{l s='No price drop'}</p>
{/if}
