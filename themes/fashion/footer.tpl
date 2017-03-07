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
{if !$content_only}
					</div><!-- #center_column -->
					{if isset($right_column_size) && !empty($right_column_size)}
						<div id="right_column" class="col-xs-12 col-sm-{$right_column_size|intval} column">{$HOOK_RIGHT_COLUMN}</div>
					{/if}
					</div><!-- .row -->
                    {*}{if $page_name == 'index'}
                    <div class="row">
                        <div id="fashion_blog_hook" class="bleft col-md-8 pad-mbn-clr">
                            {hook h='HomeBlog'}{hook h='HOOK_HOMESLIDEBLOG'}
                        </div>
                        <div id="fashion_newsletter_hook" class="col-md-4 bright pad-mbn-clr">
                            {hook h='HOOK_NEWSLETTER'}
                        </div>
                        <div class="clearBoth">{hook h='HomeBlog'}</div>
                    </div>
                    {/if}{*}
                    {if isset($page_name) && ($page_name == 'index') && isset($HOME_BOTTOM_COLUMN) && $HOME_BOTTOM_COLUMN|trim}
                        <div id="home_bottom_column" class="container clearfix">
                            {if isset($current_id_option)&& $current_id_option !=5}
                            <div class="row">
                                {$HOME_BOTTOM_COLUMN}
                            </div>
                            {else}
                                {$HOME_BOTTOM_COLUMN}
                            {/if}
                        </div>
                    {/if}
				</div><!-- #columns -->
			</div><!-- .columns-container -->
            {if isset($BOTTOM_COLUMN) && $BOTTOM_COLUMN|trim}
            	<div class="row clearfix">{$BOTTOM_COLUMN}</div>
            {/if}
			<!-- Footer -->
			<div class="footer-container clearfix clearBoth">
				<footer id="footer"  class="container">
					<div class="row">{$HOOK_FOOTER}</div>
				</footer>
			</div><!-- #footer -->
		</div><!-- #page -->
{/if}
{include file="$tpl_dir./global.tpl"}
{if isset($current_id_option)&& $current_id_option ==3}
    </div>
</div>
{/if}
	</body>
</html>