<?php

require 'inc.config.php';

set_time_limit(0);

$feeds = require 'rss-feeds.php';

// debug //
$debug = isset($_GET['test']);
if ( $debug ) {
	$db->begin();
}
// debug //

$blogs = array();

// save all new posts from all feeds
foreach ( $feeds AS $blogName => $feedUrl ) {
	// debug
	if ( !$feedUrl || ( isset($_GET['test']) && $_GET['test'] != $blogName ) ) {
		continue;
	}

	// get feed
	$xml = @simplexml_load_file($feedUrl);
	if ( !$xml ) {
		continue;
	}
//var_dump($blogName);

	// blog info
	$blogTitle = ternary(
		(string)$xml->channel->title,
		(string)$xml->title
	);
	$blogUrl = ternary(
		(string)$xml->channel->link,
		(string)$xml->link[0]['href']
	);

	// save blog
	$blog = $db->select('blogs', array('name' => $blogName), null, true);
	if ( !$blog ) {
		$data = array(
			'name' => $blogName,
			'title' => $blogTitle,
			'url' => $blogUrl,
			'updated' => REQUEST_TIME,
		);
		$db->insert('blogs', $data);
		$blog = $db->select('blogs', array('name' => $blogName), null, true);
	}
	$blogs[$blog->id] = $blog;
//print_r($blog);

	$update = array('checked' => REQUEST_TIME);

	// cycle posts
	$posts = array();
	if ( $xml->channel && $xml->channel->item ) {
		$posts = $xml->channel->item;
	}
	else {
		$posts = $xml->entry;
	}

	foreach ( $posts AS $blogPost ) {
		// post info
		$postGuid = ternary(
			(string)$blogPost->guid,
			(string)$blogPost->id,
			(string)$blogPost->link
		);
		$postTitle = (string)$blogPost->title;
		$postUrl = ternary(
			(string)$blogPost->link,
			(string)$blogPost->link[0]['href']
		);

		// new post?
		$post = $db->select('blog_posts', array('guid' => $postGuid), null, true);
		if ( $post ) {
			// old news -- next blog
			$db->update('blogs', $update, array('id' => $blog->id));
			continue 2;
		}

		// save post
		$data = array(
			'blog_id' => $blog->id,
			'guid' => $postGuid,
			'title' => $postTitle,
			'url' => $postUrl,
			'added' => REQUEST_TIME,
		);
		$db->insert('blog_posts', $data);

		// update blog
		$update['updated'] = REQUEST_TIME;
	}

	// save blog update
//print_r($update);
	$db->update('blogs', $update, array('id' => $blog->id));
}


// get new posts
$newPosts = $db->select('blog_posts', 'new = 1 ORDER BY blog_id, id ASC');

$html = '';
$lastBlog = 0;
foreach ( $newPosts AS $post ) {
	$blog = $blogs[$post->blog_id];
	if ( $lastBlog != $post->blog_id ) {
		$html .= "\n" . '<h2><a href="' . $blog->url . '">' . $blog->title . '</a></h2>' . "\n\n";
		$lastBlog = $post->blog_id;
	}

	$html .= '<h3>- <a href="' . $post->url . '">' . $post->title . '</a></h3>' . "\n\n";
}

echo $html;


// send e-mail
if ( !$debug ) {
	if ( $html ) {
		$headers = array(
			'From: Blogs feed <blogsfeed@hoblox.nl>',
			'Content-type: text/html; charset=utf-8',
		);
		mail('blogs@hotblocks.nl', 'New blog posts from feed', $html, implode("\r\n", $headers) . "\r\n");
	}
}


// update index
$db->update('blog_posts', 'new = 0', '1');



echo "\n" . number_format(microtime(1) - $_start, 4) . "\n";



function ternary($a, $b) {
	$args = func_get_args();
	foreach ( $args AS $arg ) {
		if ( $arg ) {
			return $arg;
		}
	}
}


