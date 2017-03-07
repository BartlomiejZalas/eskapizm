
function resizeTopmenu(){
    if($(window).width() >= 768){
        $(".dropdown-menu").each(function(){
            if ($(this).width() > $('#topmenu ul.nav').width()){
                $(this).css("right","0");
                $(this).css("left","auto");
            }
        });
    }
    if($(window).width() < 992){
        $("#topmenu .level-1:not(.active) .dropdown-toggle").attr('data-toggle','dropdown');
    }else{
        $("#topmenu .level-1 a.dropdown-toggle").removeAttr('data-toggle');
    }
}
$(document).ready(function(){
    resizeTopmenu();
});
$(window).resize(function() {
    resizeTopmenu();
});