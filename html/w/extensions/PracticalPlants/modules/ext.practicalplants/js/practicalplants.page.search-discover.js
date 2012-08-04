(function($){

var image_requests = [],
	image_request_els = [],
	debug = false;
	
function detect(){
	//if element exists, init 
	if($('#search-discover').length > 0)
		init()
}

function log(){
	if(debug)
		console.log.apply(console,arguments);
}
	
function init(){
	$('#search-discover').discover( getOptions() );
}

function addImageRequest(title,el){
	log('addImageRequest',arguments);
	image_requests.push(title);
	image_request_els.push(el);
	el.addClass('loading');
}

function performImageRequests(){
	log('performImageRequest',image_requests);
	var values = image_requests.map(function(val){ return 'Image:'+val; });
	
	$.get(mw.config.get( 'wgServer' )+mw.config.get( 'wgScriptPath' )+'/api.php?action=query&titles='+values.join('|')+'&prop=imageinfo&iiurlwidth=200&iiprop=url&format=json')
		.then(imageRequestSuccess,imageRequestError);
}

function imageRequestSuccess(data){
	log('Thumbdata',data);
	//as a temporary hack to prevent mediawiki parser encoding the ampersands &&, we just have to use two if statements
	if(data.query!==undefined && data.query.pages!==undefined){
		var filename;
		for(var key in data.query.pages){
			if(!data.query.pages.hasOwnProperty(key)) 
				continue;
			if(data.query.pages[key].imageinfo!==undefined){
				filename = data.query.pages[key].title.replace(/(File:|Image:)/,'');
				log('setting background image to',data.query.pages[key].imageinfo[0].thumburl);
				image_request_els[image_requests.indexOf(filename)].css('background-image','url('+data.query.pages[key].imageinfo[0].thumburl+')').removeClass('loading');
			}
		}
	}
	image_requests = [];
	image_request_els = [];
}

function imageRequestError(){
	image_requests = [];
	image_request_els = [];
}

function resultTemplate(result,templates){
	var item = $('<a class="discover-result-item" href="'+result.fullurl+'"></a>'),
		printout,
		printouts,
		print_out_values,
		common_name,
		image;
	
	
	function thumbErr(){
		
	}
	
	if(typeof result.printouts=='object'){
		printouts = [];
		common_name = '';
		for(var key in result.printouts){
			print_out_values = [];
			printout = undefined;
			for(i=0,l=result.printouts[key].length; i<l; i++){
				print_out_values.push( result.printouts[key][i].fulltext );
			}
			if(key=='Has image'){
				image = print_out_values[0];
				addImageRequest(print_out_values[0],item);
			}else if(key=='Has common name'){
				common_name = print_out_values[0];
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
		if(image===undefined){
			item.addClass('noimage');
		}
	}
	//item.append( templates.printouts(printouts) );
	var title = $('<a href="'+result.fullurl+'" class="title"></a>').append( PracticalPlants.formatPlantName(result.fulltext) );
	if(common_name){
		title.append(' <span class="common-name">'+common_name+'</span>');
	}
	
	item.append( title );
	
	return item;
}

function getOptions(){
return {
/* This is the settings file for the main page discover instance (extensions/SemanticDiscovery).
*/
	query:{
		initial:'',
		conditions:'[[Category:Plant]]',
		printouts:['Has image','Has common name','Has shade tolerance','Has sun preference','Has water requirements','Has hardiness zone','Has heat zone'],
		limit:50
	},
	debug: debug,
	collapse: true,
	filter_groups:[
		{
			label: 'Environment'
		}
	],
	filters:[
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
			label: 'AHS Heat zone',
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
	events:{
		afterDrawUpdate: performImageRequests
	},
	templates:{
		result:resultTemplate
	}
};
}

$(detect);//check for element on domload

//return methods;
	
})(jQuery);