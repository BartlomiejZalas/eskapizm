function goToElement(eId, offset){
	$("html, body").animate({ scrollTop: $('#'+eId).offset().top-offset});
}
$(document).ready(function(){
    
	if($(".tbl-banners").length >0) tableBannerOrder();
    $(".list-group a").click(function(e){
        $(".list-group").find('a').removeClass('active');
        $(this).addClass('active');
    	e.preventDefault();
    	$(this).tab('show');
    });    
    
    moduleInfoNew 			=	$("#module-info").html();
    moduleBannerNew 		=	$("#form-banners").html();
    moduleDescriptionNew 	=	$("#module-description").html();
        
    groupInfoNew 			=	$("#group-info").html();
    groupDescriptionNew 	=	$("#group-description").html();
    groupBannerNew 			=	$("#form-group-banners").html();
        
    moduleListSetup();  
    moduleIconUploader();
    moduleIconActiveUploader();  
    groupIconUploader();
    groupIconActiveUploader();
    tinySetup();
});
function setStyle(){
	if($(".mColorPicker").length >0){
		$(".mColorPicker").each(function(index) {
		  var value = $(this).val();
		  	if(value != ""){
		  		$(this).css({'background':value});
		  		var rgb = $(this).css('backgroundColor');		  		
		  		var colors = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		  		var o = Math.round(((parseInt(rgb[1]) * 299) + (parseInt(rgb[2]) * 587) + (parseInt(rgb[3]) * 114)) /1000);
		  		if(o > 125) {
			        $(this).css('color','black');
			    }else{ 
			    	$(this).css('color','white');
			        //$('#bg').css('color', 'white');
			    }
			    /*
				var brightness = 1;				
				var r = colors[1];
				var g = colors[2];
				var b = colors[3];				
				var ir = Math.floor((255-r)*brightness);
				var ig = Math.floor((255-g)*brightness);
				var ib = Math.floor((255-b)*brightness);
				$(this).css('color','rgb('+ir+','+ig+','+ib+')');
				//$('#test').css('color', 'rgb('+ir+','+ig+','+ib+')');
				*/
		  	} 
		  	else $(this).css({'background':'#ffffff'});
		});
	}
	
	
}
function showModal(elModal){
	$("#"+elModal).modal('show');
}
function moduleChangeLanguage(langId){
	var oldLang = $("#moduleLangActive").val(); 
	$("#moduleLangActive").val(langId);	
	$(".module-lang").each(function() {
		$(this).val(langId);        
    });
    $(".module-lang-"+oldLang).hide();
    $(".module-lang-"+langId).show();
    $(".tbl-banners-lang-"+oldLang).hide();
    $(".tbl-banners-lang-"+langId).show();
}
function groupChangeLanguage(langId){
	var oldLang = $("#groupLangActive").val(); 
	$("#groupLangActive").val(langId);	
	$(".group-lang").each(function() {
		$(this).val(langId);        
    });
    $(".group-lang-"+oldLang).hide();
    $(".group-lang-"+langId).show();    
}
function tableBannerOrder(){
	$(".tbl-banners").tableDnD({
		onDragStart: function(table, row) {},
		dragHandle: 'dragHandle',
		onDragClass: 'myDragClass',
		onDrop: function(table, row) {} 
	});
}
function showModuleType(moduleType){	
	if(moduleType == 'auto'){
		$(".module-type-manual").hide();
		$(".module-type-auto").show();		
	}else{
		$(".module-type-auto").hide();
		$(".module-type-manual").show();
	}
}
jQuery(function($){
	if($(".display-sortable").length >0 ) $(".display-sortable" ).sortable({});
	if($(".result-list-products").length >0 ) $(".result-list-products" ).sortable({});
	//if($("#module-list-products").length >0 ) $("#module-list-products" ).sortable({});
    $('#modalModule').on('hidden.bs.modal', function (e) {
		tinyRemove();
        $("#module-info").html(moduleInfoNew);
        $("#module-description").html(moduleDescriptionNew);
		$("#form-banners").html(moduleBannerNew);    
        $("#list-group a").click(function(e){
	        $("#list-group").find('a').removeClass('active');
	        $(this).addClass('active');
	    	e.preventDefault();
	    	$(this).tab('show');
	    });
	    moduleIconUploader();
	    moduleIconActiveUploader();
	    setStyle();
        tinySetup();  
	    if($(".tbl-banners").length >0) tableBannerOrder();
        $("p.ajax-loader").remove();
    });
    
    $('#groupModal').on('hidden.bs.modal', function (e) {  
    	tinyRemove();      
        $("#group-info").html(groupInfoNew);
        $("#group-description").html(groupDescriptionNew);
		$("#form-group-banners").html(groupBannerNew);    
        $("#list-group-group a").click(function(e){
	        $("#list-group").find('a').removeClass('active');
	        $(this).addClass('active');
	    	e.preventDefault();
	    	$(this).tab('show');
	    });
	    groupIconUploader();
	    groupIconActiveUploader();
	    tinySetup();
	    if($(".tbl-group-banners").length >0) tableBannerOrder();
        $("p.ajax-loader").remove();
    });
    $('#productsModal').on('hidden.bs.modal', function (e) {
    	if($(".result-list-products").length >0 ){
    		$(".result-list-products").each(function (){
    			if(!$(this).hasClass('ui-sortable'))
    			$(this).sortable({});		
    		});
    	}
    });	
    new AjaxUpload($('#module-banner-uploader'), {
		action: baseModuleUrl+"/uploader.php",
		name: 'uploader',
		data:{'maxFileSize':'2','uploadType':'image', 'secure_key':secure_key},
		responseType: 'json',
		onChange: function(file, ext){},
		onSubmit: function(file, ext){					
			 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
			 	showSuccessMessage('You just upload files (jpg, png,jpeg,gif)');
				return false;
			}
		},
		onComplete: function(file, response){			
			if(response.status == '1'){
				var langId = $("#moduleLangActive").val();
				var html    =   '<tr>';
                html        +=  '<td rowspan="2"><div style="width: 100px"><img class="img-responsive" src="'+response.liveImage+'temps/'+response.fileName+'" /></div></td>';
				html        +=  '<td><input type="text" name="bannerNames'+langId+'[]" value="'+response.fileName+'" class="form-control" /></td>';
				html        +=  '<td><input type="text" name="bannerLink'+langId+'[]" value="" class="form-control"  /></td>';
				html        +=  '<td><input type="text" name="bannerAlt'+langId+'[]" value="" class="form-control"  /></td>';
				html        +=  '<td rowspan="2" class="pointer dragHandle center" ><div class="dragGroup"><a href="javascript:void(0)" class="lik-banner-del color-red" title="Delete banner">Del</a></div></td>';
                html        +=  '</tr>';
                html        +=  '<tr>';
                html        +=  '<td colspan="3"><textarea style="width: 100%; height: 80px"  name="bannerDescription'+langId+'[]" placeholder="'+description+'"></textarea></td>';
                html        +=  '</tr>';                
				$('#tbl-banners-lang-'+langId+" >tbody").append(html);	
				tableBannerOrder();
			}else{
				showSuccessMessage(response.msg);
			}
			
		}
	});
	new AjaxUpload($('#group-banner-uploader'), {
		action: baseModuleUrl+"/uploader.php",
		name: 'uploader',
		data:{'maxFileSize':'2','uploadType':'image', 'secure_key':secure_key},
		responseType: 'json',
		onChange: function(file, ext){},
		onSubmit: function(file, ext){					
			 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
			 	showSuccessMessage('You just upload files (jpg, png,jpeg,gif)');
				return false;
			}
		},
		onComplete: function(file, response){			
			if(response.status == '1'){
				var langId = $("#groupLangActive").val();
				var html    =   '<tr>';
                html        +=  '<td rowspan="2"><div style="width: 100px"><img class="img-responsive" src="'+response.liveImage+'temps/'+response.fileName+'" /></div></td>';
				html        +=  '<td><input type="text" name="bannerNames'+langId+'[]" value="'+response.fileName+'" class="form-control" /></td>';
				html        +=  '<td><input type="text" name="bannerLink'+langId+'[]" value="" class="form-control"  /></td>';
				html        +=  '<td><input type="text" name="bannerAlt'+langId+'[]" value="" class="form-control"  /></td>';
				html        +=  '<td rowspan="2" class="pointer dragHandle center" ><div class="dragGroup"><a href="javascript:void(0)" class="lik-banner-del color-red" title="Delete banner">Del</a></div></td>';
                html        +=  '</tr>'
                html        +=  '<tr>';
                html        +=  '<td colspan="3"><textarea style="width: 100%; height: 80px" name="bannerDescription'+langId+'[]"  placeholder="'+description+'"></textarea></td>';
                html        +=  '</tr>';
				$('#tbl-group-banners-lang-'+langId+" >tbody").append(html);	
				tableBannerOrder();
			}else{
				showSuccessMessage(response.msg);
			}
		}
	});
	
    $(document).on('click','.lik-module-edit',function(){        
        var itemId = $(this).attr('item-id');                
        var data={'action':'loadModuleItem', 'itemId':itemId, 'secure_key':secure_key};
        $.ajax({
    		type:'POST',
    		url: currentUrl,
    		data: data,
    		dataType:'json',
    		cache:false,
    		async: true,
    		beforeSend: function(){},
    		complete: function(){},
    		success: function(response){
    			if(response.status == '1'){
		            tinyRemove();
    				$("#module-info").html(response.config);
                    $("#module-description").html(response.description); 
    				$("#form-banners").html(response.banners);    				
    				showModal('modalModule');
    				$(".result-list-products").each(function (){
		    			if(!$(this).hasClass('ui-sortable'))
		    				$(this).sortable({});		
		    		});
    				if($(".tbl-banners").length >0) tableBannerOrder();
    				$(".list-group a").click(function(e){
				        $(".list-group").find('a').removeClass('active');
				        $(this).addClass('active');
				    	e.preventDefault();
				    	$(this).tab('show');
				    });
				    moduleIconUploader();
				    moduleIconActiveUploader();
				    setStyle();
                    tinySetup();
    			}else{
    				showSuccessMessage(data.message);
    			}	
    		}		
    	});
	});
	$(document).on('click','.lik-module',function(){        
        $(".list-module-item").removeClass('selected');
        moduleId = $(this).attr('data-id');        
        $("#mod_"+moduleId).addClass('selected');
        $("#header-module-name").html('['+$(this).html()+']');
        loadModuleContent();
        $("#group_of_module").show();        
	});
	$(document).on('click','.lik-module-delete',function(){
        if(confirm("Are you sure you want to delete item?") == true){
           var itemId = $(this).attr('item-id');        
    		var data={'action':'deleteModule', 'itemId':itemId, 'secure_key':secure_key};
            $.ajax({
        		type:'POST',
        		url: currentUrl,
        		data: data,
        		dataType:'json',
        		cache:false,
        		async: true,
        		beforeSend: function(){},
        		complete: function(){},
        		success: function(response){
        			showSuccessMessage(response.msg);
                    if(response.status == '1'){
                        window.location.reload();
                    }								
        		}		
        	}); 
        }
	});    
    $(document).on('click','.lik-group-edit',function(){    	
		var itemId = $(this).attr('item-id');        
		var data={'action':'loadGroupItem', 'itemId':itemId, 'secure_key':secure_key};
        $.ajax({
    		type:'POST',
    		url: currentUrl,
    		data: data,
    		dataType:'json',
    		cache:false,
    		async: true,
    		beforeSend: function(){},
    		complete: function(){},
    		success: function(response){
    			if(response.status == '1'){
    				tinyRemove();
    				$("#group-info").html(response.config);
    				$("#group-description").html(response.description); 
    				$("#form-group-banners").html(response.banners);
    				$("#group-list-products" ).sortable();    				
    				showModal('groupModal');
    				groupIconUploader();
    				groupIconActiveUploader();
    				tinySetup();
    				if($(".tbl-banners").length >0) tableBannerOrder();
    				$(".list-group a").click(function(e){
				        $(".list-group").find('a').removeClass('active');
				        $(this).addClass('active');
				    	e.preventDefault();
				    	$(this).tab('show');
				    });
    			}else{
    				showSuccessMessage(data.message);
    			}
    			            											
    		}		
    	});		
	});
	$(document).on('click','.lik-group-delete',function(){
        if(confirm("Are you sure you want to delete item?") == true){
           var itemId = $(this).attr('item-id');        
    		var data={'action':'deleteGroup', 'itemId':itemId, 'secure_key':secure_key};
            $.ajax({
        		type:'POST',
        		url: currentUrl,
        		data: data,
        		dataType:'json',
        		cache:false,
        		async: true,
        		beforeSend: function(){},
        		complete: function(){},
        		success: function(response){
        			showSuccessMessage(response.msg);
                    if(response.status == '1'){
                        loadModuleContent();
                    }								
        		}		
        	}); 
        }
	});
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
    $(document).on('click','.lik-banner-del',function(){        
    	$(this).parent().parent().parent().remove();
	});
    $(document).on('click','.module-item',function(){        
        var itemId = $(this).attr('item-id');                
        if(moduleId != '0'){
            $("#mod_"+moduleId).removeClass('tr-selected');            
        }
        moduleId = itemId;
        $("#mod_"+moduleId).addClass('tr-selected');
        $("#span-module-name").html($(this).html());
        goToElement('groupList', 100);
        $("#mainGroupList").show();
        loadGroupByModule();     
	});
	
	
	
	$(document).on('click','.lik-module-status',function(){        
        var itemId = $(this).attr('data-id');
        var value = $(this).attr('data-value');        
        if(value == '1'){
        	$(this).attr('data-value', '0').removeClass('action-enabled').addClass('action-disabled');
        }else{
        	$(this).attr('data-value', '1').removeClass('action-disabled').addClass('action-enabled');
        }
		var data={'action':'changeModuleStatus', 'itemId':itemId, 'value':value, 'secure_key':secure_key};
        $.ajax({
    		type:'POST',
    		url: currentUrl,
    		data: data,
    		dataType:'json',
    		cache:false,
    		async: true,
    		beforeSend: function(){},
    		complete: function(){},
    		success: function(response){
                if(response){             
                    showSuccessMessage(response.msg);
                }
    		}		
    	});    
	}); 
	$(document).on('click','.lik-group-status',function(){        
        var itemId = $(this).attr('data-id');
        var value = $(this).attr('data-value');        
        if(value == '1'){
        	$(this).attr('data-value', '0').removeClass('action-enabled').addClass('action-disabled');
        }else{
        	$(this).attr('data-value', '1').removeClass('action-disabled').addClass('action-enabled');
        }
		var data={'action':'changeGroupStatus', 'itemId':itemId, 'value':value, 'secure_key':secure_key};
        $.ajax({
    		type:'POST',
    		url: currentUrl,
    		data: data,
    		dataType:'json',
    		cache:false,
    		async: true,
    		beforeSend: function(){},
    		complete: function(){},
    		success: function(response){
                if(response){             
                    showSuccessMessage(response.msg);
                }
    		}		
    	});    
	});  

	
    //link-add-manual-product
    $(document).on('click','.link-add-manual-product',function(){        
        var itemId = $(this).attr('item-id');   
        var itemName = $(this).attr('item-name');
        if($("#modalModule").hasClass('in')){
        	$(this).removeClass('link-add-manual-product').addClass('link-add-manual-product-off').html('<i class="icon-check-square-o"></i>');
	        var html = '<li id="module-manual-product-'+itemId+'"><input type="hidden" class="module_product_ids" name="product_ids[]" value="'+itemId+'" /><span>'+$(this).attr('item-name')+'</span><a class="module-manual-product-delete pull-right" data-id="'+itemId+'"><i class="icon-trash "></i></a></li>';
	        $("#module-list-products").append(html);
        }else{
	        $(this).removeClass('link-add-manual-product').addClass('link-add-manual-product-off').html('<i class="icon-check-square-o"></i>');
	        var html = '<li id="group-manual-product-'+itemId+'"><input type="hidden" class="product_ids" name="product_ids[]" value="'+itemId+'" /><span>'+$(this).attr('item-name')+'</span><a class="group-manual-product-delete pull-right" data-id="'+itemId+'"><i class="icon-trash "></i></a></li>';
	        $("#group-list-products").append(html);	
        }
        	
	});
	
	$(document).on('click','.group-manual-product-delete',function(){        
        var itemId = $(this).attr('data-id'); 
        $("#group-manual-product-"+itemId).remove();
        if($("#manual-product-"+itemId).length >0){
        	$("#manual-product-"+itemId).removeClass('link-add-manual-product-off').addClass('link-add-manual-product').html('<i class="icon-plus"></i>');
        }
	});
  	$(document).on('click','.module-manual-product-delete',function(){        
        var itemId = $(this).attr('data-id'); 
        $("#module-manual-product-"+itemId).remove();
        if($("#manual-product-"+itemId).length >0){
        	$("#manual-product-"+itemId).removeClass('link-add-manual-product-off').addClass('link-add-manual-product').html('<i class="icon-plus"></i>');
        }
	});
    
    //link-delete-category
    
    
    
});

