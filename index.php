<?php

require 'inc.config.php';

user::check('logged in');

require 'inc.menu.php';

$blogs = Blog::allWithCreator();

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
			</li>
		<?endforeach?>
	</ul>
</div>


