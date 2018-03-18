<?php

require __DIR__ . '/inc.bootstrap.php';

if ( !isset($_GET['callback']) ) {
	header('Content-type: text/plain');
	exit('Need ?callback');
}

header('Content-type: text/javascript');

$exit = function( $result ) {
	exit($_GET['callback'] . "('" . $result . "');");
};

if ( !isset($_GET['feed']) ) {
	$exit('need feed');
}

if ( !User::check('logged in', false) ) {
	$exit('not logged in');
}

$params = [ (string)$_GET['feed'], $user->id ];
$blog = $db->select('blogs', 'feed = ? AND (private = 0 OR added_by_user_id = ?)', $params, 'Blog')->first();

if ( !$blog ) {
	$exit('does not exist');
}

$subscribed = $db->count('subscriptions', [
	'user_id' => $user->id,
	'blog_id' => $blog->id,
]);

if ( !$subscribed ) {
	$exit('not subscribed');
}

$exit('subscribed');