function moduleIconUploader(){
	new AjaxUpload($('#btn-module-icon'), {
		action: baseModuleUrl+"/uploader.php",
		name: 'uploader',
		data:{'maxFileSize':'1','uploadType':'image', 'secure_key':secure_key},
		responseType: 'json',
		onChange: function(file, ext){},
		onSubmit: function(file, ext){					
			 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
			 	showSuccessMessage('You just upload files (jpg, png,jpeg,gif)');
				return false;
			}
		},
		onComplete: function(file, response){			
			if(response.status == '1'){
				$("#module-icon").val(response.fileName);
			}else{
				showSuccessMessage(response.msg);
			}
		}
	});
}
function moduleIconActiveUploader(){
	new AjaxUpload($('#btn-module-icon-active'), {
		action: baseModuleUrl+"/uploader.php",
		name: 'uploader',
		data:{'maxFileSize':'2','uploadType':'image', 'secure_key':secure_key},
		responseType: 'json',
		onChange: function(file, ext){},
		onSubmit: function(file, ext){					
			 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
			 	showSuccessMessage('You just upload files (jpg, png,jpeg,gif)');
				return false;
			}
		},
		onComplete: function(file, response){			
			if(response.status == '1'){
				$("#module-icon-active").val(response.fileName);
			}else{
				showSuccessMessage(response.msg);
			}
		}
	});
}

