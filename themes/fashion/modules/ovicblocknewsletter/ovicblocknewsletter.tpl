{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block Newsletter module-->
{assign var="current_option" value=Configuration::get('OVIC_CURRENT_OPTION')}
{if isset($current_option)&& ($current_option ==1 || $current_option==3)}
    <div id="fashion_newsletter_hook" class="col-md-4 bright pad-mbn-clr">
        <div id="newsletter_block_left" class="col-md-4 fashion_module">
{elseif isset($current_option)&& $current_option ==5}
    <div id="home5_newsletter" class="div_full_width">
        <div class="newsletter_overlay">
            <div class="container">
                <div class="newsletter_content">
{else}
    <div id="newsletter_block_left" class="col-md-4 fashion_module">
{/if}
    {if isset($current_option)&& ($current_option ==2 || $current_option ==4)}
        <div class="row">
            <div class="col-sm-6">
    {/if}
	<p class="title_block">{l s='Newsletter' mod='ovicblocknewsletter'}</p>
    <p class="sub_title">{l s='Subscribe to our newsletter massa In Curabitur id risus sit quis justo sed ovanti' mod='ovicblocknewsletter'}</p>
	<div class="block_content">
	{if isset($msg) && $msg}
		<p class="{if $nw_error}warning_inline{else}success_inline{/if}">{$msg}</p>
	{/if}
		<form action="{$link->getPageLink('index')|escape:'html'}" method="post">
                <div class="form-group">
			         <input class="inputNew form-control" id="newsletter-input" type="text" name="email" size="18" placeholder="Your Email"  value="" />
                {if isset($current_option)&& ($current_option ==1 || $current_option==3)}
                    <button type="submit" class="btnbgcolorhover button" name="submitNewsletter">{l s='Subscribe' mod='ovicblocknewsletter'}</button>
                {elseif isset($current_option)&& ($current_option ==2 || $current_option ==4)}
                    <button type="submit" class="btnbgcolorhover button" name="submitNewsletter">{l s='Subscribe' mod='ovicblocknewsletter'}</button>
                {elseif isset($current_option)&& ($current_option ==5)}
                    <button type="submit" class="button" name="submitNewsletter">{l s='Sign Up' mod='ovicblocknewsletter'}</button>
                {/if}
                    <input type="hidden" name="action" value="0" />
                </div>
		</form>
	</div>
    {if isset($current_option)&& ($current_option ==2 || $current_option ==4)}
            </div>
            <div class="col-sm-6">
                {hook h='displayBanner'}
            </div>
        </div>
    {/if}
    {if isset($current_option)&& $current_option ==5}
            </div>
        </div>
    </div>
    {/if}
</div>
{if isset($current_option)&&($current_option ==1 || $current_option==3)}
</div>
{/if}
<!-- /Block Newsletter module-->

<script type="text/javascript">
    var placeholder = "{l s='Your Email' mod='ovicblocknewsletter' js=1}";
        $(document).ready(function() {ldelim}
            $('#newsletter-input').on({ldelim}
                focus: function() {ldelim}
                    if ($(this).val() == placeholder) {ldelim}
                        $(this).val('');
                    {rdelim}
                {rdelim},
                blur: function() {ldelim}
                    if ($(this).val() == '') {ldelim}
                        $(this).val(placeholder);
                    {rdelim}
                {rdelim}
            {rdelim});

            {if isset($msg)}
                $('#columns').before('<div class="clearfix"></div><p class="{if $nw_error}warning{else}success{/if}">{l s="Newsletter:" js=1 mod='ovicblocknewsletter'} {$msg}</p>');
            {/if}
        });
</script>
