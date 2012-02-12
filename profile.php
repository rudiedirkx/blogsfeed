<?php

require 'inc.config.php';

user::check('logged in');

$account = isset($args[0]) ? user::get($args[0]) : false;

require 'inc.menu.php';

?>
<h1>
	<?=h($account)?>
</h1>


