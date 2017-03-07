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
{include file="$tpl_dir./errors.tpl"}
{if isset($category)}
	{if $category->id AND $category->active}
		<h1 class="page-heading{if (isset($subcategories) && !$products) || (isset($subcategories) && $products) || !isset($subcategories) && $products} product-listing{/if}{if !$hide_left_column and !$hide_right_column} h1-both-width{/if}{if $hide_left_column and $hide_right_column} h1-full-width{/if}"><span class="cat-name">{$category->name|escape:'html':'UTF-8'}{if isset($categoryNameComplement)}&nbsp;{$categoryNameComplement|escape:'html':'UTF-8'}{/if}</span></h1>
        {if $category->description}
            {if Tools::strlen($category->description) > 100}
                <div id="category_description_short" class="rte {if !$hide_left_column and !$hide_right_column} desc-both-width{/if}">{$category->description|strip_tags|truncate:100:'...'}&nbsp;
                    <a href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}" class="lnk_more">{l s='More'}</a></div>
                <div id="category_description_full" class="unvisible rte {if !$hide_left_column and !$hide_right_column} desc-both-width{/if}">{$category->description}</div>
            {else}
                <div class="category_description">{$category->description|strip_tags}</div>
            {/if}
        {/if}
        {*}{if isset($subcategories) && $subcategories|@count>0}
		<!-- Subcategories -->
		<div id="subcategories" class="clearBoth clearfix">
			<p class="subcategory-heading">{l s='Subcategories'}</p>
			<ul class="clearfix">
			{foreach from=$subcategories item=subcategory}
				<li>
                	<div class="subcategory-image">
						<a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategory.name|escape:'html':'UTF-8'}" class="img">
						{if $subcategory.id_image}
							<img class="replace-2x img-responsive" src="{$link->getCatImageLink($subcategory.link_rewrite, $subcategory.id_image, 'medium_default')|escape:'html':'UTF-8'}" alt="" />
						{else}
							<img class="replace-2x img-responsive" src="{$img_cat_dir}default-medium_default.jpg" alt="" />
						{/if}
					</a>
                   	</div>
					<h5><a class="subcategory-name" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|truncate:25:'...'|escape:'html':'UTF-8'|truncate:350}</a></h5>
					{if $subcategory.description}
						<div class="cat_desc">{$subcategory.description}</div>
					{/if}
				</li>
			{/foreach}
			</ul>
		</div>
		{/if}{*}
		{if $products}
			<div class="content_sortPagiBar clearfix">
            	<div class="sortPagiBar clearfix">
            		{*include file="./product-sort.tpl"*}
                    {if isset($orderby) AND isset($orderway)}
                    <ul class="display hidden-xs">
                    	<li class="display-title">{l s='View as:'}</li>
                        <li id="grid"><a rel="nofollow" href="#" title="{l s='Grid'}"><i class="icon-th-large"></i>{l s='Grid'}</a></li>
                        <li id="list"><a rel="nofollow" href="#" title="{l s='List'}"><i class="icon-th-list"></i>{l s='List'}</a></li>
                    </ul>
                    {/if}
				</div>
			</div>
			{include file="./product-list.tpl" products=$products}
			<div class="content_sortPagiBar">
				<div class="bottom-pagination-content clearfix">
                    {include file="./product-compare.tpl" paginationId='bottom'}
                    {include file="./product-sort.tpl"}
                    {include file="./nbr-product-page.tpl"}
                    {include file="./pagination.tpl" paginationId='bottom'}
				</div>
			</div>
        {else}
            <p class="warning clearBoth">{l s='Sorry! There are no products in this category.'}</p>
		{/if}
	{elseif $category->id}
		<p class="alert alert-warning">{l s='This category is currently unavailable.'}</p>
	{/if}
{/if}