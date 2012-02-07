<?php

$_start = microtime(1);
define('REQUEST_TIME', time());

header('Content-type: text/html; charset=utf-8');

// prerequisites
require '../inc/db/db_sqlite.php';

// db connection
$db = db_sqlite::open(array('database' => './blogs.sqlite3'));

// db schema
$schema = require 'inc.db-schema.php';
require 'inc.ensure-db-schema.php';

// models
require 'inc.model.php';
require 'inc.blog.php';

// session stuff
require 'inc.user.php';

// template stuff
require 'inc.tpl.php';


