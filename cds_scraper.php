<?php

header('Content-Type: text/html; charset=utf-8');
     
require 'vendor/autoload.php';

use Asymptix\HtmlDomParser\HtmlDomParser;
use League\Csv\Writer;

# Store url
$url = "http://centrodosuplemento.com.br/suplementos";

$html = new HtmlDomParser();
$html->loadUrl($url);

$page_numbers = [];
foreach ($html->find('.toolbar-bottom .pager .pages ol li') as $page) {
	$page_numbers[] = $page->plaintext;
}

$max_page = max($page_numbers);

# Initialize Arrays
$name = [];
$price = [];

for($i = 1; $i <= $max_page; $i++){
	# Open search results page
	$url = "http://centrodosuplemento.com.br/suplementos?mode=list?p=$i";
	
	$product_html = new HtmlDomParser();
	$product_html->loadUrl($url);

	# Store data in Arrays
	foreach ($product_html->find('.product-name a') as $line) {
		$name[] =  $line->plaintext;
	}

	foreach ($product_html->find('.price-box .regular-price .price') as $line) {
		$price[] =  $line->plaintext;
	}
}

$writer = Writer::createFromPath('cds_list.csv', 'w');
$writer->insertOne(["Listing Name", "Price"]);

for($p = 0; $p < count($name); $p++){
	$writer->insertOne([$name[$p], $price[$p]]);
}