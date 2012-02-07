<?php

$_start = microtime(1);

header('Content-type: text/plain');

// prerequisites
require '../inc/db/db_sqlite.php';
require '../inc/htmlextractor/HTMLExtractor.php';

// db connection
$db = db_sqlite::open(array('database' => './blogs.sqlite3'));

// db schema
$schema = require 'db-schema.php';
require 'ensure-db-schema.php';

$feeds = require 'feeds-schema.php';

foreach ( $feeds AS $blog_name => $blog_info ) {
	if ( isset($_GET['test']) && $_GET['test'] != $blog_name ) {
		continue;
	}

	$steps = array($blog_info['extraction']);

	$html = file_get_contents($blog_info['url']);

	$extractor = new HTMLExtractionProcess($html, $steps);
	$extractor->start();
print_r($extractor->output);

exit;
}


