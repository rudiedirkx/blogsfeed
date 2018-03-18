<?php

$user = false;

class User extends Model {

	function __construct() {
		if ( isset($this->id) ) {
			$this->id = (int)$this->id;
		}
	}

	function getSubscriptions() {
		global $db;

		return iterator_to_array($db->select('subscriptions', array('user_id' => $this->id)));
	}

	function __tostring() {
		return (string)$this->display_name;
	}


	static function all() {
		global $db;

		return $db->select('users', 1, null, __CLASS__);
	}

	static function messages($clear = true) {
		$messages = (array)@$_SESSION['blogsfeed']['messages'];

		if ( $clear ) {
			$_SESSION['blogsfeed']['messages'] = array();
		}

		return $messages;
	}

	static function error($message) {
		return self::message($message, 'error');
	}

	static function warning($message) {
		return self::message($message, 'warning');
	}

	static function success($message) {
		return self::message($message, 'success');
	}

	static function message($text, $type = 'info') {
		self::logincheck();

		if ( !isset($_SESSION['blogsfeed']['messages']) || !is_array($_SESSION['blogsfeed']['messages']) ) {
			$_SESSION['blogsfeed']['messages'] = array();
		}

		$_SESSION['blogsfeed']['messages'][] = array(
			'text' => $text,
			'type' => $type,
		);
	}

	static function get($conditions) {
		if ( !is_array($conditions) ) {
			$conditions = array('id' => (int)$conditions);
		}

		global $db;
		return $db->select('users', $conditions, null, array(
			'class' => __CLASS__,
			'first' => true,
		));
	}

	static function logincheck() {
		if ( defined('USER_ID') ) {
			return true;
		}

		ini_set('session.cookie_lifetime', 99999999);
		session_id() or session_start();

		if ( isset($_SESSION['blogsfeed']['uid'], $_SESSION['blogsfeed']['ip']) ) {
			if ( $_SESSION['blogsfeed']['ip'] == md5($_SERVER['REMOTE_ADDR']) ) {
				global $user;

				$user = User::get(array(
					'id' => (int)$_SESSION['blogsfeed']['uid'],
					'enabled' => 1,
				));

				if ( $user ) {
					define('USER_ID', (int)$user->id);
					define('CSRF_TOKEN', (string)@$_SESSION['blogsfeed']['token']);

					return true;
				}
			}

			// clear invalid session
			unset($_SESSION['blogsfeed']);
		}
	}

	static function access($zone) {
		switch ( strtolower($zone) ) {
			// Session stuff
			case 'token':
				return User::logincheck() && CSRF_TOKEN === @$_REQUEST['token'];

			// User stuff
			case 'logged in':
				return User::logincheck();

			case 'not logged in':
				return !User::logincheck();

			case 'log in':
				return !User::logincheck();

			case 'log out':
				return User::logincheck();

			case 'add feed':
				return User::logincheck();

			// Admin stuff
			case 'admin users':
				return User::logincheck() && USER_ID == 1;

			case 'admin feeds':
				return User::logincheck() && USER_ID == 1;

			case 'admin subscriptions':
				return User::logincheck() && USER_ID == 1;

			case 'exec queries':
				return User::logincheck() && USER_ID == 1;
		}
	}

	static function check( $zones, $exit = true ) {
		foreach ( (array) $zones AS $zone ) {
			if ( !User::access($zone) ) {
				$prefix = $postfix = '';

				$prefix = '<meta name="viewport" content="width=device-width">';

				if ( 'logged in' == $zone ) {
					$postfix = ' ' . l('Do it here!', 'login.php');
				}

				if ($exit) {
					exit($prefix . 'Access denied (' . $zone . ').' . $postfix);
				}

				return false;
			}
		}

		return true;
	}

}


