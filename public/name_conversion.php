<?php
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
$species = $db->query('SELECT latin_name, habit FROM species ');


?>
<table width=50%>
<tr><th width="200">Name before</th><th>Name after</th>
<?php

function formatName($name){
	global $db;
	$name = trim($name);
	$name = str_replace('  ',' ',$name);
	$parts = explode(' ',$name);
	/* Match:
	Genus
	Genus species 
	x SpeciesSpecies */
	if(count($parts)==1){
		return '<span style="color:blue">'.$parts[0].'</i></span>';
	}
	if(count($parts) < 3){
		if($parts[1]=='species')
			return '<span style="color:blue">'.$parts[0].'</i></span>';
		return false;//$name;
	}
	/* Match
	Genus x Species*/
	if($parts[0]=='x' || $parts[1]=='x')
		return $name;
	
	if(count($parts)==3){
		//is it a cultivar group?
		$cultivars = $db->query('SELECT * FROM cultivar WHERE latin_name="'.$name.'"');
		if($cultivars->num_rows>0){
			return '<span style="color:green">'.$parts[0].' '.$parts[1].' ('.ucfirst($parts[2]).' Group)</span>';
		}
		return '<span style="color:Red">'.$parts[0].' '.$parts[1].' <i>'.ucfirst($parts[2]).'</i></span>';
	}
	if(count($parts)==4){
		return $parts[0].' '.$parts[1];
	}
	if(count($parts)>4){
		return 'Serious wtf?';
	}	
	//how do we decide between cultivar group and variety??
	
	
}

while($s = $species->fetch_assoc() ){
	$name = formatName($s['latin_name']);
	if($name): ?><tr><td><?php  echo $s['latin_name'] ?></td><td><?php echo $s['habit'] ?></td><td><?php echo $name ?></td>></tr><?php endif;
	
	
}
?></table><?php
?>