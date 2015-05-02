<?php

exit("Closed\n");

require 'inc.config.php';

user::check('not logged in');

if ( isset($_GET['uid'], $_GET['secret']) ) {
	$user = User::get(array(
		'id' => $_GET['uid'],
		'secret' => $_GET['secret'],
	));

	// enable & log in
	if ( $user ) {
		// enable
		$db->update('users', array(
			'enabled' => 1,
			'secret' => NULL,
		), array(
			'id' => $user->id,
		));

		// log in
		$_SESSION['blogsfeed'] = array(
			'uid' => $user->id,
			'ip' => md5($_SERVER['REMOTE_ADDR']),
		);

		user::success('Account verified and logged in');

		redirect('index.php');
	}

	// fail -- notify
	exit("That's not it... Did you lose your secret code!?");
}

$error = '';
if ( isset($_POST['email'], $_POST['name'], $_POST['password']) ) {
	$email = trim($_POST['email']);
	$name = trim($_POST['name']);
	$password = trim($_POST['password']);

	// valid name
	$L = strlen($name);
	if ( 4 <= $L && 30 >= $L ) {

		// valid e-mail
		if ( filter_var($email, FILTER_VALIDATE_EMAIL) ) {

			// unique e-mail
			if ( !$db->count('users', array('email' => $email)) ) {

				// valid password
				$L = strlen($password);
				if ( 5 <= $L ) {

					// create user
					$data = array(
						'email' => $email,
						'password' => sha1($password),
						'display_name' => $name,
						'enabled' => 0,
						'secret' => rand(0, 999999),
					);
					$db->insert('users', $data);

					// get user
					$user = user::get($db->insert_id());

					// send e-mail
					$subject = 'Magic link for Blogs feed';
					$html = 'Magic link: ' . u('signup.php?uid=' . $user->id . '&secret=' . $user->secret, array('absolute' => 1)) . "\n";
					@mail($email, $subject, $html, "From: Blogs feed <blogsfeed@hoblox.nl>\r\n");

					user::success("I've sent you a confirmation e-mail. Click the link in it.");

					redirect();

				}
				else {
					$error = 'password';
				}

			}
			else {
				$error = 'email';
			}

		}
		else {
			$error = 'email';
		}

	}
	else {
		$error = 'name';
	}

	user::error("That's not it...");
}

require 'inc.menu.php';

?>
<form method="post" action autocomplete="off">
	<p class="<?=formError('email' == $error)?>">
		E-mail address:
		<input type="email" name="email" value="<?=h(@$_POST['email'])?>" class="text" required />
	</p>
	<p class="<?=formError('password' == $error)?>">
		Password:
		<input name="password" value="<?=h(@$_POST['password'])?>" class="text" required pattern=".{5,}" />
		(5- chars)
	</p>
	<p class="<?=formError('name' == $error)?>">
		Display name:
		<input name="name" value="<?=h(@$_POST['name'])?>" class="text" required maxlength="30" pattern=".{4,30}" />
		(4-30 chars)
	</p>

	<p><input type="submit" /></p>
</form>

<p><?= l('Log in here!', 'login.php') ?></p>


