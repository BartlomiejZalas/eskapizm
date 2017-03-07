$(document).ready(function() {
    $('#toppage').val(1);
    $('#loadmore-bestsellers').click(function(){
        if (typeof toptotal == "undefined") {
            var toptotal = $('#toptotal').val();
        }
        if ($('#blockbestsellers ul.product_list > li').length == toptotal){
            if (!$(this).parent().hasClass('empty'))
                $(this).parent().addClass('empty').append('<p class="empty">There is no more products to load</p>');
            return;
        }
        var toppage = $('#toppage').val();
        $('#blockbestsellers').addClass('loading');
        $('#blockbestsellers ul.product_list').addClass('fadeOut animated').addClass('invisible').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
          $(this).removeClass('fadeOut animated');
        });
        $.ajax({
        	type: 'POST',
        	url: baseDir + 'modules/ovicblockbestsellers/ovicblockbestsellers-ajax.php',
        	data: 'toppage='+toppage,
        	dataType: 'json',
        	cache: false,
        	success: function(result){
        	   setTimeout(function(){
        	       $('#blockbestsellers ul.product_list').append(result.productList);
                   $('#blockbestsellers ul.product_list > li').removeClass('last-in-line first-in-line last-line last-item-of-tablet-line first-item-of-tablet-line last-item-of-mobile-line first-item-of-mobile-line last-mobile-line');
                   var cl = $('#blockbestsellers ul.product_list > li').attr('class');
                   $('#blockbestsellers ul.product_list > li').removeAttr('class');
                   var c = $('#blockbestsellers ul.product_list > li').length;
                   $('#blockbestsellers ul.product_list > li').each(function(){
                        $(this).addClass(cl);
                   });
                   $('#blockbestsellers').removeClass('loading');
                   $('#blockbestsellers ul.product_list').removeClass('invisible').addClass('fadeIn animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                      $(this).removeClass('fadeIn animated');
                    });
        	   },500);
               toppage++;
               $('#toppage').val(toppage);
        	}
        });
    });
});