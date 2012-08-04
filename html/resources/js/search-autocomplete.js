(function($){

$(function(){ initSearch() });

function initSearch(){
	initAutocomplete();
	
	function initAutocomplete(){
		var taxos = [],
			termVariants = [];
		
		//console.log($('input#searchInput'));
		$('input#searchInput').autocomplete({
			html: true,
			delay: 0,
			minLength: 3,
			position: { my : "center top", at: "center bottom"},//, offset:'2 0' },
		    source: function(request, response){
		    	var term = request.term.replace(' ','*');
		    	termVariants = [];
		    	termVariants.push(request.term);
		    	termVariants.push(request.term.toLowerCase());
		    	termVariants.push(request.term.slice(0,1).toUpperCase()+request.term.slice(1));
				
				//console.log('Term variants:', '[[Has%20taxonomy%20name::~*'+termVariants.join('*]] OR [[Has%20taxonomy%20type::~*')+'*]]');
		    	
		    	$.getJSON('/w/api.php?action=taxonomies&query=[[Concept:All%20taxonomies]][[Has%20taxonomy%20name::~*'+termVariants.join('*]] OR [[Has%20taxonomy%20name::~*')+'*]]|%3FIs%20taxonomy%20type|limit=50&format=json').then(function(data){ 
		    		//console.log(data.items);
		    		//console.log('Data is',data);
		    		if(data){
		    			taxos = [];
		    			var t;
		    			for(var k in data){
		    				if(data.hasOwnProperty(k)){
		    					t = ' <span>'+data[k]+'</span>';
		    					taxos.push({label: k+t, value:k});
		    				}
		    			}
		    			//console.log('Responding with taxos',taxos);
		    			response(taxos);
		    		}
		    	});
		    	
		    
		    },
		   	
			change: function(event, ui) {
				
			},
			select: function(event, ui) { 
				//console.log(ui.item);
				$('input#searchInput').val(ui.item.value).parents('form').submit();
			}
		});
	}
}
	

	
})(jQuery||$);