<?php

require __DIR__ . '/inc.bootstrap.php';

$admin = User::access('admin feeds');

$blogs = Blog::allWithCreator();

if ( $admin && isset($_GET['activate'], $_GET['name'], $blogs[$_GET['activate']]) ) {
	$user->check('token');

	$blog = $blogs[$_GET['activate']];
	if ( !$blog->name && ($name = trim($_GET['name'])) ) {
		$db->update('blogs', array(
			'name' => $name,
		), array(
			'id' => $blog->id,
		));

		User::success('Activated blog # ' . $blog->id . '.');
		return redirect('index.php?blog=' . $blog->id);
	}
}

elseif ( $admin && isset($_POST['blogs'], $_POST['action']) ) {
	$user->check('token');

	$ids = (array)$_POST['blogs'];
	switch ($_POST['action']) {
		case 'enable':
			$db->update('blogs', array('enabled' => 1, 'fails' => 0), array('id' => $ids));
			User::success('Enabled ' . $db->affected_rows() . ' blogs.');
			break;

		case 'disable':
			$db->update('blogs', array('enabled' => 0), array('id' => $ids));
			User::success('Disabled ' . $db->affected_rows() . ' blogs.');
			break;

		case 'delete':
			$db->delete('blogs', array('id' => $ids));
			User::success('Deleted ' . $db->affected_rows() . ' blogs.');
			break;
	}

	return redirect('index.php');
}

require 'tpl.menu.php';

// print_r($blogs);

$showStatus = false;
foreach ($blogs as $blog) {
	if ($blog->private || !$blog->enabled) {
		$showStatus = true;
		break;
	}
}

$hilited = @$_GET['blog'];

?>
<style>
.all-blogs table {
	border-spacing: 3px;
	border: solid 1px #ddd;
}
.all-blogs td,
.all-blogs th {
	background-color: #eee;
	padding: 4px 8px;
}
</style>

<h1>Available blogs</h1>

<form method="post" action class="all-blogs">
	<input type="hidden" name="token" value="<?= CSRF_TOKEN ?>" />

	<table>
		<? foreach ($blogs AS $blog): ?>
			<tr class="<?= $blog->enabled && $blog->name ? 'enabled' : 'disabled' ?> <?= $blog->id == $hilited ? 'hilited' : '' ?>">
				<? if ($admin): ?>
					<td>
						<input type="checkbox" name="blogs[]" value="<?= $blog->id ?>" />
					</td>
				<? endif ?>
				<? if ($showStatus): ?>
					<td>
						<? if (!$blog->enabled): ?>
							<span class="status">DISABLED</span>
						<? endif ?>
						<? if ($blog->private): ?>
							<span class="status">PRIVATE</span>
						<? endif ?>
						<? if (!$blog->name): ?>
							<span class="status">INACTIVE</span>
						<? endif ?>
					</td>
				<? endif ?>
				<td>
					<?= l($blog->title, $blog->url, array('class' => 'blog')) ?>
					(<?= h($blog->name) ?>)
				</td>
				<td>
					<? if ($blog->added_by_user_id): ?>
						<?=l($blog->display_name, 'profile.php?args=' . $blog->added_by_user_id)?>
					<? endif ?>
				</td>
				<td>
					<?= l('rss', $blog->feed, array('title' => 'Go to feed')) ?>
				</td>
				<td>
					<? if ($admin): ?>
						<? if (!$blog->name): ?>
							<?= l('activate', 'index.php?activate=' . $blog->id . '&token=' . CSRF_TOKEN . '&name=', array('class' => 'activate-feed')) ?>
						<? elseif ($blog->fails): ?>
							<?= $blog->fails ?> fails
						<? endif ?>
					<? endif ?>
				</td>
				<td>
					<?= l('preview', 'preview.php?blog=' . $blog->id) ?>
				</td>
			</tr>
		<? endforeach ?>
	</table>

	<? if ($admin): ?>
		<p class="form-actions">
			<button name="action" value="enable">Enable</button>
			<button name="action" value="disable">Disable</button>
			<button name="action" value="delete">Delete</button>
		</p>
	<? endif ?>
</form>

<script src="<?= baseUrl() ?>app.js"></script>
