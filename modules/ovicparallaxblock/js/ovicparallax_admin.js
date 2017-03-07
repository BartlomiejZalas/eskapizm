$(document).ready(function(){
    $('.select_position').on('change',function(){
        var id_position = $(this).val();
        $("#form_content").closest('.panel').addClass('loading');
        $.ajax({
        	type: 'POST',
        	url: $('#ajaxurl').val(),
        	data: 'action=changeHookPosition&id_position='+id_position,
        	dataType : "html",
        	cache: false, 
        	success: function(result){
        	   $("#form_content").html(result);
               $("#form_content").closest('.panel').removeClass('loading');
        	}
        });   
    });
    $(document).on('change','.sw_type',function(){
        $('.sw_content').slideUp('slow');
        $('#'+$(this).val()+'_container').slideDown('slow');
    })
    //$('.sw_type').on('change',function(){
    //    alert('aaa');
    //    $('.sw_content').slideUp('slow');
    //    $('#'+$(this).val()+'_container').slideDown('slow');
    //});
    $(document).on('change','#module_select',function(){
        $("#hook_select option").remove();
        $.ajax({
    		type: 'POST',
    		url:  $("#ajaxurl").val(),
            dataType : "html",
    		data: 'action=getModuleHook&module_name='+$(this).val(),
    		success:function(html){
  		        if (html){
  		            //alert(html);
                    $("#hook_select").append(html);
  		        }
    		},
    		error: function(XMLHttpRequest, textStatus, errorThrown) {
    			alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
    		}
    	});
    });
});