<?php

require 'inc.config.php';

//user::check('logged in');

$admin = user::access('admin feeds');

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

			$blogs = Blog::allWithCreator();
		}
	}
}

require 'inc.menu.php';

?>
<h1>
	Available blogs
</h1>

<div class="all-blogs">
	<ul>
		<?foreach( $blogs AS $blog ):?>
			<li class="<?=$blog->enabled || $blog->private ? 'enabled' : 'disabled'?> <?=$blog->private ? 'private' : 'public'?>">
				<?if( $blog->private ):?>
					<span class="scope">PRIVATE:</span>
				<?endif?>
				<?=l($blog->title, $blog->url, array('class' => 'blog', 'title' => $blog->enabled ? 'This blog has been approved and you can subscribe to its feed.' : "This feed hasn't been approved yet. You can't subscribe to it."))?>
				<?if( $blog->added_by_user_id ):?>
					&nbsp;
					(added by <?=l($blog->display_name, 'profile.php?args=' . $blog->added_by_user_id)?>)
				<?endif?>
				&nbsp;
				(<?=l('rss', $blog->feed, array('title' => 'Go to feed'))?>)
				<?if( !$blog->private && !$blog->enabled && $admin ):?>
					&nbsp;
					(enable: <?=l('click', 'index.php?enable=' . $blog->id . '&name=', array('class' => 'enable-feed'))?>)
				<?endif?>
			</li>
		<?endforeach?>
	</ul>
</div>

<?if( !user::logincheck() ):?>
	<p>If these aren't enough, you can add your own. <?=l('Sign up', 'signup')?>.</p>
<?endif?>

<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script src="<?=baseUrl()?>app.js"></script>


