/* Super hacky get-it-done version which uses a new window instead of a javascript modal */
window.SemanticAjaxCreate = (function($){
	
	var settings = {
	};
	var options;
	var events = ['init','open-modal','close-modal','created'];
	var event_handlers = {};
	
	function init(opts,fields){
		setOptions(opts);
		
		log('SemanticAjaxCreate.init',options);
		
		/* Temporary hack! This needs to be redone  */
		var container = $(options.container);
		container.empty();
		var family = $();
		var genus = $();
		var input = $('<input type="text">');
		var button = $('<input type="button">').click(buttonClick);
		var new_window;
		function windowClose(){
			log('Received unload event from child window');
			fireEvent('created');
		}
		function buttonClick(){
			if(input.val().length < 1){
				alert('Please enter a species name');
				return;
			}
			new_window = window.open('/w/index.php?title=Special:FormEdit/Interaction&Interaction[left]='+escape(options.pagename)+'&Interaction[right]='+input.val(), 'add-interaction')
			new_window.onunload = windowClose;
		}
		var form = $('<div id="add-interaction-subform"></div>').append(input).append(button);
		container.append(form);
		
		var list = $('<div id="interactions-list"></div>');
	}
	
	function setOptions(opts){
		options = $.extend(settings,opts);
	}
	
	function log(){
		mw.log.apply(mw,arguments);
	}
	
	function attachEvent(event, callback){
		if(events.indexOf(event) < 0){
			log("Cannot attach handler to event. Event name does not exist: "+event);
			return false;
		}
		if(event_handlers[event]===undefined){
			event_handlers[event]=[];
		}
		event_handlers[event].push(callback);
		//log('Attached new event callback for event: '+event);
		//log(event_handlers)
	}
	
	function fireEvent(event, args){
		if(event_handlers[event]!==undefined){
			log('Firing '+event+' event');
			for(var i=0, l=event_handlers[event].length;i<l;i++){
				event_handlers[event][i].apply(event_handlers[event][i],args);
			}
		}
	}
	
	var methods = {
		init: init,
		attachEvent: attachEvent
	};
	
	return methods;

})(jQuery);