$(document).ready(function() {
    $('#featurepage').val(2);
    $('#loadmore-homefeature').click(function(){
        if (typeof hometotal == "undefined") {
            var hometotal = $('#hometotal').val();
        }
        if ($('#ovichomefeatured ul.product_list > li').length == hometotal){
            if (!$(this).parent().hasClass('empty'))
                $(this).parent().addClass('empty').append('<p class="empty">There is no more products to load</p>');
            return;
        }
        var featurepage = $('#featurepage').val();
        $('#ovichomefeatured').addClass('loading');
        $('#ovichomefeatured ul.product_list').addClass('fadeOut animated').addClass('invisible').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
          $(this).removeClass('fadeOut animated');
        });
        $.ajax({
        	type: 'POST',
        	url: baseDir + 'modules/ovichomefeatured/ovichomefeatured-ajax.php',
        	data: 'featurepage='+featurepage,
        	dataType: 'json',
        	cache: false,
        	success: function(result){
        	   setTimeout(function(){
        	       $('#ovichomefeatured ul.product_list').append(result.productList);
                   $('#ovichomefeatured ul.product_list > li').removeClass('last-in-line first-in-line last-line last-item-of-tablet-line first-item-of-tablet-line last-item-of-mobile-line first-item-of-mobile-line last-mobile-line');
                   var cl = $('#ovichomefeatured ul.product_list > li').attr('class');
                   $('#ovichomefeatured ul.product_list > li').removeAttr('class');
                   var c = $('#ovichomefeatured ul.product_list > li').length;
                   $('#ovichomefeatured ul.product_list > li').each(function(){
                        $(this).addClass(cl);
                   });
                   $('#ovichomefeatured').removeClass('loading');
                   $('#ovichomefeatured ul.product_list').removeClass('invisible').addClass('fadeIn animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                      $(this).removeClass('fadeIn animated');
                    });
        	   },500);
               featurepage++;
               $('#featurepage').val(featurepage);
        	}
        });
    });
});