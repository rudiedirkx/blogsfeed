<?php

require __DIR__ . '/inc.bootstrap.php';

User::check('logged in');

$account = isset($args[0]) ? User::find($args[0]) : false;

require 'tpl.menu.php';

?>
<h1>
	<?=h($account)?>
</h1>


