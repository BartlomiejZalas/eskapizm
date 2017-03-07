var flexibleCustomWindowWidth = 0;
$(document).ready(function(){
    flexibleCustomWindowWidth = $(this).width();
    if($(".compare-checked").length >0){
		$(".compare-checked").each(function() {
			$(this).addClass('checked');
		});
	}      
});
$(document).on('ready page:load', function () {
	$(".flexible-custom-banners").owlCarousel({
	    items:1,
	    loop:true,	    
	    autoPlay:true,
	    nav:false,
	    stopOnHover:true
	});
    flexibleCustom_SetPages();
    resizeScreen();
});
$(window).resize(function() {    
    if($(window).width()!=flexibleCustomWindowWidth){    
        $(".item-avatar-inner").removeAttr('style');
        $(".option2-inner").removeAttr('style');
        $(".page-style3").removeAttr('style');
		setTimeout('flexibleCustom_SetPages()', 1000);
        flexibleCustomWindowWidth = $(window).width();
    }
    resizeScreen();
});
var offsetSlideWidth = 0;
function flexibleCustom_SetPages(){
	if($(".default-section").length >0){
        $(".default-section").each(function(){
        	var innerHeight = 0;
            $(this).find('.item-avatar-inner').each(function(){
                var itemWidth = $(this).actual('width');
                var itemHeight = itemWidth * h_per_w;
                if(innerHeight == 0){
            		if(flexibleCustomWindowWidth <992){
		            	innerHeight =  (itemHeight + 184);
		            }else{
		            	innerHeight =  (itemHeight + 116);
		            }    	
                }
                $(this).css({'width':itemWidth, 'height':itemHeight});  
                //css = 'width:'+itemWidth+'px; height:'+ itemHeight +'px';                           
            });            
            if(innerHeight == 0) innerHeight = 80;
            $(this).find('.option2-inner').css('height', innerHeight);
            $(this).find('.link-load-more').attr('data-inner-height', innerHeight);
        });
    }
    $(".no-load").hide();
    // style 2
    if($(".flex-layout2-sections").length >0){
        $(".flex-layout2-sections").each(function(){
            var minItemWidth = 230;                  
            var sectionW = $(this).width();
            var i = 1;
            var cw = sectionW;  
            while((sectionW/i) > minItemWidth){
                cw = sectionW/i;                        
                i++;                        
            }
            var itemWidth = Math.ceil(cw * 1000)/1000;
            
            var subHeight = 100;  
            if(flexibleCustomWindowWidth < 992){
                subHeight = 168;
            }                      
            var itemHeight = itemWidth * h_per_w;
            var avatarW = itemWidth-50;
            var avatarH = avatarW * h_per_w;
            var height = itemHeight + 30 + (subHeight - (itemHeight - avatarH));
                     
            $(this).find('.page-style3').each(function(){
                $(this).css({'width':itemWidth, 'height':(itemHeight), 'float':'left'});                
            });
            $(this).find('.item-avatar-inner').each(function(){      
                $(this).css({'width':avatarW, 'height':avatarH});                                         
            });
            $(this).css('height', height);
            $(this).find('.flex-section-inner').each(function(){
                var totalItem = $(this).attr('data-total-item');                
                var innerWidth = totalItem * itemWidth;
                if(innerWidth ==0) innerWidth = 300;
                $(this).attr({'data-item-width':itemWidth, 'data-item-height':itemHeight}).css({'width':innerWidth, 'height':height, 'position':'absolute', 'top':0, 'left':0});                                         
            });
            
            
        });
    }
}
function resizeScreen(){
    if (($(window).width()) <= 767)
	{
	   $('.flex-layout2').each(function(){
           var navObj = $(this).find('.pull-right');
            $(this).find('.flex-layout2-sections').addClass('clearBoth');
	      navObj.insertAfter($(this).find('.box'));
	   });
	}
	else if (($(window).width()) >= 768)
	{
        $('.flex-layout2').each(function(){
           var navObj = $(this).find('.pull-right');
            $(this).find('.flex-layout2-sections').removeClass('clearBoth');
	      navObj.insertAfter($(this).find('.box-title .navbar-brand'));
	   });
	}
}

