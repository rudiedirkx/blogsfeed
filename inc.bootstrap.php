<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/env.php';

$_start = microtime(1);
define('REQUEST_TIME', time());

header('Content-type: text/html; charset=utf-8');

define('BLOGSFEED_KEEP_BLOGS', 50);

// db connection
$db = db_sqlite::open(array('database' => __DIR__ . '/db/blogs.sqlite3'));
if ( !$db ) {
	exit('No database connecto...');
}

// db schema
$db->ensureSchema(require 'inc.db-schema.php', function(array $changes) use ($db) {
	if ( isset($changes['columns']['blog_posts']['sent']) ) {
		$db->update('blog_posts', ['sent' => 1], '1');
		$db->affected_rows();
	}
});
