(function($){
$(init); //init on domload

function init(){
	
	if($('.article-content').length > 0){
		initCollapse();
	}
	if($('.tabify').length > 0){
		tabify();
	}

	//indentSections();
	initTips();
	initIconbarPopovers();
	initCCNC();
	setArticleContentHeight();
	initStickySidebar();
	initScrollSpyTOC();
	moveDataTable();
}
function initCollapse(){
	//mw.log('init collapse');
	
}
function setArticleContentHeight(){
  $('.article-content').css({'min-height':$('#main-entry').outerHeight() - $('#header').outerHeight() } );
}


var sidebar_top, //sidebar top before we fixed it
    sidebar_btm,
    $sidebar,
    $sidebar_toc,
    $sidebar_toc_ul,
    sidebar_toc_ul_height, //sidebar toc ul height before we change it
    article_height,
    $footer,
    footer_top;
function stickSidebar(){
  var ws = $(window).scrollTop(),
      wh = $(window).height();
  
  //mw.log("Scroll; sidebar top",h,sidebar_top);
  if($sidebar.length){
  
    //set sidebar as static when the window is scrolled to meet it's top
    if(ws > sidebar_top){
      
      //if the footer is visible in the window, make sure the sidebar does not overlap it...
      if(ws+wh > footer_top){
        if(!$sidebar.hasClass('absolute')){
          $sidebar.removeClass('fixed')
                  .addClass('absolute')
                  .css({
                        'top': '',
                        'right':0
                       });
        }
        //mw.log('Sidebar height:',$sidebar.height());
        $sidebar.offset({'top': footer_top-$sidebar.outerHeight() });
      
      
      
      //if the footer is not visible, position the sidebar as fixed and size it to the window height
      }else{
        if(!$sidebar.hasClass('fixed')){
          $sidebar.removeClass('absolute')
                  .addClass('fixed')
                  .css({'top':0});
        }
        $sidebar.css({'height':wh});
        
        //if the sidebar is too tall for the window, make the TOC scrollable
        if($sidebar_toc.position().top + $sidebar_toc.height() > wh - 60){
          $sidebar_toc.addClass('scroll')
          $sidebar_toc_ul.css({ 'height': wh - $sidebar_toc.position().top - 60 });
          //mw.log("setting toc height to",wh,'-',$sidebar_toc.position().top,'=',wh - $sidebar_toc.position().top);
          
          //calculate the percentage of the article height scrolled...
          var percent = (ws-sidebar_top)/article_height;
          
          //mw.log('Scroll percent',percent);
          //mw.log('Sidebar TOC height, scrollTop, percent, actual percent',
                  //$sidebar_toc_ul.height(), $sidebar_toc_ul.scrollTop(), percent, $sidebar_toc_ul.scrollTop()/sidebar_toc_ul_height );
          
          //set the scrolltop of the sidebar toc ul to the same percent of it's height as the article
          $sidebar_toc_ul.scrollTop( sidebar_toc_ul_height * percent );
  
        }else{
          $sidebar_toc.removeClass('scroll')
          $sidebar_toc_ul.css('height','auto');
        }
      }
          
      
    }else{
      $sidebar.removeClass('fixed').removeClass('absolute');
      $sidebar.innerHeight(article_height).css({'bottom':'', 'top':''});
    }
  }
   
}

function initStickySidebar(){
  //these variables do not change on window resize, so we set them here...
  $sidebar = $('#sidebar');
  $sidebar_toc = $('#sidebar #toc-container');
  $sidebar_toc_ul = $('#sidebar #toc-container td > ul');
  $footer = $('#footer');
  sidebar_toc_ul_height = $sidebar_toc_ul.height(); //the initial height of the content
  
  //this function sets variables which need to be refreshed on window resize
  setStickySidebarVars();
  
  if($sidebar.length){
    sidebar_top = $sidebar.offset().top;
    stickSidebar();
    $(window).bind({'scroll':stickSidebar, 'resize':stickSidebar})
             .bind('resize',setStickySidebarVars);
  }
}

//set variables in outer scope, so they aren't calculated on scroll
function setStickySidebarVars(){
  article_height = $('.article-content').outerHeight();
  footer_top = $footer.offset().top;
}

function initScrollSpyTOC(){
  $('body').scrollspy({selector:'#toc td > ul > li > a', offset:0});
  $(window).bind('activate',function(ev){
    //if the toc is scrolled, make sure the active item is visible
    //mw.log('Setting scrollTop of TOC to active element position', $(ev.target).position().top );
    //$('#toc td > ul').scrollTop( $(ev.target).position().top );
    mw.log('Activate!', $(ev.target).find('a').attr('href'));
  });
}

function tabify(){
	$('.tabify').each(function(i){
		var tabs = $(this).find('.tabify-tab');
		tabs.each(function(j){
			if(!$(this).attr('id'))
				$(this).attr('id','tabify-'+i+'-'+j); 
		});
		var headers = tabs.find('.tabify-header').remove();
		var tabcontainer = $('<ul></ul>').append(headers);
		headers.wrap(function(j){
			var tab_id = $(tabs[j]).attr('id') ? $(tabs[j]).attr('id') : 'tabify-'+i+'-'+j;
			return '<li><a href="#'+tab_id+'"></a></li>';
		});
		//headers.wrap('<li></li>');
	
		$(this).prepend(tabcontainer);
	});
	
	$('.tabify').tabs();
}

function indentSections(){
	var toc = $('#toc-wrapper.left');
	
	if(toc.length > 0){
		var toc_top = toc.position().top;
		var toc_bottom = toc_top + toc.outerHeight();
		$('#mw-content-text').find('.article-section').each(function(){
			if($(this).position().top < toc_bottom){
				//if($(this).hasClass('article-section')){
					if($(this).position().top+40 < toc_bottom){
						$(this).addClass('indented');
					}else{
						$(this).css('clear','left');
					}
				//}
			}else{
				return true;
			}
		});
	}
	
}

function initTips(){

	//init general article tips
		
	/*$('.article-content .definition[title]').tooltip({
	    placement: 'top'
	});*/
	
	var opts = {
		style: { name: 'dark', tip: true },
		position: {
			adjust:{
				screen:true
			},
			corner: {
				target: 'topMiddle',
				tooltip: 'bottomMiddle'
			}
		   },
		
		show:{
			delay:0
		}
	};
	
	$('.article-content .definition[title]').qtip(opts);
	
	//$('.article-content .cc-nc .cc-nc-logo[title]').qtip(opts);
	/*opts = {
	    placement: 'top'
	};*/
	$('#plant-uses-qr div.active').each(function(){
		var $this = $(this);
		$(this).qtip($.extend({}, opts,{ content: $(this).html() }));
		/*var content = $this.html();
		opts.content = function(){
		    return content;
		}
    $this.tooltip(opts);*/
  });
	

}

function initCCNC(){
  var opts = {
  	    placement: 'top',
  	    trigger: 'manual',
  	    title: 'Creative Commons NC License',
  	    content: 'This text is only available under a restrictive Non-Commercial license. Please help us liberate this text by editing this article!'
  };
  
  $('.article-content .cc-nc').popover(opts);
  
  $('.article-content .cc-nc .cc-nc-logo')
  .mouseenter(function(){
    $(this).parents('.cc-nc').addClass('active').popover('show');
  })
  .mouseleave(function(){
    $(this).parents('.cc-nc').removeClass('active').popover('hide');
  });
  
}


function initIconbarPopovers(){
  /*var opts = {
  	style: { name: 'cream', tip: true },
  	position: {
  		adjust:{
  			screen:true
  		},
  		corner: {
  			target: 'topMiddle',
  			tooltip: 'bottomMiddle'
  		}
  	   },
  	
  	show:{
  		delay:0
  	}
  };*/
  
  
	/* Init plant article icon bar popovers */
	var opts = {
	    placement: 'top',
	    trigger: 'manual'
	};

	$('#plant-iconbar .iconbar-icon').each(function(){
		var $this = $(this);
		//$(this).qtip($.extend({}, opts,{ content: $(this).html() }));
		var content = $(this).siblings('.iconbar-popover-content').remove();
		opts.content = function(){
		    return content;
		}
		opts.title = function(){
		    var title = $(this).parent('[title]').attr('title');
		    if(title)
		        return title;
		    return false;
		}

		$(this).popover(opts);
		var popover = $(this).data('popover');
		var $popover_el = popover.tip();
		$popover_el.addClass('iconbar-popover'); 

		//we want the mouse to be able to pass into the popover
		var over_icon = false;
		var over_tip = false;
		var popover_tips = []; //array of tips opened by this popover, so we can be sure to destroy them later

		/*
		To allow the mouse to pass from the icon to the popover without triggering the popover to close
		we have to monitor the mouse position. We can compare the x and y co-ordinates of the mouse
		with the .offset() of an element.
		*/
		var mouse = {x:undefined,y:undefined};
		var mousemove = function(event){
			mouse.x = event.pageX;
			mouse.y = event.pageY;
		}

		var mouse_within_bounds = function(el){
			if(    mouse.x > el.offset().left
				&& mouse.x < el.offset().left + el.outerWidth()
				&& mouse.y > el.offset().top
				&& mouse.y < el.offset().top + el.outerHeight()
			){
				mw.log('Mouse within bounds of el',mouse,el.offset());
				return true;
			}
			return false;
		}

		var icon_enter = function(){
			//mw.log('entered icon');
			show_popover();
		}

		var icon_leave = function(ev){
			mw.log('left icon',ev);
			var $toEl = $(ev.relatedTarget);
			var $isPopover = false;
			if($toEl.hasClass('.popover')){
				$isPopover = $toEl;
			}else if($toEl.parents('.popover').length > 0){
				$isPopover = $toEl.parents('.popover');
			}

			if(	$isPopover && $isPopover.get(0) === $popover_el.get(0) ){	
				mw.log('Mouse moved to popover');
				return;
			}
			
			mw.log("Mouse left icon.",$toEl);
			
			hide_popover();
			
		}
		
		var popover_enter = function(ev){
			//mw.log('entered popover');
		}

		var popover_leave = function(ev){
			//mw.log('left popover');
			var $toEl = $(ev.relatedTarget);

			//mw.log('toEL',$toEl);

			var $isIcon = false;
			if($toEl.hasClass('iconbar-icon')){
				$isIcon = $toEl;
			}else if($toEl.parents('.iconbar-icon').length > 0){
				$isIcon = $toEl.parents('.iconbar-icon');
			}

			//if the mouse has moved to the icon, don't hide, let the icon mouseleave handle it
			if($isIcon && $isIcon.get(0) === $this.get(0)){
				mw.log('Popover mouseleave fired. Mouse moved back to icon.');
				return;
			}
			//mw.log('isTip?',$toEl,$toEl.hasClass('tooltip'), $toEl.parent().hasClass('tooltip'), $toEl.parents('.tooltip'));
			//if the mouse moves over a tip that is spawned by this popover, this mouseleave is fired as the tooltip isn't nested within this element
			//as long as the mouse cursor is still within the bounds of this popover, we ignore it
			if(($toEl.hasClass('tooltip') || $toEl.parent().hasClass('tooltip')) && mouse_within_bounds($popover_el)){
				mw.log("Popover mouseleave event fired, but mouse still within bounds of popover. Ignoring.");
				return;
			}
			popover.hide();
		}

		var show_popover = function(ev){
			//if the popover is not currently visible, show it
			if($popover_el.hasClass('in')){
				mw.log('Popover is already visible');
				return;
			}
			popover.show();
			if($popover_el.offset().left < 0){
			  var popover_arrow_left = $this.offset().left;
			  $popover_el.css('left',0);
			  $popover_el.find('.arrow').css('left',popover_arrow_left+15);
			}
			/*if($popover_el.offset().right > $(window).width()){
			  var popover_adjust = $popover_el.offset().right;
			  $popover_el.css('left',0);
			  $popover_el.find('.arrow').css('left',(popover_minus*-1)-8`);
			}*/
			//we want to know when the mouse leaves the icon whether it is over the popover or not
			$popover_el.mouseenter(popover_enter);
			$popover_el.mouseleave(popover_leave);
			$('body').mousemove(mousemove);

			//add tooltips to key icons
			$popover_el.find('.key .iconbar-icon').each(function(){
			  $(this).tooltip({placement:'top'});
			  popover_tips.push($(this).data('tooltip'));
        mw.log('added tip', popover_tips);
			});
		}
		var hide_popover = function(){
			if(!$popover_el.hasClass('in')){
				mw.log('Popover is already hidden');
				return;
			}
			$('body').unbind('mousemove',mousemove);
			$popover_el.find('.key .iconbar-icon').tooltip('destroy');
			for(var i=0,l=popover_tips.length,tip; i<l; i++){
			  tip = popover_tips.shift();
			  mw.log('Destroying tip',tip);
			  tip.destroy();
			};
			popover.hide();
			$popover_el.unbind(['mouseenter','mouseleave']);
		}

		$this.mouseenter(icon_enter);
		$this.mouseleave(icon_leave);
	});
}

function moveDataTable(){
	var datatable = $('#plant-datatable');
	if(datatable.length>0){
		var refs = $('#article-references');
		
		if(refs.length > 0){
			refs.before(datatable);
		}else{
			$('#mw-content-text').append(datatable);
		}
	}
}

})(jQuery);