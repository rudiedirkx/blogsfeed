<?php

require 'inc.config.php';

user::check('exec queries');

if ( isset($_POST['q']) ) {
	header('Content-type: text/plain; charset=utf-8');
	set_time_limit(0);

	var_dump($db->execute($_POST['q']));
	exit;
}

require 'tpl.menu.php';

?>
<h1>
	Query
</h1>

<form method="post" action>
	<p><textarea name="q" rows="10" cols="100"></textarea></p>

	<p><input type="submit" /></p>
</form>


