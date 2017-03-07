$(document).ready(function() {
    var discount_owl = $("#discountproducts_list");
  discount_owl.owlCarousel({
      items : 2,
      itemsDesktop : [1199,2],
      itemsDesktopSmall : [974,2],
      itemsMobile :	[479,1],
      navigation : false,
      slideSpeed: 500
  });
  $("#discount_next").click(function(){
    discount_owl.trigger('owl.next');
  })
  $("#discount_prev").click(function(){
    discount_owl.trigger('owl.prev');
  })
});