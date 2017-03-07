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
{if isset($HOME_TOP_CONTENT) && $HOME_TOP_CONTENT|trim}
    <div class="row clearfix">{$HOME_TOP_CONTENT}</div>
{/if}
{if isset($HOOK_HOME_TAB_CONTENT) && $HOOK_HOME_TAB_CONTENT|trim}
    {if isset($HOOK_HOME_TAB) && $HOOK_HOME_TAB|trim}
        <ul id="home-page-tabs" class="nav nav-tabs clearfix">
            {$HOOK_HOME_TAB}
        </ul>
    {/if}
    <div id="home-tabs-content" class="tab-content">{$HOOK_HOME_TAB_CONTENT}</div>
{/if}
{if isset($HOOK_HOME) && $HOOK_HOME|trim}
    <div class="clearfix">{$HOOK_HOME}</div>
{/if}
{if isset($HOME_BOTTOM_CONTENT) && $HOME_BOTTOM_CONTENT|trim}
    <div id="home_bottom_content" class="clearfix">
        {if isset($current_id_option)&& $current_id_option !=5}
        <div class="row">
            {$HOME_BOTTOM_CONTENT}
        </div>
        {else}
            {$HOME_BOTTOM_CONTENT}
        {/if}
    </div>
{/if}