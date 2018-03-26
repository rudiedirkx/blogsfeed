<?php

require __DIR__ . '/inc.bootstrap.php';

User::check('logged in');

require 'tpl.menu.php';

$batches = $db->select_fields('blog_posts', 'sent, count(1)', '1 group by sent order by sent desc');

?>
<h1>Sent batches</h1>

<table class="table">
	<thead>
		<tr>
			<th>When</th>
			<th>Posts</th>
		</tr>
	</thead>
	<tbody>
		<? foreach ($batches as $batch => $posts): ?>
			<tr>
				<td><?= date('Y-m-d H:i', $batch) ?></td>
				<td><a href="batch.php?id=<?= $batch ?>"><?= $posts ?></a></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
