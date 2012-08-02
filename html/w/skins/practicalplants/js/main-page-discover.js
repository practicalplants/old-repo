{
/* This is the settings file for the main page discover instance (extensions/SemanticDiscovery).
*/
query:{
	initial:'[[Has primary image::+]]',
	conditions:'<q>[[Category:Plant]] OR [[Category:Plant Species]]</q>',
	printouts:['Has image','Has common name','Has shade tolerance','Has sun preference','Has water requirements','Has hardiness zone','Has heat zone'],
	limit:9
},
collapsed: true,
filter_groups:[
	{
		label: 'Environment'
	}
],
filters:[
	{
		group:0,
		property:'Has primary image',
		label:'Image',
		collapsed:false
	},
	{
		group:0,
		property:'Has shade tolerance',
		label:'Shade tolerance',
		values:['permanent deep shade', 'permanent shade', 'partial shade', 'light shade', 'no shade']
	},
	{
		group:0,
		property:'Has sun preference',
		label:'Sun preference',
		values: ['full sun', 'partial sun', 'indirect sun']
	},
	{
		group:0,
		property:'Has water requirements',
		label:'Water requirements',
		values: ['low','moderate','high','aquatic']
	},
	{
		group:0,
		property:'Has soil ph preference',
		label:'Soil PH',
		values:['very acid','acid','neutral','alkaline','very alkaline']
	},
	{
		group:0,
		property:'Has soil texture preference',
		label:'Soil texture',
		values: ['sandy','loamy','clay','heavy clay']
	},
	{
		group:0,
		property:'Has soil water retention preference',
		label:'Soil humidity',
		values: ['well drained','moist','wet']
	},
	{
		group:0,
		property:'Has hardiness zone',
		label:'USDA Hardiness zone',
		values: ['1','2','3','4','5','6','7','8','9','10','11','12']
	},
	{
		group:0,
		property:'Has heat zone',
		label: 'RHS Heat zone',
		values: ['1','2','3','4','5','6','7','8','9','10','11','12']
	},
	{
		group:0,
		properties:['tolerates wind','tolerates maritime exposure','tolerates air pollution','tolerates nutritionally poor soil'],
		values:['Yes','Yes','Yes','Yes'],
		label:'Environmental tolerances'
	},
	{
		group:0,
		property:'Inhabits ecosystem niche',
		label:'Ecosystem niche/Layer',
		values:['Canopy','Secondary canopy','Shrub','Herbaceous','Soil surface','Rhizosphere','Climber']
	},
	{
		property:'Is deciduous or evergreen',
		label:'Deciduous/Evergreen',
		values:['deciduous','evergreen']
	},
	{
		property:'Is herbaceous or woody',
		label:'Herbaceous/Woody',
		values:['herbaceous','woody']
	}				
],
templates:{
	result: function(result,templates){
		var item = $('<div class="discover-result-item"></div>'),
			printout,
			printouts,
			print_out_values;
		
		function thumbData(data){
			console.log('Thumbdata',data);
			//as a temporary hack to prevent mediawiki parser encoding the ampersands &&, we just have to use two if statements
			if(data.query!==undefined){
				if(data.query.pages!==undefined){
					for(var key in data.query.pages){
						if(!data.query.pages.hasOwnProperty(key)) 
							break;
						if(data.query.pages[key].imageinfo!==undefined){
							console.log('setting background image to',data.query.pages[key].imageinfo.ii.thumburl);
							item.css('background-image',data.query.pages[key].imageinfo.ii.thumburl);
							break;
						}
					}
				}
			}
		}
		
		function thumbErr(){
			
		}
			
		if(typeof result.printouts=='object'){
			printouts = [];
			var common_name = '';
			for(var key in result.printouts){
				print_out_values = [];
				printout = undefined;
				for(i=0,l=result.printouts[key].length; i<l; i++){
					print_out_values.push( result.printouts[key][i].fulltext );
				}
				if(key=='Has image'){
					
					$.get('http://practicalplants.local/w/api.php?action=query&titles=Image:'+print_out_values[0]+'&prop=imageinfo&iiurlwidth=200&iiprop=url&format=json')
						.then(thumbData,thumbErr);
				}else if(key==='Has common name'){
					//don't print it here, we add it to the title
					common_name = '<br><span class="common-name">'+print_out_values[0]+'</span>';
				}else{
				
					if(templates.printout_property[key]!==undefined){
						printout = templates.printout_property[key](print_out_values);
					}else{
						printout = templates.printout(key,print_out_values);
					}
				}
				if(printout)
					printouts.push(printout);									
			}
		}
		item.append( templates.printouts(printouts) );
		item.append( $('<a href="'+result.fullurl+'" class="title"></a>').append( PracticalPlants.formatPlantName(result.fulltext).append(common_name) );
		
		return item;
	}
}
}