<?php

require __DIR__ . '/inc.bootstrap.php';

User::check(['logged in', 'add feed']);
$admin = User::access('admin feeds');

$feedUrl = '';
$feedExists = false;

if ( isset($_POST['url']) ) {
	$feedUrl = $_POST['url'];

	// Parse feed
	$feed = RSSReader::parse($feedUrl, $error);

	// Show feed to user
	if ( empty($_POST['confirm']) ) {
		echo '<p>This is what I got:</p>';
		if ( $feed ) {
			echo '<pre>' . h(print_r($feed, 1)) . '</pre>';
		}
		else {
			var_dump($feed, $error);
		}
	}

	// May exist already
	$blog = $db->select('blogs', array('feed' => $feedUrl), null, array('first' => true, 'class' => 'Blog'));
	if ( !$blog ) {
		if ( !empty($_POST['confirm']) ) {
			// save into db
			$data = array(
				'name' => '',
				'title' => $feed['title'],
				'url' => $feed['url'],
				'updated' => 0,
				'checked' => 0,
				'feed' => $feed['feed'],
				'added_by_user_id' => USER_ID,
				'private' => (int)!empty($_POST['private']),
			);
			if ( $admin && !empty($_POST['name']) ) {
				$data['name'] = $_POST['name'];
			}
			$db->insert('blogs', $data);
			$id = $db->insert_id();

			if ( !empty($data['name']) ) {
				$db->insert('subscriptions', array(
					'user_id' => $user->id,
					'blog_id' => $id,
				));
			}

			User::success('Blog added: ' . h($data['title']));

			redirect('index.php?blog=' . $id);
		}

		echo '<h1>Looks good? Resubmit!</h1>';
	}
	else {
		$feedExists = true;
		echo '<p>I already have this one. You can use this page to test the feed reader.</p>';
	}
}

require 'tpl.menu.php';

?>
<h1>
	Add feed
</h1>

<form method="post" action autocomplete="off">
	<p>Feed URL: <input type="url" name="url" value="<?=h($feedUrl ?: @$_GET['url'])?>" autofocus required /></p>
	<?if( $feedUrl && !$feedExists ):?>
		<?if( $admin ):?>
			<p>Machine name: <input name="name" /> (auto activate &amp; enable)</p>
		<?endif?>
		<p><label><input type="checkbox" name="confirm" checked /> Yup, that's the one. Save it!</label></p>
		<p><label><input type="checkbox" name="private" /> This is a <strong>private</strong> feed. (I won't tell.)</label></p>
	<?endif?>

	<p><input type="submit" value="<?=$feedUrl ? 'Save' : 'Preview'?>" /></p>
</form>


