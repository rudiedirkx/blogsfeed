<?php

require 'inc.config.php';

header('Content-type: text/plain; charset=utf-8');

user::check('admin feeds');

// delete blogs
$db->delete('blogs', '1');

// delete subscriptions
$db->delete('subscriptions', '1');

// delete blog posts
$db->delete('blog_posts', '1');

// insert from hard coded feeds
foreach ( require 'inc.rss-feeds.php' AS $feedName => $feedUrl ) {
var_dump($feedName);
	$feed = RSSReader::parse($feedUrl);

	$data = array(
		'name' => $feedName,
		'title' => $feed['title'],
		'url' => $feed['url'],
		'feed' => $feed['feed'],
	);
	var_dump($db->insert('blogs', $data));
}


