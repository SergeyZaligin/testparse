<?php
/**
 * I. STAP load all countries in file (href and title)
 */
include_once('lib/Curl.php');
include_once('lib/simple_html_dom.php');


// Load page with wiki.conf
$c = Curl::app('https://person-agency.ru')->config_load('wiki.cnf');
					
// Get array data on data['headers'] and data['html']
$data = $c->request('cities/sankt-peterburg.html');

// Build DOM
$dom = str_get_html($data['html']);

// Find parser items $flags - array objects    
$flags = $dom->find('.title');

// Count items
$done = 0;

// Array countries
$countries = [];

// Iteration $flags
foreach ($flags as $span) {
    
    $countries[$span->href] = $span->plaintext;  
    
    $done++;       
}
    
echo '<pre>';
print_r($countries);
echo '</pre>';

// PHP in JSON
file_put_contents('res/all', json_encode($countries));

echo "<br>done: $done<br>";