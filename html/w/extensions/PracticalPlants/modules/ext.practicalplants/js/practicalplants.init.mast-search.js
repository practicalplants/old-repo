(function($){
	var search_input;
	
	function init(){
		
		search_input = $('input#searchInput');
		var search_input_width = search_input.width();
		
		
		search_input.focus(function(){
			var $this = $(this);
			$this.width(search_input_width*1.5);
		})
		.blur(function(){
			var $this = $(this);
			$this.width(search_input_width);
		});
		
		initAutocomplete();
	}
	
	function doQuery(request, response){
		var term = request.term.replace(' ','*').toLowerCase(),
			query = '';
		/*termVariants = [];
		termVariants.push(term);
		termVariants.push(term.toLowerCase());
		termVariants.push(term.slice(0,1).toUpperCase()+term.slice(1));
		var query = [];
		for(var i=0, l=termVariants.length;i<l;i++){
			query.push( '[[Concept:Plant%20taxonomies]][[Has%20taxonomy%20name::~*'+termVariants[i]+'*]]' );
			query.push( '[[Concept:Plant%20taxonomies]][[Has%20common%20name::~*'+termVariants[i]+'*]]' );
		}*/
		//terms are now stored in lower case for easier searching
		query+='[[Concept:Plant%20taxonomies]][[Has%20search%20name::~*'+term+'*]]';
		query+='|%3FIs%20taxonomy%20type|%3FHas%20common%20name|limit=50&format=json';
		mw.log(query);
		//mw.log('Term variants:', '[[Has%20taxonomy%20name::~*'+termVariants.join('*]] OR [[Has%20taxonomy%20name::~*')+'*]]');
		
		/*
		query is:
		has taxonomy name: aPPle Apple apple
		*/
		
		$.getJSON('/w/api.php?action=taxonomies&query='+query).then(function(data){ 
			//console.log(data.items);
			mw.log('Data is',data);
			if(data){
				results = [];
				var name,taxo,common;
				for(var k in data){
					if(data.hasOwnProperty(k)){
						name = '<span class="name">'+k+'</span>';
						common = ' <span class="common">'+data[k].common+'</span>';
						taxo = ' <span class="taxonomy">'+data[k].taxonomy+'</span>';
						results.push({label: name+common+taxo, value:k});
					}
				}
				mw.log('Responding with results',results);
				response(results);
				search_input.removeClass('loading');
			}
		});
	}
	
	function initAutocomplete(){
		var taxos = [],
			termVariants = [],
			timeout;
		$(search_input).autocomplete({
			html: true,
			delay: 0, //this delay doesn't seem to work, so we recreate it in the source method
			minLength: 3,
			position: { my : "center top", at: "center bottom"},//, offset:'2 0' },
		    source: function(request, response){
		    	
		    	//delay query with a timeout. If the user types more within the timeout then the query is cancelled and another is queued. This is how the 'delay' should work for autocomplete but it doesn't...
		    	clearTimeout(timeout);
		    	timeout = setTimeout(function(){ doQuery(request,response); },500);
		    
		    },
		   	
			change: function(event, ui) {
				
			},
			select: function(event, ui) { 
				//console.log(ui.item);
				search_input.val(ui.item.value).parents('form').submit();
			},
			search: function(event, ui){
				search_input.addClass('loading');
			}
		});
	}
	
$(function(){ init(); });
	
})(jQuery||$);