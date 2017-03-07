function closeDialog(){    
    if ($('#persistent').is(':checked')){
        var data={'action':'cancelRegisNewsletter'};        
        data.persistent = '1';
            $.ajax({
    		type: "POST",
    		cache: false,
    		url: ovicNewsletterUrl + '/front-end-ajax.php',
    		dataType : "json",
    		data: data,
            complete: function(){},
    		success: function (response) {
    
    		}
    	});
    }    
	$("#overlay").hide();
    $(".ovicnewsletter").hide();    
}
function check_email(email){
	emailRegExp = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.([a-z]){2,4})$/;	
	if(emailRegExp.test(email)){
		return true;
	}else{
		return false;
	}
}
function regisNewsletter(){
    var data={'action':'regisNewsletter'};
    var email = $("#input-email").val();
    if(check_email(email) == true){
        data.email = email;
        $("#regisNewsletterMessage").html("");
    }else{
        $("#regisNewsletterMessage").html(enterEmail);
        //alert("Enter your email please!");
        return false;
    }
    if ($('#persistent').is(':checked')){
        data.persistent = '1';
    }else{
        data.persistent = '0';
    }
    $.ajax({
		type: "POST",
		cache: false,
		url: ovicNewsletterUrl + '/front-end-ajax.php',
		dataType : "json",
		data: data,
        complete: function(){
            /*$("#regisNewsletterMessage").html(regisNewsletterMessage);
            setTimeout(function(){ $("#regisNewsletterMessage").html("").hide(); closeDialog();}, 3000);*/
        },
		success: function (response) {
			if (response.status){
				$("#regisNewsletterMessage").addClass('success').html(response.msg);
            setTimeout(function(){ $("#regisNewsletterMessage").html("").hide(); closeDialog();}, 3000);	
			}else{
				$("#regisNewsletterMessage").addClass('error').html(response.msg);
            setTimeout(function(){ $("#regisNewsletterMessage").html("").hide(); closeDialog();}, 3000);
			}
			
		}
	});
}