<?php

require 'env.php';

$_start = microtime(1);
define('REQUEST_TIME', time());

header('Content-type: text/html; charset=utf-8');

// prerequisites
require WHERE_DB_GENERIC_AT . '/db_sqlite.php';

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
spl_autoload_register(function($class) {
	require __DIR__ . '/inc.' . strtolower($class) . '.php';
});

// template stuff
require 'inc.tpl.php';


