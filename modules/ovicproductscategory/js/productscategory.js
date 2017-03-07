/*
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
*/

function pc_serialScrollFixLock(event, targeted, scrolled, items, position)
{
	var leftArrow = position == 0 ? true : false;
	var rightArrow = position + 4 >= $('#productscategory_list li:visible').length ? true : false;
	
	$('a#productscategory_scroll_left').css('cursor', leftArrow ? 'default' : 'pointer').fadeTo(0, leftArrow ? 0 : 1);		
	$('a#productscategory_scroll_right').css('cursor', rightArrow ? 'default' : 'pointer').fadeTo(0, rightArrow ? 0 : 1).css('display', rightArrow ? 'none' : 'block');

	return true;
}
function resizeProductitem() {
    var docWidth = $(document).width()+scrollCompensate();
    if ( docWidth <= 480 ) {
        var docItemWidth = $('#productscategory_list').width();
        $('#productscategory_list_ul.product_list.grid li').css('width', docItemWidth);
    }else if ( docWidth <= 767 ) {
        var docItemWidth = Math.floor(($('#productscategory_list').width()-30)/2);
        $('#productscategory_list_ul.product_list.grid li').css('width', docItemWidth);
    }else if ( docWidth <= 991 ) {
        var docItemWidth = Math.floor(($('#productscategory_list').width()-60)/3);
        $('#productscategory_list_ul.product_list.grid li').css('width', docItemWidth);
    }else if ( docWidth >= 992 ) {
        $('#productscategory_list_ul.product_list.grid li').removeAttr('style');
    }
}
$(document).ready(function()
{
	$('#productscategory_list').serialScroll({
		items: 'li',
		prev: 'a#productscategory_scroll_left',
		next: 'a#productscategory_scroll_right',
		axis: 'x',
		offset: 0,
		stop: true,
		onBefore: pc_serialScrollFixLock,
		duration: 400,
		step: 1,
		lazy: true,
		lock: false,
		force: false,
		cycle: true });
	$('#productscategory_list').trigger( 'goto', 0);
    $(window).load(resizeProductitem);
    $(window).resize(function(){
        resizeProductitem();
        $('#productscategory_list').trigger( 'goto', 0); }   
    );
    $(window).on( "orientationchange", function() { 
        resizeProductitem();
     });
});
// Used to compensante Chrome/Safari bug (they don't care about scroll bar for width)
function scrollCompensate()
{
    var inner = document.createElement('p');
    inner.style.width = "100%";
    inner.style.height = "200px";

    var outer = document.createElement('div');
    outer.style.position = "absolute";
    outer.style.top = "0px";
    outer.style.left = "0px";
    outer.style.visibility = "hidden";
    outer.style.width = "200px";
    outer.style.height = "150px";
    outer.style.overflow = "hidden";
    outer.appendChild(inner);

    document.body.appendChild(outer);
    var w1 = inner.offsetWidth;
    outer.style.overflow = 'scroll';
    var w2 = inner.offsetWidth;
    if (w1 == w2) w2 = outer.clientWidth;

    document.body.removeChild(outer);

    return (w1 - w2);
}