function pc_serialScrollFixLock(event, targeted, scrolled, items, position)
{
	var leftArrow = position == 0 ? true : false;
	var rightArrow = position + 2 >= $('#blogs_wrap_ul li:visible').length ? true : false;

	$('a#blog_prev').css('cursor', leftArrow ? 'default' : 'pointer').fadeTo(0, leftArrow ? 0 : 1);
	$('a#blog_next').css('cursor', rightArrow ? 'default' : 'pointer').fadeTo(0, rightArrow ? 0 : 1).css('display', rightArrow ? 'none' : 'block');

	return true;
}

$(document).ready(function()
{
    //$(window).load(function(){
//        var pelementWidth = $('#blogs_wrap_ul').width();
//        var itemWidth = 370;  //width default
//        var marginItem = 30;  //margin-right default
//        var nItem = 2;  //number of items
//        var perWidth = 48.3  //percent
//
//        itemWidth = Math.floor(pelementWidth*perWidth/100);
//        marginItem =  Math.floor(pelementWidth - (nItem*itemWidth));
//
//        $('#blogs_wrap_ul li.slide_item').css('margin-right',marginItem+'px');
//        $('#blogs_wrap_ul li.slide_item').css('width',itemWidth+'px');
//    });
//
//    $(window).resize(function(){
//        var pelementWidth = $('#blogs_wrap_ul').width();
//        var itemWidth = 370;  //width default
//        var marginItem = 30;  //margin-right default
//        var nItem = 2;  //number of items
//        var perWidth = 48.3  //percent
//
//        itemWidth = Math.floor(pelementWidth*perWidth/100);
//        marginItem =  Math.floor(pelementWidth - (nItem*itemWidth));
//
//        $('#blogs_wrap_ul li.slide_item').css('margin-right',marginItem+'px');
//        $('#blogs_wrap_ul li.slide_item').css('width',itemWidth+'px');
//    });


	$('#blogs_wrap_ul').serialScroll({
		items: 'li',
		prev: 'a#blog_prev',
		next: 'a#blog_next',
		axis: 'x',
		offset: 0,
		stop: true,
		onBefore: pc_serialScrollFixLock,
		duration: 500,
		step: 1,
		lazy: true,
		lock: false,
		force: false,
		cycle: false });
	$('#blogs_wrap_ul').trigger( 'goto', 0);
    $(window).resize(function(){
        $('#blogs_wrap_ul').trigger( 'goto', 0);
    });
});