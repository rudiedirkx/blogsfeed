<?php

require 'inc.config.php';

user::check('logged in');

$blogs = Blog::allWithCreator();

if ( isset($_GET['enable'], $_GET['name'], $blogs[$_GET['enable']]) ) {
	if ( '' != ($name = trim($_GET['name'])) ) {
		$blog = $blogs[$_GET['enable']];

		if ( !$blog->name ) {
			$db->update('blogs', array(
				'name' => $name,
			), array(
				'id' => $blog->id,
			));
		}
	}
}

require 'inc.menu.php';

$admin = user::access('admin feeds');

?>
<h1>
	All blogs
</h1>

<div class="all-blogs">
	<ul>
		<?foreach( $blogs AS $blog ):?>
			<li class="<?=$blog->enabled ? 'enabled' : 'disabled'?>">
				<?=l($blog->title, $blog->url, array('class' => 'blog', 'title' => $blog->enabled ? 'This blog has been approved and you can subscribe to its feed.' : "This feed hasn't been approved yet. You can't subscribe to it."))?>
				<?if( $blog->added_by_user_id ):?>
					&nbsp;
					(added by <?=l($blog->display_name, 'profile/' . $blog->added_by_user_id)?>)
				<?endif?>
				&nbsp;
				(<?=l('rss', $blog->feed, array('title' => 'Go to feed'))?>)
				<?if( !$blog->enabled && $admin ):?>
					&nbsp;
					(enable: <?=l('click', 'index?enable=' . $blog->id . '&name=')?>)
				<?endif?>
			</li>
		<?endforeach?>
	</ul>
</div>


