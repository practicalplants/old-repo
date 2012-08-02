<?php
//phpinfo();exit;
$filebase = 'export-2012-05-31';
$ext='.csv';
$dir = __DIR__.'/';
$tofile = $dir.$filebase.'-0'.$ext; 
//if($tofile){
//	$file = fopen($tofile,'w');
//}
$per_block = 1000;
$file=true;

$names = array('Toona sinensis','Tilia tomentosa','Tilia platyphyllos','Tilia cordata','Tilia americana','Quercus petraea','Quercus robur','Pinus edulis','Pinus cembra','Pinus armandii','Prunus dulcis','Juglans nigra','Ginkgo biloba','Corylus maxima','Corylus avellana','Castanea sativa','Carya ovata','Carya laciniosa','Carya illinoinensis','Caragana arborescens','Araucaria araucana','Sambucus nigra','Prunus spinosa','Prunus cerasifera','Morus nigra','Hippophae salicifolia','Hippophae rhamnoides','Elaegnus umbellata','Halesia carolina','Elaeagnus x ebbingei','Diospyros virginiana','Diospyros lotus','Diospyros kaki','Decaisnea fargesii','Crataegus monogna','Cornus mas','Cornus kousa','Cornus capitata','Asimina triloba','Amelanchier laevis','Amelanchier lamarckii','Amelanchier canadensis','Pyrus ussuriensis','Pyrus pyrifolia','Pyrus communis','Prunus salicina','Prunus persica','Prunus insititia','Prunus domestica','Fragaria x ananassa','Fragaria viridis','Fragaria virginiana','Fragaria vesca','Fragaria moschata','Juglans regla','Fragaria chiloensis','Ficus carica','Mespilus germanica','Cydonia oblonga');
$names = array("Malus domestica");
$names = array('Elaeagnus umbellata','Prunus cerasus','Juglans regia','Alnus incana','Hippophae rhamnoides turkestanica','Arbutus unedo');

$columns = array( 'Title' );
$planttemplate = array(
	'common', 'binomial', 'family', 'genus', 
	'variety', 'cultivar', 'cultivar group',
	'edible part and use', 'material part and use', 'medicinal part and use',
	'functions as', 'forage for',
	'sun', 'shade', 'hardiness zone', 'heat zone', 'water', 'drought', 'salinity',
	'soil water retention', 'soil texture', 'soil ph',
	'wind', 'maritime', 'pollution', 'poornutrition',
	'ecosystem niche', 'native range', 
	'life cycle', 'herbaceous or woody', 'deciduous or evergreen', 
	'growth rate', 'mature measurement unit', 'mature height', 'mature width', 
	'flower type', 'flower color', 'fertility', 'pollinators'
	
	
);


$conversion = array(
	'common_name','latin_name','family','',
	'','','',
	'','','',
	'','',
	'shade','shade','hardiness','','moisture','drought','salinity',
	'well_drained','soil','ph',
	'wind','wind','pollution','poor_soil',
	'habit','range',
	'habit','','deciduous_or_evergreen',
	'growth_rate','','height','width',
	'flower_type','','self_fertile','pollinators'
	
);

$rows = array();

/*
Shade conversion: Full, Semi, None
Full = Tolerates permanent shade
Semi = Tolerates partial shade
None = Shade intolerant, prefers full sun
Full/Semi = Tolerates permanent shade, prefers partial sun
Semi/None = Tolerates partial shade, prefers full sun
Full/Semi/None = Tolerates permanent shade, prefers full sun

*/


$db = new mysqli('127.0.0.1','root','','pfaf');
$species = $db->query('SELECT  * FROM species WHERE latin_name IN ("'.implode('","',$names).'")  ORDER BY latin_name ASC');
//echo 'SELECT  * FROM species WHERE latin_name IN ("'.implode('","',$names).'")  ORDER BY latin_name ASC'; exit;
if(!$species){
	print_r($species); exit;
}
$species_ref_ids = array();

