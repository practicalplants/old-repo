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
	//console.log('init collapse');
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
	//$('.article-content [title]').colorTip({color:'yellow'});
	$('.article-content .definition[title], .article-content .infobox [title]').qtip({ 
		style: { name: 'cream', tip: true },
		position: {
		      corner: {
		         target: 'topMiddle',
		         tooltip: 'bottomMiddle'
		      }
		   },
		show:{
			delay:0
		}
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