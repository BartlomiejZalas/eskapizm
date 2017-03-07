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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $page_name =='index'}
    <!-- Module HomeSlider -->
    {assign var="current_option" value=Configuration::get('OVIC_CURRENT_OPTION')}
    {if isset($homeslider_slides)}
        <div id="homepage-slider" class="col-sm-8">
            <ul id="homeslider">
                {foreach from=$homeslider_slides item=slide}
                    {if $slide.active}
                        <li class="homeslider-container">
                            <a href="{$slide.url|escape:'html':'UTF-8'}" title="{$slide.legend|escape:'html':'UTF-8'}">
                                <img src="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`homeslider/images/`$slide.image|escape:'htmlall':'UTF-8'`")}"{if isset($slide.size) && $slide.size} {$slide.size}{else} width="100%" height="100%"{/if} alt="{$slide.legend|escape:'htmlall':'UTF-8'}" />
                            </a>
                            {if isset($slide.description) && trim($slide.description) != ''}
                                <div class="homeslider-description">{$slide.description}</div>
                            {/if}
                        </li>
                    {/if}
                {/foreach}
            </ul>
        </div>
    {/if}
    <!-- /Module HomeSlider -->
    {if isset($current_option)&& ($current_option ==1 || $current_option==3)}
    <script type="text/javascript">
        $(document).ready(function(){
	if (typeof(homeslider_speed) == 'undefined')
		homeslider_speed = 500;
	if (typeof(homeslider_pause) == 'undefined')
		homeslider_pause = 3000;
	if (typeof(homeslider_loop) == 'undefined')
		homeslider_loop = false;
    if (typeof(homeslider_width) == 'undefined')
        homeslider_width = 779;

    var effect = new Array();
    effect[0] = 'fadeInRight animated';
    effect[1] = 'bounceIn animated';
    effect[2] = 'slideInDown animated';
    effect[3] = 'fadeInUp animated';
    effect[4] = 'bounceIn animated';
    effect[5] = 'slideInDown animated';
    effect[6] = 'lightSpeedIn animated';
    effect[7] = 'fadeInRight animated';

	if (!!$.prototype.bxSlider)
		$('#homeslider').bxSlider({
            mode: 'fade',
			useCSS: false,
			maxSlides: 1,
			//slideWidth: homeslider_width,
			infiniteLoop: homeslider_loop,
			hideControlOnEnd: true,
			pager: true,
			autoHover: true,
			auto: homeslider_loop,
			speed: homeslider_speed,
			pause: homeslider_pause,
			controls: true,
            onSliderLoad: function(){
                $('#homepage-slider .bx-wrapper').addClass('loading');
                $('#homeslider').addClass('hidden');
                var homesliderHeight = parseInt($('#homepage-slider .bx-wrapper').height() - 115);
                var pager_item_height = parseInt($('.bx-pager-item').height()+ parseInt($('.bx-pager-item').css('marginBottom')));
                var pager_height = parseInt(($('.bx-pager-item').length -1)*pager_item_height+$('.bx-pager-item').height());
                var bt_next_bottom  = parseInt((homesliderHeight/2)-(pager_height/2)-40);
                $('#homepage-slider .bx-wrapper .bx-next').css('bottom',bt_next_bottom+'px')
                $('#homepage-slider .bx-wrapper .bx-pager').css('bottom',bt_next_bottom+40+'px');
                $('#homepage-slider .bx-wrapper .bx-prev').css('bottom',bt_next_bottom+55+pager_height+'px');
                $('#homepage-slider .bx-controls').addClass('container hidden');
                },
            onSlideBefore: function(currentSlide, totalSlides, currentSlideHtmlObject){
                $('#homeslider').removeClass('hidden');
                $('#homepage-slider .bx-controls').removeClass('hidden');
                $('#homepage-slider .bx-wrapper').removeClass('loading');                
            }
		});

    $('.homeslider-description').click(function () {
        window.location.href = $(this).prev('a').prop('href');
    });
});
$(window).resize(function() {
    var pager_item_height = $('.bx-pager-item').height()+ parseInt($('.bx-pager-item').css('marginBottom'));
    var pager_height = parseInt(($('.bx-pager-item').length -1)*pager_item_height+$('.bx-pager-item').height());
    var bt_next_bottom  = parseInt($('#homepage-slider .bx-wrapper .bx-next').css('bottom'));
    $('#homepage-slider .bx-wrapper .bx-pager').css('bottom',bt_next_bottom+35+'px');
    $('#homepage-slider .bx-wrapper .bx-prev').css('bottom',bt_next_bottom+50+pager_height+'px');
 });
    </script>
    {elseif isset($current_option)&& ($current_option ==2 || $current_option ==4)}
    <script type="text/javascript">
        $(document).ready(function(){
	if (typeof(homeslider_speed) == 'undefined')
		homeslider_speed = 500;
	if (typeof(homeslider_pause) == 'undefined')
		homeslider_pause = 3000;
	if (typeof(homeslider_loop) == 'undefined')
		homeslider_loop = false;
    if (typeof(homeslider_width) == 'undefined')
        homeslider_width = 779;

    var effect = new Array();
    effect[0] = 'fadeInRight animated';
    effect[1] = 'bounceIn animated';
    effect[2] = 'slideInDown animated';
    effect[3] = 'fadeInUp animated';
    effect[4] = 'bounceIn animated';
    effect[5] = 'slideInDown animated';
    effect[6] = 'lightSpeedIn animated';
    effect[7] = 'fadeInRight animated';

	if (!!$.prototype.bxSlider)
		$('#homeslider').bxSlider({
			useCSS: false,
			maxSlides: 1,
			//slideWidth: homeslider_width,
			infiniteLoop: homeslider_loop,
			hideControlOnEnd: true,
			pager: false,
			autoHover: true,
			auto: homeslider_loop,
			speed: homeslider_speed,
			pause: homeslider_pause,
			controls: true,
            onSliderLoad: function(){
                $('#homepage-slider .bx-wrapper').addClass('loading');
                $('#homeslider').addClass('hidden');
                $('#homepage-slider .bx-controls').addClass('container hidden');
                },
            onSlideBefore: function(currentSlide, totalSlides, currentSlideHtmlObject){
                $('#homeslider').removeClass('hidden');
                $('#homepage-slider .bx-controls').removeClass('hidden');
                $('#homepage-slider .bx-wrapper').removeClass('loading');

            }
		});

    $('.homeslider-description').click(function () {
        window.location.href = $(this).prev('a').prop('href');
    });
});
    </script>
    {elseif isset($current_option)&& ($current_option ==5)}
    <script type="text/javascript">
        $(document).ready(function(){
    if (typeof(homeslider_speed) == 'undefined')
        homeslider_speed = 500;
    if (typeof(homeslider_pause) == 'undefined')
        homeslider_pause = 3000;
    if (typeof(homeslider_loop) == 'undefined')
        homeslider_loop = false;
    if (typeof(homeslider_width) == 'undefined')
        homeslider_width = 779;

    var effect = new Array();
    effect[0] = 'fadeInRight animated';
    effect[1] = 'bounceIn animated';
    effect[2] = 'slideInDown animated';
    effect[3] = 'fadeInUp animated';
    effect[4] = 'bounceIn animated';
    effect[5] = 'slideInDown animated';
    effect[6] = 'lightSpeedIn animated';
    effect[7] = 'fadeInRight animated';

    if (!!$.prototype.bxSlider)
        $('#homeslider').bxSlider({
            useCSS: false,
            maxSlides: 1,
            //slideWidth: homeslider_width,
            infiniteLoop: homeslider_loop,
            hideControlOnEnd: true,
            pager: true,
            autoHover: true,
            auto: homeslider_loop,
            speed: homeslider_speed,
            pause: homeslider_pause,
            controls: true,
            onSliderLoad: function(){
                $('#homepage-slider .bx-wrapper').addClass('loading');
                $('#homeslider').addClass('hidden');
                $('#homepage-slider .bx-controls').addClass('container hidden');
                },
            onSlideBefore: function(currentSlide, totalSlides, currentSlideHtmlObject){
                $('#homeslider').removeClass('hidden');
                $('#homepage-slider .bx-controls').removeClass('hidden');
                $('#homepage-slider .bx-wrapper').removeClass('loading');

            }
        });

    $('.homeslider-description').click(function () {
        window.location.href = $(this).prev('a').prop('href');
    });
});
    </script>
    {/if}
{/if}