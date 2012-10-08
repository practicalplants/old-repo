(function($){
	$(init);
	
	function init(){
		if($('.plant-form').length > 0)
			initPlantForm();
		
		/*$(".collapsing-form").collapse({
			head:'.collapser',
			group:'.collapsee',
		    show: function() {
		        this.animate({
		            opacity: 'toggle', 
		            height: 'toggle'
		        }, 300);
		    	//this.siblings('.collapse-notice').remove();
		    },
		    hide: function(){
		    	this.animate({
		    	    opacity: 'toggle', 
		    	    height: 'toggle'
		    	}, 300);
		    	//this.after('<div class="collapse-notice">This content is hidden. Click the heading to display it.</div>');
		    }
		});*/
		
		//$( ".collapsing-form" ).accordion({ header: '.collapser', autoHeight: false, clearStyle:true, collapsible:true, icons:false, navigation:true });
		$('.collapsing-form').bind('accordionchange', function(event, ui) {
			$(ui.newHeader).ScrollTo();
			
			
		  /*ui.newHeader // jQuery object, activated header
		  ui.oldHeader // jQuery object, previous header
		  ui.newContent // jQuery object, activated content
		  ui.oldContent // jQuery object, previous content*/
		});
		
		initSidebarButtons();
		
	}
	
	function initSidebarButtons(){
	  var save_button = $('#sidebar-save-button')
	      , edit_form = $('#editform')
	      , sf_form = $('#sfForm');
	  if(save_button.length){
	    if(edit_form.length){
	      save_button.click(function(){ edit_form.submit(); return false; });
	    }
	    if(sf_form.length){
	      save_button.click(function(){ sf_form.submit(); return false; });
	    }
	  }
	}
	
	function initPlantForm(){
		
		
		//function createTabs
		
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
		
		
		(function initPropagation(){
			var container = $('#plantform-propagation');
			var tabs = container.tabs();
			var checkboxes = $('#plant-propagation-from').find('input[type=checkbox]');
			var disable = [];
			var values = [];
			checkboxes.each(function(i){
				values.push($(this).val());
				if(!$(this).attr('checked'))
					disable.push(i+1);
			});
			console.log('Values, disable',values,disable);
			//for(var i=0,l=disable.length;i<l;i++){
			//	container.tabs('disable',disable[i]);
			//}
			container.tabs('option','disabled',disable);
			checkboxes.change(function(){
				var val = $(this).val();
				var index = values.indexOf( $(this).val() );
				if(index>-1)
					index++;
				if($(this).attr('checked')){
					container.tabs('enable',index);
				}else{
					container.tabs('disable',index);
				}
			});
		})();
		
		
		//load 
		var $list = $('#plantform-interactions-list');
		$list.addClass('loading');
		var $list_ul = $('<ul class="interactions-list"></ul>');
		
		
		function updateList(api_result){
			if(api_result && api_result.query!==undefined && api_result.query.results!==undefined){
				var i = 0;
				var $listitem;
				var $listitem_link;
				var item_text = '?';
				var r; //result
				var this_side, other_side;
				
				$list_ul.empty();
				
				for(var k in api_result.query.results){
					if(!api_result.query.results.hasOwnProperty(k))
						continue;
					r = api_result.query.results[k];
					$listitem = $('<li class="interactions-list-item"></li>');
					this_side = (r.printouts['Has left member'][0]===mw.config.get( 'wgTitle' )) ? 'left' : 'right';
					other_side = (this_side==='right') ? 'left' : 'right';
					/*switch(r.printouts['Has direction']){
						case '>':
							item_text = r.printouts['Has '+other_side+' member']+' ('+r.printouts['Has impact'].toLowerCase()+' impact)';
						break;
						case '<':
							item_text = r.printouts['Has right member']+' has a '+r.printouts['Has impact']+' impact on '+r.printouts['Has left member'];
						break;
						case '<>':
							tem_text = r.printouts['Has left member']+' and '+r.printouts['Has right member']+'have a mutually '+r.printouts['Has impact']+' impact';
						default:
							item_text = r.printouts['Has left member']+' and '+r.printouts['Has right member']+'have a mutually '+r.printouts['Has impact']+' impact';
						break;
					}*/
					item_text = r.printouts['Has '+other_side+' member'][0]+' ('+r.printouts['Has impact'][0].toLowerCase()+' impact) ';
					
					
					$listitem_link = $(' (<a href="'+r.fullurl+'?action=formedit" target="blank">edit</a> | <a href="'+r.fullurl+'?action=delete" target="blank">delete</a>)');
					$listitem.append(item_text).append($listitem_link);
					$listitem.append('<p>'+r.printouts['Has details']+'</p>');
					$list_ul.append($listitem);
					i++;
				}
				if(i===0){
					$list.append($("<span>No interactions found for "+mw.config.get( 'wgTitle' )+'</span>').click(loadInteractions) );
				}else{
					$list.append($list_ul);
				}
			}
		}
		
		function loadInteractions(){
			var api = new mw.Api();
			api.get( {
			    action: 'ask',
			    query: '[[Interaction:+]][[Has member::'+ mw.config.get( 'wgTitle' ) +']] |? Has member |? Has direction |? Has details |?Has impact |?Has left member |?Has right member'
			    
			},{
				ok:updateList,
				err: function(){
					$list.removeClass('loading');
					$list.append($('<span>Failed to load interactions. Click here to try again.</span>').click(loadInteractions));
				}
			});
		}
		
		function createdEvent(){
			mw.log('Received create event from SemanticAjaxCreate');
			loadInteractions();
		}
		
		loadInteractions();
		
		//when semantic ajax create is loaded, attach events
		mw.loader.using( 'ext.saci.main', function () {
			mw.log('Attaching create event to SemanticAjaxCreate');
			SemanticAjaxCreate.attachEvent('created',createdEvent);
		});
		
	}

	
})(jQuery);