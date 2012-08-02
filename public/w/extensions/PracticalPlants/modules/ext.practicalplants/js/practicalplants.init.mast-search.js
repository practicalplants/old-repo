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
	
	
	function initAutocomplete(){
		var taxos = [],
			termVariants = [];
		
		//console.log(taxos);
		$(search_input).autocomplete({
			html: true,
			delay: 0,
			minLength: 3,
			position: { my : "center top", at: "center bottom"},//, offset:'2 0' },
		    source: function(request, response){
		    	var term = request.term.replace(' ','*');
		    	termVariants = [];
		    	termVariants.push(term);
		    	//termVariants.push(term.toLowerCase());
		    	//termVariants.push(term.slice(0,1).toUpperCase()+term.slice(1));
				var query = '';
				query+='[[Concept:Plant%20taxonomies]][[Has%20taxonomy%20name::~*'+term+'*]]';
				query+=' OR [[Concept:Plant%20taxonomies]][[Has%20common%20name::~*'+term+'*]]'
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
		    		}
		    	});
		    	
		    
		    },
		   	
			change: function(event, ui) {
				
			},
			select: function(event, ui) { 
				//console.log(ui.item);
				search_input.val(ui.item.value).parents('form').submit();
			}
		});
	}
	
$(function(){ init() });
	
})(jQuery||$);