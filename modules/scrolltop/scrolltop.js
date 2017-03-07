$(document).ready(function(){
 $('.mypresta_scrollup').click(function(){
  $("html, body").animate({ scrollTop: 0 }, 600);
   return false;
 });
 $(window).scroll(function(){
  if ($(this).scrollTop() > 500) {
   $('.mypresta_scrollup').fadeIn();
  } else {
   $('.mypresta_scrollup').fadeOut();
  }
 });
});