jQuery(function($){
    $(document).on('click','.ul-list li',function(){        
        if($(this).hasClass('active')) return true;
        var moduleId = $(this).attr('data-module');
        var groupId = $(this).attr('data-group');
        
        var currentActive = $('ul#module-'+moduleId+' li.active');
        var currentActiveModule = currentActive.attr('data-module');
        var currentActiveGroup = currentActive.attr('data-group');
        
        currentActive.removeClass('active');
        $("#section-"+currentActiveModule+"-"+currentActiveGroup).hide();
        $("#section-"+moduleId+"-"+groupId).show();        
        $(this).addClass('active');        
        return true;        
	});
	$(document).on('click','.link-load-more',function(){
		var moduleId = $(this).attr('data-module');
		var groupId = $(this).attr('data-group');  
		var innerHeight = parseInt($(this).attr('data-inner-height'));
		//var totalPage = parseInt($(this).attr('data-total-pages'));
		if(flexibleCustomWindowWidth >=1200) var item = 4;
		else if(flexibleCustomWindowWidth >=992 && flexibleCustomWindowWidth <=1199) var item = 3;
		else if(flexibleCustomWindowWidth >=768 && flexibleCustomWindowWidth <=991) var item = 2;
		else var item = 1;
				
		var countItem = $("#section-inner-"+moduleId+"-"+groupId+" .item").length;
		if(countItem % item == 0){
			var totalPage = parseInt(countItem/item);
		}else{
			var totalPage = parseInt(countItem/item)+1;
		}
		$(this).attr("data-total-pages", totalPage);			
		var sectionInner = $("#section-inner-"+moduleId+"-"+groupId);
		var currentHeight = parseInt(sectionInner.height());
		var currentPage = parseInt(sectionInner.attr('data-current-page'));
		if(currentHeight < (totalPage * innerHeight)){					
			sectionInner.animate({height: (currentHeight+innerHeight) +"px"}, 800);
		}else{
			$(this).parent().find('.no-load').show();			
		}
	});
    // option 2 next
    $(document).on('click','.navNext',function(){
        var moduleId = $(this).attr('data-module');
        var groupEl = $("ul#module-"+moduleId+" li.active");
        var groupId = groupEl.attr('data-group');        
        var section = $("#section-"+moduleId+"-"+groupId);
        var slideWidth = section.width();
        var sectionInner = $("#section-inner-"+moduleId+"-"+groupId);
        var sectionInnerWidth = sectionInner.width();
        var position = sectionInner.position();
        var left = position.left - slideWidth;
        var currentPage = parseInt(sectionInner.attr('data-current-page'));
        
        if((currentPage * slideWidth) < sectionInnerWidth){
            //$(this).parent().find('.group-products-back ').removeClass('disable');
            sectionInner.animate({left: left + "px"}, 800);            
            currentPage++;
            sectionInner.attr('data-current-page', currentPage);
            var lastPage = parseInt(sectionInner.attr('data-last-page'));
            if(lastPage < currentPage){
                sectionInner.attr({'data-last-left': left, 'data-last-page':currentPage});    
            }            			
        }else{
			sectionInner.attr('data-current-page', 1);			
			sectionInner.animate({left: "0px"}, 800);			
        }
	});
    // option 2 preview
    $(document).on('click','.navPrev',function(){
        var moduleId = $(this).attr('data-module');
        var groupEl = $("ul#module-"+moduleId+" li.active");
        var groupId = groupEl.attr('data-group');
        
        var section = $("#section-"+moduleId+"-"+groupId);
        var slideWidth = section.width();
        var sectionInner = $("#section-inner-"+moduleId+"-"+groupId);
        var sectionInnerWidth = sectionInner.width();
        
        var currentPage = parseInt(sectionInner.attr('data-current-page'));
        
        if(currentPage >1){
            var position = sectionInner.position();
            var left = position.left + slideWidth;                                
            sectionInner.animate({left: left + "px"}, 800);            
            currentPage--;
            sectionInner.attr('data-current-page', currentPage);
        }else{
			var lastLeft = sectionInner.attr('data-last-left');
			var lastPage = sectionInner.attr('data-last-page');
			sectionInner.attr('data-current-page', lastPage);
			sectionInner.animate({left: lastLeft + "px"}, 800);
		}    
	});
    
});

