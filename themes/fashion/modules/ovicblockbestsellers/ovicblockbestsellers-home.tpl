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
* @author PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2014 PrestaShop SA

* @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
{if isset($best_sellers) && $best_sellers}
    {if isset($current_id_option)&& $current_id_option !=5}
        <ul id="blockbestsellers" class="blockbestsellers fade tab-pane">
        	<li class="list-container">
                {include file="$tpl_dir./product-list.tpl" products=$best_sellers}
            </li>
            <li class="loadmore-container">
                <div id="loadmore-bestsellers" class="loadmore-button outline-outward">
                    <h5>{l s='To view other products' mod='ovicblockbestsellers'}&#44;</h5>
                    <h2>{l s='You click ...' mod='ovicblockbestsellers'}</h2>
                </div>
                <input type="hidden" id="toppage" value="{$toppage}" />
                <input type="hidden" id="toptotal" value="{$toptotal|intval}" />
            </li>
        </ul>
    {else}
        <div id="blockbestsellers" class="blockbestsellers fade tab-pane">
            <div class="row">
            <div class="home5_owl">
            {include file="$tpl_dir./product-list5-owl.tpl" products=$best_sellers}
            </div>
             </div>
        </div>
    {/if}
    {addJsDef topnbItemsPerLine=4}
    {addJsDef topnbItemsPerLineTablet=3}
    {addJsDef topnbItemsPerLineMobile=2}
    {addJsDef toptotal=($toptotal)|intval}
    
{else}
<ul id="blockbestsellers" class="blockbestsellers tab-pane">
	<li class="alert alert-info">{l s='No best sellers at this time.' mod='ovicblockbestsellers'}</li>
</ul>
{/if}
