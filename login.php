<?php

require 'inc.config.php';

user::check('not logged in');

if ( isset($_POST['usr'], $_POST['pwd']) ) {
	setcookie('bf_usr', $_POST['usr']);

	$user = User::get(array(
		'email' => $_POST['usr'],
		'password' => sha1($_POST['pwd']),
		'enabled' => 1,
	));
	if ( $user ) {
		$_SESSION['blogsfeed'] = array(
			'uid' => $user->id,
			'ip' => md5($_SERVER['REMOTE_ADDR']),
		);
		redirect('index.php');
	}

	user::error("That's not it...");

	redirect();
}

require 'inc.menu.php';

?>
<form method="post" action>
	<p>E-mail address: <input name="usr" value="<?= @$_COOKIE['bf_usr'] ?>" required /></p>
	<p>Password: <input type="password" name="pwd" required /></p>
	<p><input type="submit" /></p>
</form>

<p><?=l('Sign up here!', 'signup.php')?></p>

<p>For more info, complaints, issues and the source code: <a href="https://github.com/rudiedirkx/Blogs-feed">https://github.com/rudiedirkx/Blogs-feed</a>.</p>


