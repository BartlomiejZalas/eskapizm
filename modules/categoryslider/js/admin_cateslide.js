jQuery(function($){
	$(document).on('click','.tree-folder input',function(){        
		var id_cate = $(this).val();
	   location.href = $('#mainUrl').val()+'&id_category='+id_cate;
	});
});