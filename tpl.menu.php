<!doctype html>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width" />
<meta name="theme-color" content="#333" />

<title>Blogs feed</title>

<link rel="stylesheet" href="<?=baseUrl()?>base.css" />
<link rel="shortcut icon" href="<?=baseUrl()?>favicon.ico" type="image/x-icon">

<?if( $messages = User::messages() ):?>
	<div class="messages">
		<ul>
			<?foreach( $messages AS $msg ):?>
				<li class="<?=$msg['type']?>"><?=$msg['text']?></li>
			<?endforeach?>
		</ul>
	</div>
<?endif?>

<div class="service-menu">
<?php

require User::logincheck() ? 'tpl.menu-authenticated.php' : 'tpl.menu-anonymous.php';

?>
</div>
