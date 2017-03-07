$(document).ready(function(){
    $('.call_slide_product').click(function(){
        var id_collection = $(this).parent().find('.id_collection').attr('value');
        var new_ul = $('#collection_products_wrap .wrap_ul').find('ul');
        var docWidth  = $(document).width();
        var desiName = $(this).parent().find('input.name_collection').val();
        if (desiName != '')
            $('#collection_products_wrap>h3').html(desiName);
        $('#collection_products_wrap').css('opacity',0.7);
        $('#collection_products_wrap .load-more-img').css('visibility','visible');
        $.ajax({
        	type: 'POST',
        	url: baseDir + 'modules/blockcollection/blockcollection-ajax.php',
        	data: 'id_collection='+id_collection,
        	dataType: 'json',
        	cache: false, 
        	success: function(result){
        	   setTimeout(function(){
        	   new_ul.html(result.productList).fadeIn(2000);
               $('#collection_products_wrap').css('opacity',1);
               $('#collection_products_wrap .load-more-img').css('visibility','hidden');
               },1200);
        	}
        });        
    });
});
