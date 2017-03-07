$(document).ready(function() {
    $('#newspage').val(1);
    $('#loadmore-newproducts').click(function(){
        if (typeof newtotal == "undefined") {
            var newtotal = $('#newtotal').val();
        }
        if ($('#blocknewproducts ul.product_list > li').length == newtotal){
            if (!$(this).parent().hasClass('empty'))
                $(this).parent().addClass('empty').append('<p class="empty">There is no more products to load</p>');
            return;
        }
        var newpage = $('#newspage').val();
        $('#blocknewproducts').addClass('loading');
        $('#blocknewproducts ul.product_list').addClass('fadeOut animated').addClass('invisible').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
          $(this).removeClass('fadeOut animated');
        });
        $.ajax({
        	type: 'POST',
        	url: baseDir + 'modules/ovicblocknewproducts/ovicblocknewproducts-ajax.php',
        	data: 'newpage='+newpage,
        	dataType: 'json',
        	cache: false,
        	success: function(result){
        	   setTimeout(function(){
        	       $('#blocknewproducts ul.product_list').append(result.productList);
                   $('#blocknewproducts ul.product_list > li').removeClass('last-in-line first-in-line last-line last-item-of-tablet-line first-item-of-tablet-line last-item-of-mobile-line first-item-of-mobile-line last-mobile-line');
                   var cl = $('#blocknewproducts ul.product_list > li').attr('class');
                   $('#blocknewproducts ul.product_list > li').removeAttr('class');
                   var c = $('#blocknewproducts ul.product_list > li').length;
                   $('#blocknewproducts ul.product_list > li').each(function(){
                        $(this).addClass(cl);
                   });
                   $('#blocknewproducts').removeClass('loading');
                   $('#blocknewproducts ul.product_list').removeClass('invisible').addClass('fadeIn animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                      $(this).removeClass('fadeIn animated');
                    });
        	   },500);
               newpage++;
               $('#newspage').val(newpage);
        	}
        });
    });
});