<?php

require __DIR__ . '/inc.bootstrap.php';

header('Content-type: text/plain; charset=utf-8');
set_time_limit(0);




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
// print_r($blogs);

$fails = array();
foreach ( $blogs AS $blog ) {
echo $blog->name . " (" . $blog->id . ")\n";
	if ( $debug && !in_array($blog->name, $debug) ) {
echo "- skip\n\n\n";
		continue;
	}

	$feedUrl = $blog->feed;
	$error = null;
	$feed = RSSReader::parse($feedUrl, $error);

	$update = array('checked' => REQUEST_TIME);

	if ( $feed && !$error ) {
		$update['fails'] = $blog->fails = 0;

		$new = 0;
		foreach ( array_slice($feed['posts'], 0, BLOGSFEED_KEEP_BLOGS) AS $feedPost ) {
			// new post?
			$post = $db->select('blog_posts', array('guid' => $feedPost['guid']), null, true);
			if ( $post ) {
				// old news -- next blog
				$db->update('blogs', $update, array('id' => $blog->id));
				break;
			}

echo "- new: " . $feedPost['title'] . "\n";

			// save post
			$data = array(
				'blog_id' => $blog->id,
				'guid' => $feedPost['guid'],
				'title' => $feedPost['title'],
				'url' => $feedPost['url'],
				'image' => $feedPost['image'],
				'pubdate' => $feedPost['pubdate'],
				'added' => REQUEST_TIME,
			);
			$db->insert('blog_posts', $data);
			$new++;

			// update blog
			$update['updated'] = REQUEST_TIME;

		} // foreach posts

		if ( $new ) {
echo "- " . $new . " new posts\n";
		}
		else {
echo "- old news\n";
		}
	}
	else {
echo "- read fail!\n";
		$blog->fails++;
		$update['fails'] = $blog->fails;
		$fails[] = compact('blog', 'error');
	}

	// save blog update
	$db->update('blogs', $update, array('id' => $blog->id));

	echo "\n\n";

} // foreach blogs



// PART 2 -- create HTML from new posts //

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

	$postHtmls[$blogId] = array(
		'number' => count($posts),
		'html' => $html,
	);
}

// cronjob feedback
// echo '&postHtmls:' . "\n";
// print_r($postHtmls);
// echo "\n\n";




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
			'email' => $sub->display_name . ' <' . ($sub->send_to_email ?: $sub->email) . '>',
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
// echo '&userHtmls:' . "\n";
// print_r($userHtmls);
// echo "\n\n";

// send e-mail
foreach ( $userHtmls AS $userId => $info ) {
	$html = trim($info['html']);
	$subject = 'New blog posts from feed (' . $info['posts'] . ')';
	$recipient = $info['email'];
	echo "$recipient - $subject\n";

	if ( $html ) {
		$headers = array(
			'From: Blogs feed <blogsfeed@hoblox.nl>',
			'Content-type: text/html; charset=utf-8',
		);
		if ( !$debug ) {
			echo "sent: ";
			var_dump(mail($recipient, $subject, $html, implode("\r\n", $headers) . "\r\n"));
		}
	}
}



// PART 4 -- clean up //

// update index
$time = REQUEST_TIME;
$db->update('blog_posts', "new = 0, sent = $time", 'new = 1');
echo "update new=0, sent=$time: ";
var_dump($db->affected_rows());

// delete old blog posts
require 'inc.delete-old-posts.php';



// PART 5 -- save //

if ( !$debug ) {
	$db->commit();

	$db->execute('VACUUM');
}




echo "\n\n\n\n\n" . number_format(microtime(1) - $_start, 2) . "\n\n";


