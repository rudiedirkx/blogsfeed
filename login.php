<?php

require 'inc.config.php';

user::check('not logged in');

if ( isset($_POST['usr'], $_POST['pwd']) ) {
	$user = User::get(array(
		'username' => $_POST['usr'],
		'password' => $_POST['pwd'],
	));
	if ( $user ) {
		$_SESSION['blogsfeed'] = array(
			'uid' => $user->id,
			'ip' => md5($_SERVER['REMOTE_ADDR']),
		);
		redirect('index');
	}

	echo "Nope...\n\n";
}

require 'inc.menu.php';

?>
<form method="post" action>
	<p>Username: <input name="usr" required /></p>
	<p>Password: <input type="password" name="pwd" required /></p>
	<p><input type="submit" /></p>
</form>

<p><?=l('Sign up here!', 'signup')?></p>


