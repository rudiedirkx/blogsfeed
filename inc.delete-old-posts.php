<?php

$keepPostsPerBlog = 25;

try {
	$postsPerBlog = $db->fetch_fields('select blog_id, group_concat(id) post_ids from blog_posts group by blog_id');
	foreach ( $postsPerBlog AS $blogId => $postIds ) {
		$postIds = array_map('intval', explode(',', $postIds));
		rsort($postIds, SORT_NUMERIC);

		if ( $boundary = array_slice($postIds, $keepPostsPerBlog-1, 1) ) {
			$boundary = $boundary[0];

			// delete from this blog what's older than $boundary
			$db->delete('blog_posts', 'blog_id = ? AND id < ?', array($blogId, $boundary));
			var_dump($db->affected_rows());
		}
	}
}
catch ( db_exception $ex ) {
	echo "\n\nEXCEPTION: " . $ex->getMessage() . "\n\n\n";
}
