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
{if isset($new_products) && $new_products}
    {if isset($current_id_option)&& $current_id_option !=5}
    <ul id="blocknewproducts" class="blocknewproducts fade tab-pane">
    	<li class="list-container">
            {include file="$tpl_dir./product-list.tpl" products=$new_products}
        </li>
        <li class="loadmore-container">
            <div id="loadmore-newproducts" class="loadmore-button outline-outward">
                <h5>{l s='To view other products' mod='ovicblocknewproducts'}&#44;</h5>
                <h2>{l s='You click ...' mod='ovicblocknewproducts'}</h2>
            </div>
            <input type="hidden" id="newspage" value="{$newspage}" />
            <input type="hidden" id="newtotal" value="{$newtotal|intval}" />
        </li>
    </ul>
    {else}
        <div id="blocknewproducts" class="blocknewproducts fade tab-pane">
            <div class="row">
                <div class="home5_owl">
                {include file="$tpl_dir./product-list5-owl.tpl" products=$new_products}
                </div>
            </div>
        </div>
    {/if}
    {addJsDef newnbItemsPerLine=4}
    {addJsDef newnbItemsPerLineTablet=3}
    {addJsDef newnbItemsPerLineMobile=2}
	{addJsDef newtotal=($newtotal)|intval}
    
{else}
    <ul id="blocknewproducts" class="blocknewproducts tab-pane">
    	<li class="alert alert-info">{l s='No new products at this time.' mod='blocknewproducts'}</li>
    </ul>
{/if}