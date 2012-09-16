(function($){
$(init); //init on domload

function init(){
	
	if($('.article-content').length > 0){
		//initCollapse();
	}
	if($('.tabify').length > 0){
		tabify();
	}

	indentSections();
	initTips();
	moveDataTable();
}
function initCollapse(){
	//mw.log('init collapse');
	$(".article-content h2, .article-content h3").collapse({
	    show: function() {
	        this.animate({
	            opacity: 'toggle', 
	            height: 'toggle'
	        }, 300);
	    }
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

	function enterIcon(){

	}
	function leaveIcon(){

	}
	function enterTip(){

	}
	function leaveTip(){

	}

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
			//mw.log('left icon',ev);
			var $toEl = $(ev.toElement);
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
			
			hide_popover();
			
		}
		
		var popover_enter = function(ev){
			//mw.log('entered popover');
		}

		var popover_leave = function(ev){
			//mw.log('left popover');
			var $toEl = $(ev.toElement);

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
			//we want to know when the mouse leaves the icon whether it is over the popover or not
			$popover_el.mouseenter(popover_enter);
			$popover_el.mouseleave(popover_leave);
			$('body').mousemove(mousemove);

			//add tooltips to key icons
			$popover_el.find('.key .iconbar-icon').tooltip({placement:'top'});
		}
		var hide_popover = function(){
			if(!$popover_el.hasClass('in')){
				mw.log('Popover is already hidden');
				return;
			}
			$('body').unbind('mousemove',mousemove);
			$popover_el.find('.key .iconbar-icon').tooltip('hide');
			popover.hide();
			$popover_el.unbind(['mouseenter','mouseleave']);
		}

		$this.mouseenter(icon_enter);
		$this.mouseleave(icon_leave);
	});


	//init general article tips
		
	$('.article-content .definition[title]').tooltip({
	    placement: 'top'
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