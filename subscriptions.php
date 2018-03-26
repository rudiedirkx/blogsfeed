<?php

require __DIR__ . '/inc.bootstrap.php';

User::check('logged in');

require 'inc.account.php';

$blogs = Blog::allForUser($account);

$subscriptions = $db->select_fields('subscriptions', 'blog_id', array('user_id' => $account->id));

// SAVE
if ( isset($_POST['feeds']) ) {
	// delete old subscriptions
	$db->delete('subscriptions', array('user_id' => $account->id));

	// add new subscriptions
	$db->begin();
	foreach ( $_POST['feeds'] AS $blogId ) {
		if ( isset($blogs[$blogId]) ) {
			$db->insert('subscriptions', array(
				'user_id' => $account->id,
				'blog_id' => $blogId,
			));
		}
	}
	$db->commit();

	User::success('Subscriptions saved!');

	redirect();
}

// EXPORT
else if ( isset($_GET['export']) ) {
	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename="blogs.txt"');

	$blogs = $db->fetch('SELECT b.* FROM blogs b JOIN subscriptions s ON (b.id = s.blog_id AND s.user_id = ?)', array($account->id));
	foreach ($blogs as $blog) {
		echo $blog->feed . "\n";
	}

	exit;
}

require 'tpl.menu.php';

?>
<h1>
	Subscriptions
	<?=otherAccountInfo($account)?>
</h1>

<p><a href="?export=1">Export</a></p>

<form method="post" action>
	<div class="subscriptions">
		<ul>
			<?foreach( $blogs AS $blog):?>
				<? $active = in_array($blog->id, $subscriptions); ?>
				<li class="<?=$active ? 'active' : 'inactive'?>">
					<label>
						<input type="checkbox" <?if( $blog->enabled || $active ):?>name="feeds[]" value="<?=$blog->id?>"<?else:?>disabled<?endif?> <?if( $active ):?>checked<?endif?>/>
						<span>
							<?if( $blog->private ):?>
								<span class="scope">PRIVATE:</span>
							<?endif?>
							<?=$blog->title?>
						</span>
					</label>
				</li>
			<?endforeach?>
		</ul>
	</div>

	<p><input type="submit" /></p>
</form>


