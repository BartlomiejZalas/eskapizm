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
{if $testimonials|@count > 0}
<!-- MODULE Block otutestimonial -->
<div id="block_testimonial_block" class="block clearfix">
    <p class="title_block">
        {l s='Testimonials' mod='blocktestimonial'}
        <span class="sub_title">{l s='Testimonials by Customers' mod='blocktestimonial'}</span>
    </p>
    <div class="block_content">
        {if isset($current_id_option) && $current_id_option ==5}
        <div id="block_testimonial_home5">	
        {else}
        <div id="block_testimonial_block_slide">    
        {/if}
    		{foreach from=$testimonials item=info}
    			<div class="slide_item"> 
                    <div class="align-image">
                        <img class="block_testtimonial_avat" src="{$module_dir}img/{$info.file_name}" alt="{$info.text|escape:html:'UTF-8'}" width="170" height="157" />
                    </div> 
                    <div class="block_testimonial_info">
                        <p class="block_testimonial_name">{$info.name|escape:html:'UTF-8'},&nbsp;<span class="block_testimonial_company">{$info.company|escape:html:'UTF-8'}</span></p>
                        <p class="block_testimonial_content">{$info.text|escape:html:'UTF-8'}</p>
                    </div>
                </div>
    		{/foreach} 
    	</div>
    </div>
</div>
<!-- /MODULE Block otutestimonial -->
{/if}