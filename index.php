<?php

require 'inc.config.php';

user::check('logged in');

$blogs = $db->select('blogs', '1', null, 'Blog');

?>
<h1>Available blogs</h1>

<ul>
	<?foreach( $blogs AS $blog ):?>
		<li><?=l($blog->title, $blog->url, array('title' => 'Go to blog'))?> &nbsp; <?=l('>>', $blog->feed, array('title' => 'Go to feed'))?></li>
	<?endforeach?>
<ul>


