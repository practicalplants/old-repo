
jQuery(document).ready(function(){

	// additional styles
		var containerStyle	= '';
		var buttonStyle		= '';
		
	// variables
		var dropdown	= jQuery('#Form_CategoryID')
		var options		=  dropdown.find('option');
		var container	= $('<div class="category-buttons"/>').addClass(containerStyle)
		
	// update category text
		$('.Category label').text('Category:');
		
	// create buttons
		options.each(function(i, e){
			
			// variables
				var opt		= $(e);
				var val		= opt.attr('value');
				var button	= $('<a />')
					.attr('href', '#')
					.bind('click', function(event){
								
						// update dropdown
							dropdown.val(val);
							
						// reset all buttons
							$(event.target)
								.closest('div')
								.find('a')
								.removeClass('selected');
								
						// update selected button
							$(event.target)
								.addClass('selected');
								
							return false;
					})
					.addClass(buttonStyle)
					.text(opt.text());
					
			// select
				if(val == dropdown.val())
				{
					button.addClass('selected')
				}
					
			// append
				container.append(button);
		})
		
	// append after and hide dropdown
		dropdown
			.after(container)
			.hide();

});

