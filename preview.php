<?php

require __DIR__ . '/inc.bootstrap.php';

$blog = Blog::one(@$_GET['blog']);
if ( !$blog ) {
	exit("No blog found.\n");
}

$_time = microtime(1);

$feedUrl = $blog->feed;
$error = null;
$feed = RSSReader::parse($feedUrl, $error);

$_download = microtime(1) - $_time;

echo '<p><code>' . round($_download * 1000) . ' ms</code> to download &amp parse.</p>';

$_time = microtime(1);

if ( $feed === false ) {
	$html = '<pre>' . h(print_r($error, 1)) . '</pre>';
}
else {
	$posts = array_map(function($post) {
		return (object) $post;
	}, $feed['posts']);

	$html = call_user_func(function() use ($blog, $posts) {
		ob_start();
		include 'tpl.new-posts.php';
		return ob_get_clean();
	});
}

echo $html;

$_render = microtime(1) - $_time;

echo '<p><code>' . round($_render * 1000) . ' ms</code> to render &amp; print.</p>';

echo "\n\n\n<hr>\n\n\n";

echo '<pre>' . h(print_r($blog, 1)) . '</pre>';

echo "\n\n\n<hr>\n\n\n";

echo '<pre>' . h(print_r($feed, 1)) . '</pre>';