function replaceRefs($text){
	$refs = findRefs($text);
	foreach($refs as $r){
		$text = str_replace($r['string'],$r['wikitext'],$text);
	}
	return $text;
}
function findRefs($text,$ids_only=false){
	global $species_ref_ids;
	preg_match_all('/\[(([0-9]+[,\s]*)+)*\]/i',$text,$matches);
	//print_r($matches);
	$refs = array();
	$ref_ids = array();
	foreach($matches[0] as $k => $m){
		$ids = explode(',',$matches[1][$k]);
		$wikitext = '';
		foreach($ids as &$id){
			$id = trim($id);
			if(!in_array($id,$ref_ids))
				$ref_ids[]=$id;
			if(!in_array($id,$species_ref_ids))
				$species_ref_ids[] = $id;
			$wikitext.='{{Ref | PFAFimport-'.$id.'}}';
		}
		$refs[$m] = array(
			'string' => $m,
			'ids' => $ids,
			'wikitext' => $wikitext
		);
	}
	
	//echo $text;
	return $ids_only ? $ref_ids : $refs;
}
function createRefNameFromId($id){
	return 'PFAFimport-'.$id;
}
$i=0;
while($s = $species->fetch_assoc() ){
	
	$species_ref_ids = array();
	$morefields = $db->query('SELECT height,width,canopy,secondary,ground_cover,hedge FROM location WHERE latin_name=\''.$s['latin_name'].'\'');
	if($morefields && $morefields = $morefields->fetch_assoc()){
		$s = array_merge($s, $morefields);
	}
		
	/* Set values for the PFAF import template, which displays the text fields and sets some article flags */
	$plant = array(
		'cultivation'=>mb_convert_encoding(replaceRefs($s['cultivation_details']),'UTF-8','ASCII'),
		'propagation'=>mb_convert_encoding(replaceRefs($s['propagation_1']),'UTF-8','ASCII'),
		'range'=>mb_convert_encoding(replaceRefs($s['range']),'UTF-8','ASCII'),
		'habitat'=>mb_convert_encoding(replaceRefs($s['habitat']),'UTF-8','ASCII'),
		'toxicity notes'=>mb_convert_encoding(replaceRefs($s['known_hazards']),'UTF-8','ASCII'),
		'material use notes'=>mb_convert_encoding(replaceRefs($s['uses_notes']),'UTF-8','ASCII'),
		'edible use notes'=>mb_convert_encoding(replaceRefs($s['edible_uses']),'UTF-8','ASCII'),
		'medicinal use notes'=>mb_convert_encoding(replaceRefs($s['medicinal']),'UTF-8','ASCII'),
		'title irregular'=>'No'
	);

	
	$botref_ids = explode(',',$s['botanical_references']);
	foreach($botref_ids as &$id)
		$id = trim($id);
		if(!in_array($id,$species_ref_ids))
			$species_ref_ids[] = $id;
	
	if(count($species_ref_ids) > 0){
		$ref_ids = implode(' OR id=',$species_ref_ids);
		$refs = $db->query('SELECT * FROM booklist WHERE id='.$ref_ids);
		$pfaf_refs = array();
		if($refs){
			while($ref = $refs->fetch_assoc() ){
				$pfaf_refs[$ref['id']] = $ref;
			}
		}
	}
	
	//print_r($s);
	
	$medicinal = $db->query('SELECT * FROM medic WHERE latin_name=\''.$s['latin_name'].'\'');
	if(!$medicinal || !$medicinal = $medicinal->fetch_all(MYSQLI_ASSOC))
		$medicinal = array();
	$edible = $db->query('SELECT * FROM edib WHERE latin_name=\''.$s['latin_name'].'\'');
	if(!$edible || !$edible = $edible->fetch_all(MYSQLI_ASSOC))
		$edible = array();
	$material = $db->query('SELECT * FROM other WHERE latin_name=\''.$s['latin_name'].'\'');
	if(!$material || !$material = $material->fetch_all(MYSQLI_ASSOC))
		$material = array();
	
	$row = array();
	$references = array();
	
	$name = str_replace('  ',' ',trim($s['latin_name'])); //some records have a double space
	$nameparts = explode(' ',$name);
	$namepartscount = count($nameparts);
	if($namepartscount!==2 || ($namepartscount===2 && $nameparts[1]=='species')){
		//name is composed of more or less than two parts, or it's a genus/general hybrid record
		if( !(isset($nameparts[1]) && $nameparts[1]==='x') ){
			$plant['title irregular'] = 'Yes';
		}
		
	}
	
	$row['Title'] = $name;
	
	$genus = '';
	if($nameparts[0]!=='x')
		$genus=$nameparts[0];
	
	$plant['common'] = $s['common_name'];
	$plant['binomial'] = $s['latin_name'];
	$plant['family']= $s['family'];
	$plant['genus'] = $genus;
	$plant['variety'] = '';
	$plant['cultivar'] = ''; 
	$plant['cultivar group'] = '';
	

	/* Edible use - PFAF doesn't have a part for edible uses, so we assign it to an unknown part */
	//pfaf edible uses mixes up parts and uses, so we have to separate into parts with unknown uses and unknown uses for parts
	$edible_parts = array( 'Flowers','Fruit','Inner bark','Leaves','Manna','Pollen','Root','Sap','Seed','Seedpod','Stem');
	$plant['edible part and use'] = '';
	foreach($edible as $e){
		$use = trim($e['use']);
		if(in_array($use, $edible_parts)){
			$plant['edible part and use'] .='{{Has part with edible use
|part used='.$use.'
|part used for=Unknown use}}';
		}else{
			$plant['edible part and use'] .='{{Has part with edible use
|part used=Unknown part
|part used for='.$use.'}}';
		}
	}
	
	//ignore these uses, we don't want them
	$ignore_uses = array('Miscellany','Plant breeding');
	//these 'uses' are actually functions, ignore for material uses and add to functions
	$functions_as_uses = array('Green manure','Ground cover','Hedge','Pioneer','Rootstock','Shelterbelt','Soil conditioner','Soil reclamation','Soil stabilization');
	$rename_uses = array('Rust'=>'Rust treatments','Teeth'=>'Dental care','Hair'=>'Hair care','Alcohol'=>'Ethanol','Filter'=>'Water filtration');
	//create functions array, this will be added to further down
	$funcs = array();
	
	$plant['material part and use'] = '';
	foreach($material as $e){
		$use = trim($e['use']);
		if(in_array($use,$ignore_uses)){
			continue;
		}
		if(in_array($use,$functions_as_uses)){
			$funcs[] = $use=='Shelterbelt' ? 'Windbreak' : $use;
			$use=false;
			continue;
		}
		if(array_key_exists($use,$rename_uses)){
			$use = $rename_uses[$use];
		}
		if($use){
			$plant['material part and use'] .='{{Has part with material use
|part used=Unknown part
|part used for='.$use.'}}';
		}
	}
	
	$plant['medicinal part and use'] = '';
	foreach($medicinal as $e){
		$plant['medicinal part and use'] .='{{Has part with medicinal use
|part used=Unknown part
|part used for='.$e['use'].'}}';
	}
	
	/* convert flags to functions */
	if((int) $s['nitrogen_fixer'] && !in_array('Nitrogen fixer',$funcs))
		$funcs[] = 'Nitrogen fixer';
	if(isset($s['ground_cover']) && (int) $s['ground_cover'] && !in_array('Ground cover',$funcs))
		$funcs[] = 'Ground cover';
	if(isset($s['hedge']) && (int) $s['hedge'] && !in_array('Hedge',$funcs))
		$funcs[] = 'Hedge';
	$plant['functions as'] = implode(',',$funcs);
	
	$plant['forage for'] = ''; //no data on this
	
	/* convert shade property into sun preference & shade tolerance */
	$sun = '';
	$shade = '';
	switch($s['shade']){
		case 'F':   $sun=''; 			$shade='permanent shade'; 	break;
		case 'S':   $sun=''; 			$shade='partial shade'; 	break;
		case 'N':   $sun='full sun'; 	$shade='no shade';			break;
		case 'FS':  $sun='partial sun'; $shade='permanent shade';	break;
		case 'SN':  $sun='full sun'; 	$shade='light shade';		break;
		case 'FSN': $sun='full sun'; 	$shade='permanent shade';	break;
		default:    $sun=''; 			$shade='';					break;
	}
	$plant['sun'] = $sun;
	$plant['shade'] = $shade;
	
	/* Hardiness exists, but no heat zone :( */
	$plant['hardiness zone'] = $s['hardyness'];
	$plant['heat zone'] = '';
	
	/* convert water from D/M/We/Wa to low, moderate, high, aquatic */
	$conv = array('D'=>'low','M'=>'moderate','We'=>'high','Wa'=>'aquatic');
	$water = null;
	$w = 0;
	foreach($conv as $k => $v){
		if(strstr($s['moisture'], $k)){
			if($water===null || $w > $water){
				$water=$w;
			}
		}
		$w++;
	}
	$waterkeys = array_keys($conv);
	$plant['water'] = $conv[$waterkeys[$water]];
	
	/* convert drought from boolean to tolerant/intolerant (practicalplants also has 'dependent', but this data isn't present in pfaf) */
	if($s['drought']==1){
		$drought = 'tolerant';
	}else if($s['drought']==0){
		$drought = 'intolerant';
	}
	$plant['drought'] = isset($drought) ? $drought : '';
	
	$plant['salinity'] = ((int) $s['saline']===1) ? 'tolerant' : '';
	
	$plant['soil water retention'] = ($s['well_drained']==1) ? 'well drained' : '';
	
	/*convert soil texture from L/M/H (including a combination of those, eg 'LM', to sandy, loamy, clay */
	$conv = array('L'=>'sandy','M'=>'loamy','H'=>'clay');
	$soil = array();
	foreach($conv as $k => $v){
		if(strstr($s['soil'], $k))
			$soil[] = $v;
	}
	if((int) $s['heavy_clay']===1)
		$soil[] = 'heavy clay';
	$plant['soil texture'] = implode(',', $soil);
	
	
	/*convert soil ph from A/N/B (including combinations) to acid, neutral, alkaline
	 and the boolean fields 'acid' and 'alkaline' to very acid and very alkaline */
	$conv = array('A'=>'acid','N'=>'neutral','B'=>'alkaline');
	$soil = array();
	if((int) $s['acid']===1)
		$soil[] = 'very acid';
	foreach($conv as $k => $v){
		if(strstr($s['ph'], $k))
			$soil[] = $v;
	}
	if((int) $s['alkaline']===1)
		$soil[] = 'very alkaline';
	$plant['soil ph'] = implode(',', $soil);
	
	$plant['wind'] = (strstr($s['wind'],'W') || strstr($s['wind'],'M')) ? 'Yes' : '';
	$plant['maritime'] = (strstr($s['wind'],'M')) ? 'Yes' : '';
	$plant['pollution'] = ((int) $s['pollution']===1) ? 'Yes' : ((string) $s['pollution']==='0') ? 'No' : '';
	$plant['poornutrition'] = ((int) $s['poor_soil']===1) ? 'Yes' : ((string) $s['poor_soil']==='0') ? 'No' : '';
	//echo $plant['poornutrition'].' : '.(int) $s['poor_soil']; exit;
	/*convert 'habit' field (contains Tree, Shrub, and a lot of bad data) and boolean fields: canopy, secondary, ground_cover into niches
	alas there are no fields usable for herbaceous or rhizome layers
	*/
	$niches=array();
	if(strstr($s['habit'], 'Climber')) $niches[]='Climber';
	if(isset($s['canopy']) && (int) $s['canopy']) $niches[]='Canopy';
	if(isset($s['secondary']) && (int) $s['secondary']) $niches[]='Secondary canopy';
	if(isset($s['ground_cover']) && (int) $s['ground_cover']) $niches[]='Soil surface';
	$plant['ecosystem niche'] = implode(',',$niches);
	
	$plant['native range'] = '';
	
	/* convert habit field (contains Annual, Biennial, Perennial and combinations of those) */
	$life = array();
	if(strstr($s['habit'], 'Annual')) $life[]='annual';
	if(strstr($s['habit'], 'Biennial')) $life[]='biennial';
	if(strstr($s['habit'], 'Perennial') || strstr($s['habit'], 'Tree') || strstr($s['habit'], 'Shrub')) $life[]='perennial';
	$plant['life cycle'] = implode(',',$life);
	
	$herbwood = '';
	if(strstr($s['habit'], 'Tree') || strstr($s['habit'], 'Perennial Climber') || strstr($s['habit'], 'Shrub') || strstr($s['habit'], 'Bamboo') ) 
		$herbwood='woody';
	$plant['herbaceous or woody'] = $herbwood; //Can't find a good field for this!!
	
	$plant['deciduous or evergreen'] = ($s['deciduous_or_evergreen']=='E') ? 'evergreen' : ($s['deciduous_or_evergreen']=='D') ?  'deciduous' : '';
	
	/* convert S/F/M to slow,moderate,vigorous*/
	$conv = array('S'=>'slow','M'=>'moderate','F'=>'vigorous',''=>'');
	$plant['growth rate'] = array_key_exists($s['growth_rate'],$conv) ? $conv[$s['growth_rate']] : ''; 
	
	$plant['mature measurement unit'] = 'meters';
	$plant['mature height'] = $s['height'];
	$plant['mature width'] = $s['width'];
	
	/* convert */
	$conv = array('H'=>'hermaphrodite','M'=>'monoecious','D'=>'dioecious',''=>'');
	$plant['flower type'] = array_key_exists($s['flower_type'],$conv) ? $conv[$s['flower_type']] : '';
	
	$plant['flower color'] = '';
	
	/*convert field self_fertile: 1/0/empty to self fertile, self sterile, empty (practicalplants has the property partially self fertile, which is not present in pfaf*/
	$conv = array('Y'=>'self fertile', 'N'=>'self sterile', ''=>'');
	$plant['fertility'] = array_key_exists((string) $s['self-fertile'], $conv) ? $conv[$s['self-fertile']] : '';
	
	/* the pfaf field pollinators is already a CSV so we can just escape it and use the values as they are */
	$pollinators = array();
	foreach(explode(',',$s['pollinators']) as $p){
		$pollinators[] = ucfirst(trim($p));
	}
	$plant['pollinators'] = implode(',',$pollinators);
	
	
	//add data references
	$botrefs = array();
	foreach($botref_ids as $k => $v){
		if(in_array($id,$species_ref_ids)){
			$botrefs[] = 'PFAFimport-'.$v;
		}
	}
	$plant['botanical references'] = implode(',',$botrefs);	
	
	//medicinal, edible_uses, uses_notes
	$use_refs = array(
		'edible'=>findRefs($s['edible_uses']),
	);
	
	//array_walk($array, $funcname[, $userdata])
	$plant['edible uses references'] = implode(',',array_map('createRefNameFromId',findRefs($s['edible_uses'],true)));
	$plant['medicinal uses references'] = implode(',',array_map('createRefNameFromId',findRefs($s['medicinal'],true)));
	$plant['material uses references'] = implode(',',array_map('createRefNameFromId',findRefs($s['uses_notes'],true)));

	
	foreach($plant as $k=>$v){
		$row['Plant['.$k.']'] = $v;
	}
	
	
		
		/* Add textual references */
	foreach($pfaf_refs as $ref){
		$type = !empty($ref['url']) ? 'website' : 'book';
		$id = ($type=='book') ? $ref['isbn'] : $ref['url'];
		$date = empty($ref['date_of_publication']) ? '' : $ref['date_of_publication'].'-00-00';
		//some eejits put dashes instead of leaving the field blank
		if($id==='-'){
			$id='';
		}else if(!empty($id) && $type=='book'){
			$id = 'ISBN '.$id;
		}
		$references[] = '{{Reference|name=PFAFimport-'.$ref['id'].'
|type='.$type.'
|author='.$ref['author'].'
|title='.$ref['title'].'
|publisher='.$ref['publisher'].'
|id='.$id.'
|date='.$date
.'}}';
	}
	
	
	
	
	$state = array();
	//$state['unvalidated import'] = 'Yes';
	$state['article incomplete'] = 'Yes';
	
	foreach($state as $k=>$v){
		$row['Article state['.$k.']'] = $v;
	}
		
	$row['Free Text'] = '';
		
	$row['References[refs]'] = implode('',$references);
		
	//$rows[] = $row;//implode(',	',$row);
	//echo implode(',',$row)."\n";
	
	foreach($row as &$col){
		$col = '"'.str_replace('"','\"',$col).'"';
		
	}
	
	if($file){
		if($i % $per_block===0){
			@fclose($file);
			$letter = strtoupper( substr($s['latin_name'],0,1) );
			$file=fopen($dir.$filebase.'-'.$i.$ext,'w'); 
		}
		
		if($i % $per_block===0){
			$cols = array_keys($row);
			foreach($cols as &$c){
				$c = '"'.$c.'"';
			}
			fputs($file,implode(',',array_keys($row)));
		}
		
		fputs($file,"\n".implode(',',$row));
		
	}else{
		if($i===0)
			echo implode(',',array_keys($row));
		echo "\n".implode(',',$row);
	}
	$i++;
	?> <?php echo $i; ?>, <?php
	unset($row);
}

	
	/*foreach($row as $i => $line){
		echo $line."\n";
	}*/

//echo implode("/n",$rows);

?>