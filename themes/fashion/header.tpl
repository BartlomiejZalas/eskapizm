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
<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 " lang="{$lang_iso}"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="{$lang_iso}"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="{$lang_iso}"><![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9" lang="{$lang_iso}"><![endif]-->
<html lang="{$lang_iso}">
	<head>
		<meta charset="utf-8" />
		<title>{$meta_title|escape:'html':'UTF-8'}</title>
{if isset($meta_description) AND $meta_description}
		<meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
{/if}
{if isset($meta_keywords) AND $meta_keywords}
		<meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
{/if}
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
		<meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
		<link rel="stylesheet" href="{$css_uri}" type="text/css" media="{$media}" />
	{/foreach}
{/if}
{if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
	{$js_def}
	{foreach from=$js_files item=js_uri}
	<script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
	{/foreach}
{/if}
		{$HOOK_HEADER}
		<link rel="stylesheet" href="http{if Tools::usingSecureMode()}s{/if}://fonts.googleapis.com/css?family=Open+Sans:300,600" type="text/css" media="all" />
		<!--[if IE 8]>
		<script type="text/javascript" src="{$js_dir}html5shiv.js"></script>
		<script type="text/javascript" src="{$js_dir}respond.min.js"></script>
		<![endif]-->
        <link rel="stylesheet" type="text/css" href="{$css_dir}animate.css" />
        <link rel="stylesheet" type="text/css" href="{$css_dir}custom.css" />
        <script type="text/javascript" src="{$js_dir}owl.carousel.min.js"></script>
        {if isset($current_id_option)&& $current_id_option ==5}
            <script type="text/javascript" src="{$js_dir}option5.js"></script>
        {/if}
	</head>
	<body{if isset($page_name)} id="{$page_name|escape:'html':'UTF-8'}"{/if} class="{if isset($current_id_option)}option-{$current_id_option} {/if}{if isset($page_name)}{$page_name|escape:'html':'UTF-8'}{/if}{if isset($body_classes) && $body_classes|@count} {implode value=$body_classes separator=' '}{/if}{if $hide_left_column} hide-left-column{/if}{if $hide_right_column} hide-right-column{/if}{if $content_only} content_only{/if} lang_{$lang_iso}">
	{if !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
			<div id="restricted-country">
				<p>{l s='You cannot place a new order from your country.'} <span class="bold">{$geolocation_country}</span></p>
			</div>
		{/if}
        {if $page_name == 'index'}{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}{/if}
        {if $page_name == 'index' and isset($comparator_max_item)}
        {addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
        {addJsDef comparator_max_item=$comparator_max_item}
        {/if}
        {if $page_name == 'index' and isset($compared_products)}{addJsDef comparedProductsIds=$compared_products}{/if}
        {if isset($current_id_option)&& $current_id_option ==3}
            <div class="page_outter">
            <div class="page_wrapper">
                <div id="page" class="container">
                    <div class="header-container container">
                        <div class="row">
        {else}
            <div id="page">
                <div class="header-container{if isset($current_id_option)&& $current_id_option ==3} container{/if}">
        {/if}
				<header id="header">
					<div>
                        {if isset($current_id_option)&& $current_id_option !=3}
						<div class="container">
                        <div class="row">
                        {/if}
                                <div id="before_logo" class="visible-lg">
                                    {$BEFORE_LOGO}
                                </div>
                                {if isset($current_id_option)&& ($current_id_option ==1 || $current_id_option == 3)}
                                <div class="top-container col-sm-10">
                                    <div class="row top-nav-container">
                                        <div class="top-nav col-sm-12">
                                            <nav>{hook h="displayNav"}</nav>
                                        </div>
                                    </div>
                                    <div class="header_logo visible-mobile">
    									<a class="logo-im mainBgColor" href="{$base_dir}" title="{$shop_name|escape:'html':'UTF-8'}">
    										<img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if $logo_image_width} width="{$logo_image_width}"{/if}{if $logo_image_height} height="{$logo_image_height}"{/if}/>
    									</a>
    								</div>
                                    <div class="row top-menu-container">
                                        <div class="col-sm-12">
                                        {if isset($HOOK_TOP)}{$HOOK_TOP}{/if}
                                        </div>
                                    </div>
                                </div>
                                {elseif isset($current_id_option)&& ($current_id_option ==2 || $current_id_option==4 || $current_id_option==5 )}
                                <div class="top-container {if $current_id_option == 2}col-sm-9{elseif $current_id_option == 4} col-lg-5 col-sm-9{/if}">
                                    <div>
                                        <div class="{if $current_id_option == 2}col-lg-6 {/if}col-xs-12 top-nav-container">
                                            <div class="top-nav">
                                                <nav>{hook h="displayNav"}</nav>
                                            </div>
                                        </div>
                                        <div class="{if $current_id_option == 2}col-lg-6 {/if}col-xs-12 top-menu-container">
                                            <div>
                                            {if isset($HOOK_TOP)}{$HOOK_TOP}{/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {/if}
                                <div id="header_logo" class="header_logo {if isset($current_id_option)&& ($current_id_option ==1 || $current_id_option == 3)}hidden-mobile{/if}">
									<a class="logo-im mainBgColor" href="{$base_dir}" title="{$shop_name|escape:'html':'UTF-8'}">
										<img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if $logo_image_width} width="{$logo_image_width}"{/if}{if $logo_image_height} height="{$logo_image_height}"{/if}/>
									</a>
								</div>
                                {if isset($BEFORE_LOGO) && $BEFORE_LOGO|trim}
                                {/if}
                        {if isset($current_id_option)&& $current_id_option !=3}
                            </div>
						</div>
                        {/if}
					</div>
				</header>
                {if isset($current_id_option)&& $current_id_option ==3}
                    </div>
                {/if}
			</div>
			<div id="topcolumn" class="columns-container">
				<div id="top_column" class="container">
                    {if isset($current_id_option)&& $current_id_option ==3}
                        <div class="row">
                    {/if}
                    {hook h="displayTopColumn"}
                    {if isset($current_id_option)&& $current_id_option ==3}
                        </div>
                    {/if}
                </div>
			</div>

            <div class="columns-container">
				<div id="columns" class="container">
                    {if isset($page_name) && ($page_name == 'index') && isset($HOOK_HOME_TOP_COLUMN) && $HOOK_HOME_TOP_COLUMN|trim}
                        <div id="home_top_column" class="container clearfix">
                            {$HOOK_HOME_TOP_COLUMN}
                        </div>
                    {/if}
					{if $page_name !='index' && $page_name !='pagenotfound'}
						{include file="$tpl_dir./breadcrumb.tpl"}
					{/if}

					<div class="row">
						{if isset($left_column_size) && !empty($left_column_size)}
						<div id="left_column" class="column col-xs-12 col-sm-{$left_column_size|intval}">{$HOOK_LEFT_COLUMN}</div>
						{/if}
						<div id="center_column" class="center_column col-xs-12 col-sm-{12 - $left_column_size - $right_column_size}">
	{/if}