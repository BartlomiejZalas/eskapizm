$(document).ready(function(){
    $('.home-category .nav-tabs li').on('click',function(){
        if ($(this).hasClass('active')){
            return;
        }
        var obj_id = $(this).find('a').attr('href');
        $(obj_id+' .owl-item.active ').each(function(i){
            var elem_width = $(this).width();  
            $(this).attr("style",
                  "width: " + elem_width + "px;" 
                + "-webkit-animation-delay:" + i * 300 + "ms;"
                + "-moz-animation-delay:" + i * 300 + "ms;"
                + "-o-animation-delay:" + i * 300 + "ms;"
                + "animation-delay:" + i * 300 + "ms;").addClass('slideInTop animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                     $(this).removeClass('slideInTop animated');
                     var elem_width = $(this).width();  
                    $(this).attr("style","width: " + elem_width + "px;" );
            }); 
        });
    });
})