function groupIconUploader(){
	new AjaxUpload($('#group-icon'), {
		action: baseModuleUrl+"/uploader.php",
		name: 'uploader',
		data:{'maxFileSize':'1','uploadType':'image', 'secure_key':secure_key},
		responseType: 'json',
		onChange: function(file, ext){},
		onSubmit: function(file, ext){					
			 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
			 	showSuccessMessage('You just upload files (jpg, png,jpeg,gif)');
				return false;
			}
		},
		onComplete: function(file, response){			
			if(response.status == '1'){
				$("#icon").val(response.fileName);
			}else{
				showSuccessMessage(response.msg);
			}
		}
	});
}

function groupIconActiveUploader(){
	new AjaxUpload($('#group-iconActive'), {
		action: baseModuleUrl+"/uploader.php",
		name: 'uploader',
		data:{'maxFileSize':'1','uploadType':'image', 'secure_key':secure_key},
		responseType: 'json',
		onChange: function(file, ext){},
		onSubmit: function(file, ext){					
			 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
			 	showSuccessMessage('You just upload files (jpg, png,jpeg,gif)');
				return false;
			}
		},
		onComplete: function(file, response){			
			if(response.status == '1'){
				$("#icon_active").val(response.fileName);
			}else{
				showSuccessMessage(response.msg);
			}
		}
	});
}
function loadModuleContent(){
	if(parseInt(moduleId) >0){
		var data = {'action':'loadModuleContent', 'moduleId':moduleId, 'secure_key':secure_key}; 
        $.ajax({
    		type:'POST',
    		url: currentUrl,
    		data: data,
    		dataType:'json',
    		cache:false,
    		async: true,
    		beforeSend: function(){
    			//$("#row_of_menu").hide();
    		},
    		complete: function(){},
    		success: function(response){
				$("#groupList >tbody").html(response.content);				
				if(response.status == "1") groupListSetup();
				else showErrorMessage(response.msg);
    		}
    	});
	}
}
function groupChangeType(value){
	if(value == 'auto'){
		$(".group-type-auto").show();
		$(".group-type-manual").hide();
	}else{
		$(".group-type-manual").show();
		$(".group-type-auto").hide();
	}
}
function moduleChangeType(value){
	if(value == 'auto'){
		$(".module-type-auto").show();
		$(".module-type-manual").hide();
	}else{
		$(".module-type-manual").show();
		$(".module-type-auto").hide();
	}
}

