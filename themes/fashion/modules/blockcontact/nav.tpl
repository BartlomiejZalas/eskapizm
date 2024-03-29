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
<div id="top_contact" class="visible-lg">
{*}<div id="contact-link">
	<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" title="{l s='Contact Us' mod='blockcontact'}">{l s='Contact Us' mod='blockcontact'}</a>
</div>{*}
{if (isset($current_id_option) && ($current_id_option != 5)) || !isset($current_id_option)}
<span class="ship_info">{l s='We Ship Internationally' mod='blockcontact'}</span>
<span class="return_info">{l s='Extended 60-Day Returns' mod='blockcontact'}</span>
<span class="discount_info">{l s='Get $20 off $100+' mod='blockcontact'}</span>
{/if}
{if $telnumber}
	<span class="shop-phone">
		{if isset($current_id_option)&& ($current_id_option != 5)}<i class="icon-phone"></i>{/if}{l s='Call us:' mod='blockcontact'} <strong>{$telnumber}</strong>
	</span>
{/if}
</div>