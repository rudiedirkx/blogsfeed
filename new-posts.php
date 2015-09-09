<?php

require 'inc.config.php';

$blogs = Blog::allForCronjob();

// get new posts
$newPosts = $db->select('blog_posts', 'new = 1 ORDER BY blog_id, id ASC');

// group by blog
$newPostsByBlog = array();
foreach ( $newPosts as $post ) {
	$newPostsByBlog[$post->blog_id][] = $post;
}

// create HTML per blog
$postHtmls = array();
foreach ( $newPostsByBlog AS $blogId => $posts ) {
	$blog = $blogs[$blogId];

	$html = call_user_func(function() use ($blog, $posts) {
		ob_start();
		include 'tpl.new-posts.php';
		return ob_get_clean();
	});

	echo $html;
}
