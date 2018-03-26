<?php

require __DIR__ . '/inc.bootstrap.php';

User::check('logged in');

require 'tpl.menu.php';

if ( !($id = $_GET['id']) ) {
	exit('Need id');
}

$posts = Post::query("
	SELECT p.*
	FROM blog_posts p
	JOIN blogs b ON b.id = p.blog_id
	WHERE p.sent = ? AND b.enabled = 1 AND (b.private <> 0 OR added_by_user_id = ?)
	ORDER BY b.title
", [$id, $user->id]);

$groupedPosts = array_reduce($posts, function(array $list, Post $post) {
	$list[$post->blog_id][] = $post;
	return $list;
}, []);

?>
<h1>Posts (<?= count($posts) ?>)</h1>

<? foreach ($groupedPosts as $posts): ?>
	<?= $posts[0]->blog->renderPosts($posts) ?>
<? endforeach ?>
