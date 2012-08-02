var PracticalPlants = (function($){

	function formatPlantName(name,opts){
		/* Check to see if there's a standard binomial to begin with, and if so apply formatting */
		if(opts===undefined)
			opts = {};
		var binom, binom_match, binom_parts,
			full_match;
		if( binom_match = name.match(/^([\w]+ (?:x )?[\w]+)/) ){
			if(opts.abbreviate!==undefined && opts.abbreviate===true){
				binom_parts = binom_match[1].split(' ');
				if(binom_parts.length>1){
					//grab the first letter of the genus, abbreviate with a period, then add the other parts back on
					binom = binom_parts[0].slice(0, 1) +'. '+binom_parts.slice(1).join(' ');
				}else{
					binom = binom_match[1];
				}
			}else{
				binom = binom_match[1];
			}
			
			binomial = $('<em class="binomial"></em>').text(binom);
		}else{
			return name;
		}
		
		if(name.match(/^([\w]+ (?:x )?[\w]+)$/)){
			name = binomial;
			
		/*match binomial name with cultivar and/or cultivar group. Eg. 
		* Brassica oleracea Capitata Group 
		* Brassica oleracea 'January King'
		* Brassica oleracea (Capitata Group) 'January King'
		* Brassica oleracea Capitata Group 'January King'
		*/
		}else if(full_match = name.match(/^(?:[\w]+ (?:x )?[\w]+)(?: \(?([\w\s]+ Group)\)?)?(?: ?('[\w\s]+')?)?$/)){
			full_match.pop(); //remove first element, it's a match of the whole string and we want to know about the parts
			var cultivar_group = '', cultivar = '';
			
			//cultivar group matched	
			if(full_match[1]!==undefined){
				
				cultivar_group = $(' <span class="cultivar-group"></span>');
				if(full_match[2]!==undefined){
					//cultivar matched, enclose cultivar group in parenthesis
					cultivar_group.text('('+full_match[2]+')');
				}else{
					//no cultivar, no need to enclose group in parenthesis
					cultivar_group.text(full_match[2]);
				}
			}
			//cultivar matched
			if(full_match[2]){
				cultivar = $(' <span class="cultivar"></span>').text(full_match[2]);
			}
			
			name.append(cultivar_group).append(cultivar);;
			
		/* Match binomial name and variety. Eg.
		* Malus domestica var. varietyname
		* Malus domestica var varietyname		
		*/
		}else if(full_match = name.match(/^(?:[\w]+ (?:x )?[\w]+)(?: var\.? )([\w]+)$/)){
			name = binomial;
			name.after(' <span class="variety-var">var.</span>');
			name.after( ' <em class="variety">'+full_match[2]+'</em>');
			
	
		/* Match binomial name and subspecies. Eg.
		* Malus domestica var. varietyname
		* Malus domestica var varietyname		
		*/
		}else if( full_match = name.match(/^(?:[\w]+ (?:x )?[\w]+)(?: (?:ssp\.?|subsp\.?) )([\w]+)$/) ){
			name = binomial;
			name.after(' <span class="subspecies-ssp"> ssp. </span>');
			name.after( ' <em class="subspecies">'+full_match[2]+'</em>');
			
		}else{
			name = name;
		}
		
		return $('<span class="species-name"></span>').append(name);
	};
	
	var methods = {
		formatPlantName: formatPlantName
	};
	
	return methods;
})(jQuery);