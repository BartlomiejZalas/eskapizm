$(document).ready(function(){
	if($("#index #vslider_body").length >0){
		$("html").addClass("fsvs");
	    var slider = $.fn.fsvs({
	        speed               : 1000,
	        bodyID              : 'vslider_body',
	        selector            : '> .slide',
	        mouseSwipeDisance   : 40,
	        afterSlide          : function(){},
	        beforeSlide         : function(){},
	        endSlide            : function(){},
	        mouseWheelEvents    : true,
	        mouseWheelDelay     : false,
	        mouseDragEvents     : true,
	        touchEvents         : true,
	        arrowKeyEvents      : true,
	        pagination          : true,
	        nthClasses          : 2,
	        detectHash          : true
	    });	
	} 
	
	if($(".compare-checked").length >0){

        $(".compare-checked").each(function() {

            $(this).addClass('checked');		

        });	

    }
    /*
	        jQuery('.66.products-grid').owlCarousel({
	            items: 4,
	            itemsDesktop: [1199, 3],
	            itemsDesktopSmall: [991, 3],
	            itemsTablet: [768, 2],
	            itemsMobile: [479, 1],
	            lazyLoad: true,
	            pagination: false,
	            navigation: true
	        });
            */
    
    $('.66.products-grid').owlCarousel({        
        lazyLoad: true,
        margin:0,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
                nav:true
            },
            480:{
                items:2,
                nav:true
            },
            992:{
                items:3,
                nav:true
            },
            1200:{
                items:4,
                nav:true
            }
        }
    });
    
    $('.saleproduct .products-doubled').owlCarousel({        
        lazyLoad: true,
        margin:0,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
                nav:true
            },
            768:{
                items:2,
                nav:true
            },
            1199:{
                items:3,
                nav:true
            }
        }
    });
    if($(".owl-banners").length >0){
       $('.owl-banners').owlCarousel({        
            lazyLoad: true,
            margin:0,
            responsiveClass:true,
            responsive:{
                0:{
                    items:1,
                    nav:true
                }            
            }
        }); 
    }
    $('.countdown').each(function(){
		var $this = $(this),        
			endDate = $this.data(),
            untilTime = endDate.time,
			until = new Date(
				endDate.year,
				endDate.month || 0,
				endDate.day || 1,
				endDate.hours || 0,
				endDate.minutes || 0,
				endDate.seconds || 0
			);
        
            //alert(untilTime);
		// initialize
		$this.countdown({
			until : untilTime,
			format : 'dHMS',
            layout: '<div class="count-date"><span class="value-date day">{dn}</span><span class="format-date">{dl}</span></div><div class="count-date"><span class="value-date day">{hn}</span><span class="format-date">{hl}</span></div><div class="count-date"><span class="value-date day">{mn}</span><span class="format-date">{ml}</span></div><div class="count-date"><span class="value-date day">{sn}</span><span class="format-date">{sl}</span></div>',
			labels : [txt_years, txt_month, txt_weeks, txt_days, txt_hours, txt_min, txt_sec]
		});
	});
    
});