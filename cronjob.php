<?php

require __DIR__ . '/inc.config.php';

header('Content-type: text/plain; charset=utf-8');
set_time_limit(0);
$keepPostsPerBlog = 5;




// PART 0 -- debug environment //
$db->begin();

// debug //
$debug = false;
if ( isset($_GET['debug']) ) {
	$debug = explode(',', $_GET['debug']);
}
// debug //




// PART 1 -- parse feeds for all blogs //

$blogs = Blog::allForCronjob();

foreach ( $blogs AS $blog ) {
	if ( $debug && !in_array($blog->name, $debug) ) {
		continue;
	}

	$feedUrl = $blog->feed;
	$feed = RSSReader::parse($feedUrl);

	$update = array('checked' => REQUEST_TIME);

echo '&blog->id: ' . $blog->id . "\n";
	if ( $feed['posts'] ) {
		foreach ( $feed['posts'] AS $feedPost ) {
			// new post?
			$post = $db->select('blog_posts', array('guid' => $feedPost['guid']), null, true);
			if ( $post ) {
				// old news -- next blog
				$db->update('blogs', $update, array('id' => $blog->id));
				continue 2;
			}

			// save post
			$data = array(
				'blog_id' => $blog->id,
				'guid' => $feedPost['guid'],
				'title' => $feedPost['title'],
				'url' => $feedPost['url'],
				'added' => REQUEST_TIME,
			);
			$db->insert('blog_posts', $data);

			// update blog
			$update['updated'] = REQUEST_TIME;

		} // foreach posts
	}

	// save blog update
	$db->update('blogs', $update, array('id' => $blog->id));

} // foreach blogs




// PART 2 -- create HTML from new posts //

// get new posts
$newPosts = $db->select('blog_posts', 'new = 1 ORDER BY blog_id, id ASC');

// create HTML per blog
$postHtmls = array();
$lastBlog = 0;
foreach ( $newPosts AS $post ) {
	$blog = $blogs[$post->blog_id];
	isset($postHtmls[$blog->id]) or $postHtmls[$blog->id] = array('number' => 0, 'html' => '');
	$info = &$postHtmls[$blog->id];

	if ( $lastBlog != $post->blog_id ) {
		$lastBlog = $post->blog_id;
		$info['html'] .= "\n\n" . '<h2><a href="' . $blog->url . '">' . $blog->title . '</a></h2>' . "\n\n\n";
	}

	$info['html'] .= '<h3>- <a href="' . $post->url . '">' . $post->title . '</a></h3>' . "\n\n";
	$info['number']++;

	unset($info);
}

// cronjob feedback
echo '&postHtmls:' . "\n";
print_r($postHtmls);
echo "\n\n";




// PART 3 -- send per-user e-mail //

// fetch subscriptions
$subscriptions = $db->fetch('SELECT u.id user_id, u.email, u.display_name, s.blog_id FROM subscriptions s, users u WHERE s.user_id = u.id AND u.enabled <> 0 AND s.blog_id IN (?) ORDER BY u.id', array(array_keys($postHtmls)));

// combine subscriptions, users and blog posts
$userHtmls = array();
$lastUser = 0;
foreach ( $subscriptions AS $sub ) {
	if ( $lastUser != $sub->user_id ) {
		$lastUser = $sub->user_id;
		$userHtmls[$lastUser] = array(
			'email' => $sub->display_name . ' <' . $sub->email . '>',
			'html' => '',
			'posts' => 0,
		);
	}

	$info = &$userHtmls[$lastUser];

	if ( isset($postHtmls[$sub->blog_id]) ) {
		$info['html'] .= $postHtmls[$sub->blog_id]['html'];
		$info['posts'] += $postHtmls[$sub->blog_id]['number'];
	}

	unset($info);
}

// cronjob feedback
echo '&userHtmls:' . "\n";
print_r($userHtmls);
echo "\n\n";

// send e-mail
foreach ( $userHtmls AS $userId => $info ) {
	$html = trim($info['html']);

	if ( $html ) {
		$recipient = $info['email'];
		$subject = 'New blog posts from feed (' . $info['posts'] . ')';

		$headers = array(
			'From: Blogs feed <blogsfeed@hoblox.nl>',
			'Content-type: text/html; charset=utf-8',
		);
		if ( !$debug ) {
			var_dump(mail($recipient, $subject, $html, implode("\r\n", $headers) . "\r\n"));
		}
	}
}




// PART 4 -- clean up //

// update index
$db->update('blog_posts', 'new = 0', '1');

// delete old blog posts
/*try {
	$postsPerBlog = $db->fetch_fields('select blog_id, group_concat(id) post_ids from blog_posts group by blog_id');
	foreach ( $postsPerBlog AS $blogId => $postIds ) {
		$postIds = array_map('intval', explode(',', $postIds));
		rsort($postIds, SORT_NUMERIC);

		if ( $boundary = array_slice($postIds, $keepPostsPerBlog-1, 1) ) {
			$boundary = $boundary[0];

			// delete from this blog what's older than $boundary
			$db->delete('blog_posts', 'blog_id = ? AND id < ?', array($blogId, $boundary));
		}
	}
}
catch ( db_exception $ex ) {
	echo "\n\nEXCEPTION: " . $ex->getMessage() . "\n\n\n";
}*/




// PART 5 -- save //

if ( !$debug ) {
	$db->commit();
}




echo "\n\n\n\n\n" . number_format(microtime(1) - $_start, 2) . "\n\n";