function moduleListSetup(){
    $("#modList").tableDnD({
		onDragStart: function(table, row) {
			originalOrder = $.tableDnD.serialize();
		},
		dragHandle: 'dragHandle',
		onDragClass: 'myDragClass',
		onDrop: function(table, row) {
            if (originalOrder != $.tableDnD.serialize()) {
                var rows = table.tBodies[0].rows;
                var ids = [];
                for (var i=0; i<rows.length; i++) {
                    var tr = rows[i].id;                    
                    ids[i] = tr.replace("mod_", ""); 
                }
    			var data={'action':'updateModuleOrdering', 'ids':ids, 'secure_key':secure_key};
                $.ajax({
            		type:'POST',
            		url: currentUrl,
            		data: data,
            		dataType:'json',
            		cache:false,
            		async: true,
            		beforeSend: function(){},
            		complete: function(){},
            		success: function(response){
                        showSuccessMessage(response);
                        window.location.reload();											
            		}		
            	});
            }              		         
		}        
	});
}
function groupListSetup(){
    $("#groupList").tableDnD({
		onDragStart: function(table, row) {
			originalOrder = $.tableDnD.serialize();
		},
		dragHandle: 'dragHandle',
		onDragClass: 'myDragClass',
		onDrop: function(table, row) {
            if (originalOrder != $.tableDnD.serialize()) {
                var rows = table.tBodies[0].rows;
                var ids = [];
                for (var i=0; i<rows.length; i++) {
                    var tr = rows[i].id;                    
                    ids[i] = tr.replace("gro_", ""); 
                }
    			var data={'action':'updateGroupOrdering', 'ids':ids, 'secure_key':secure_key};
                $.ajax({
            		type:'POST',
            		url: currentUrl,
            		data: data,
            		dataType:'json',
            		cache:false,
            		async: true,
            		beforeSend: function(){},
            		complete: function(){},
            		success: function(response){
                        showSuccessMessage(response);
                        loadModuleContent();											
            		}		
            	});
            }              		         
		}        
	});
}
function saveModule(){
    tinymce.triggerSave();
    $("#modalModule .modal-footer").append('<p class="ajax-loader"><i class="fa fa-spinner fa-spin"></i></p>');
    var data = $('form#frmModule').serializeObject();    
    $.ajax({
		type:'POST',
		url: currentUrl,
		data: data,
		dataType:'json',
		cache:false,
		async: true,
		beforeSend: function(){
		},
		complete: function(){ 					
		},
		success: function(response){
		  $("p.ajax-loader").remove();
            showSuccessMessage(response.msg);
            if(response.status == '1') window.location.reload();
		}		
	});
}
function saveGroup(){
    if(moduleId != '0'){
    	tinymce.triggerSave();
        $("#groupModal .modal-footer").append('<p class="ajax-loader"><i class="fa fa-spinner fa-spin"></i></p>');
    	var data = $('form#frmGroup').serializeObject();
    	data.moduleId = moduleId;    	
        $.ajax({
    		type:'POST',
    		url: currentUrl,
    		data: data,
    		dataType:'json',
    		cache:false,
    		async: true,
    		beforeSend: function(){},
    		complete: function(){},
    		success: function(response){
    		  $("p.ajax-loader").remove();
                showSuccessMessage(response.msg);
                if(response.status == '1'){
                	loadModuleContent();
                	$('#groupModal').modal('hide');
                }
    		}		
    	});
    }else{
        showSuccessMessage("You need to choose one module, please!");
    }    
}
function openProductsModal(id){
	groupId = id;    
    showModal('productsModal');
    loadListProducts('1');
    
}
function tinyRemove(elParent){	

	var i, t = tinyMCE.editors;

	for (i in t){

	    if (t.hasOwnProperty(i)){

	        t[i].remove();

	    }

	}	 

}
function loadListProducts(page){
    productPage = page;
    var keyword = $("#keyword").val();
    var productIds = new Array();
    if($("#modalModule").hasClass('in')){
    	if($(".module_product_ids").length >0){
	    	$(".module_product_ids").each(function (index){
	    		productIds[index] = $(this).val();
	    	});
	    }
	    var categoryId = $("#module-category :selected").val();
    }else{
	    if($(".product_ids").length >0){
	    	$(".product_ids").each(function (index){
	    		productIds[index] = $(this).val();
	    	});
	    }
	    var categoryId = $("#group_category :selected").val();	
	    
    }
    var data={'action':'loadListProducts', 'categoryId':categoryId, 'moduleId':moduleId, 'groupId':groupId, 'productIds':productIds, 'page':page, 'keyword':keyword, 'secure_key':secure_key};
    $.ajax({
		type:'POST',
		url: currentUrl,
		data: data,
		dataType:'json',
		cache:false,
		async: true,
		beforeSend: function(){
		},
		complete: function(){ 					
		},
		success: function(response){
            $("#allProductList-pagination").html(response.pagination);
            $("#allProductList >tbody").html(response.list);										
		}		
	});
}