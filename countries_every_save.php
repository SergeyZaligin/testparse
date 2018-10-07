<?php
/**
 * III. STAP save for every country html page in file
 */
include_once('lib/Parser.php');
include_once('lib/simple_html_dom.php');
	
set_time_limit(0);

$countries = json_decode(file_get_contents('res/all')); 

$done = 0;   
        
foreach ($countries as $href => $name) {
    
    $country = file_get_contents('res/country_every/' . $name);
    
    $p = Parser::app($country);

    $p->moveTo('<table class="infobox');
    $p->moveTo('<b>Capital</b>');
    $p->moveTo('<a');
    $p->moveAfter('>');
    $str = $p->readTo('<');
    
    echo $str . '<br>';

    $done++;
}
    
echo "done: $done<br>";