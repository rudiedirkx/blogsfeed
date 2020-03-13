
<h2><a href="<?= h($blog->url) ?>"><?= $blog->title ?></a> (<?= count($posts) ?>)</h2>

<table border="1" cellpadding="10" cellspacing="0" width="100%">
	<? foreach ($posts as $post): ?>
		<tr>
			<td width="200">
				<? if ($post->image): ?>
					<a href="<?= h($post->url) ?>">
						<img src="<?= h($post->image) ?>" alt="<?= h($post->title) ?>" style="max-width: 250px; max-height: 250px" />
					</a>
				<? endif ?>
			</td>
			<td>
				<?= postTitleToHtml($post) ?>
			</td>
		</tr>
	<? endforeach ?>
</table>
