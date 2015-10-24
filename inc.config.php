<?php

require 'env.php';

$_start = microtime(1);
define('REQUEST_TIME', time());

header('Content-type: text/html; charset=utf-8');

define('BLOGSFEED_KEEP_BLOGS', 50);

// prerequisites
require WHERE_DB_GENERIC_AT . '/db_sqlite.php';

// db connection
$db = db_sqlite::open(array('database' => dirname(__FILE__) . '/db/blogs.sqlite3'));
if ( !$db ) {
	exit('No database connecto...');
}

// Screw ACID, go SPEED!
$db->execute('PRAGMA synchronous=OFF');
$db->execute('PRAGMA journal_mode=OFF');

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


