<?php
ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL ^ E_WARNING);
set_time_limit(0);

$time_start = microtime(true);
$ctr = 1;
function getDirectoryListing($result, $path, $cat){
	$path .= " > ".$cat;
	$cat = str_replace(" ", "_", $cat);
	$ch = curl_init();
	$buffer = fopen("buffer.html", "w");
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2");
	curl_setopt($ch, CURLOPT_URL, "http://ru.wikipedia.org/wiki/Категория:".$cat);
	curl_setopt($ch, CURLOPT_FILE, $buffer);
	curl_exec($ch);
	curl_close ($ch);
	fclose($buffer);

	// working with the pre-fetched page using DOMDocument
	// due to a bug (?) w/ fetching Cyrillic URLs via DOMDocument->loadHTMLFile
	$html = new DOMDocument();
	$html->loadHTMLFile("buffer.html");

	$subCats = $html->getElementById("mw-subcategories");

	if($subCats){
		foreach ($subCats->getElementsByTagName("a") as $s){
			getDirectoryListing($result, $path, $s->textContent);
		}
		unset($subCats);
	}
	$pages = $html->getElementById("mw-pages");
	if($pages) {
		foreach ($pages->getElementsByTagName("a") as $a){
			fwrite($result, "$path > $a->textContent".PHP_EOL);
		}
		unset($pages);
	}
}
$result = fopen("result.txt", "w");
getDirectoryListing($result, null, "Дискретная математика");
fclose($result);

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "<br>Finished in $time seconds";
?>