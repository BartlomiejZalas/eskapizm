function serialCollection(event, targeted, scrolled, items, position)
{
	var leftArrow = position == 0 ? true : false;
	var rightArrow = position + 1 >= $('#collection_block .wrap_ul li:visible').length ? true : false;
	
	$('a#collection_block_prev').css('cursor', leftArrow ? 'default' : 'pointer').fadeTo(0, leftArrow ? 0 : 1);			
	$('a#collection_block_next').css('cursor', rightArrow ? 'default' : 'pointer').fadeTo(0, rightArrow ? 0 : 1).css('display', rightArrow ? 'none' : 'block');

	return true;
}
function serialProducts(event, targeted, scrolled, items, position)
{
	var leftArrow = position == 0 ? true : false;
	var rightArrow = position + 4 >= $('#collection_products_wrap .wrap_ul li:visible').length ? true : false;
	
	$('a#collection_products_prev').css('cursor', leftArrow ? 'default' : 'pointer').fadeTo(0, leftArrow ? 0 : 1);		
	$('a#collection_products_next').css('cursor', rightArrow ? 'default' : 'pointer').fadeTo(0, rightArrow ? 0 : 1).css('display', rightArrow ? 'block' : 'block');

	return true;
}

function resizeItem() {
    var docWidth = $(document).width();
    var slideitemWidth = 100;  
    if ( docWidth <= 767 ) {
        slideitemWidth = $('#collection_block').width();
        $('#fashion_collection_block .wrap_ul ul li.slide_item').css('width', slideitemWidth);
    }else if (docWidth >= 768) {
        $('#fashion_collection_block .wrap_ul ul li.slide_item').removeAttr('style');
    }
}

$(document).ready(function()
{     
	$('#collection_block .wrap_ul').serialScroll({
		items: 'li',
		prev: 'a#collection_block_prev',
		next: 'a#collection_block_next',
		axis: 'x',
		offset: 0,
		stop: false,
		onBefore: serialCollection,
		duration: 400,
		step: 1,
		lazy: true,
		lock: false,
		force: false,
		cycle: true });
	$('#collection_block .wrap_ul').trigger( 'goto', 0);
    
    // Products slide
    $('#collection_products_wrap .wrap_ul').serialScroll({
		items: 'li',
		prev: 'a#collection_products_prev',
		next: 'a#collection_products_next',
		axis: 'x',
		offset: 0,
		stop: false,
		onBefore: serialProducts,
		duration: 400,
		step: 1,
		lazy: true,
		lock: false,
		force: false,
		cycle: true });
	$('#collection_products_wrap .wrap_ul').trigger( 'goto', 0);
    $(window).load(resizeItem);
    $(window).resize( function(){
        resizeItem();
        $('#collection_products_wrap .wrap_ul').trigger( 'goto', 0);
        $('#collection_block .wrap_ul').trigger( 'goto', 0);
        }
    );
});