<?php

$_start = microtime(1);
define('REQUEST_TIME', time());

header('Content-type: text/html; charset=utf-8');

require 'inc.args.php';

// prerequisites
require '../inc/db/db_sqlite.php';

// db connection
$db = db_sqlite::open(array('database' => './blogs.sqlite3'));

// db schema
$schema = require 'inc.db-schema.php';
require 'inc.ensure-db-schema.php';

// class
function blogs_feed_class_autoloader($class) {
	require dirname(__FILE__) . '/inc.' . strtolower($class) . '.php';
}
spl_autoload_register('blogs_feed_class_autoloader');

// template stuff
require 'inc.tpl.php';


