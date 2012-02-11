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
			<li><?=l($blog->title, $blog->url, array('title' => 'Go to blog'))?> <?if( $blog->added_by_user_id ):?>&nbsp; (added by <?=l($blog->display_name, 'profile/' . $blog->added_by_user_id)?>)<?endif?> &nbsp; (<?=l('rss', $blog->feed, array('title' => 'Go to feed'))?>)</li>
		<?endforeach?>
	</ul>
</div>

<pre><? print_r($_SESSION) ?></pre>


