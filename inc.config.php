<?php

chdir(dirname(__FILE__));

$_start = microtime(1);
define('REQUEST_TIME', time());

header('Content-type: text/html; charset=utf-8');

// prerequisites
require '../inc/db/db_sqlite.php';

// db connection
$db = db_sqlite::open(array('database' => dirname(__FILE__) . '/db/blogs.sqlite3'));
if ( !$db ) {
	exit('No database connecto...');
}

require 'inc.args.php';

// db schema
$schema = require 'inc.db-schema.php';
$db->schema($schema);

// class
function blogs_feed_class_autoloader($class) {
	require dirname(__FILE__) . '/inc.' . strtolower($class) . '.php';
}
spl_autoload_register('blogs_feed_class_autoloader');

// template stuff
require 'inc.tpl.php';


