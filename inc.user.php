<?php

$user = false;

class User extends Model {

	static function get($conditions) {
		if ( !is_array($conditions) ) {
			$conditions = array('id' => (int)$conditions);
		}

		global $db;
		return $db->select('users', $conditions, null, array(
			'class' => 'User',
			'first' => true,
		));
	}

	static function logincheck() {
		if ( defined('USER_ID') ) {
			return true;
		}

		session_start();

		if ( isset($_SESSION['blogsfeed']['uid'], $_SESSION['blogsfeed']['ip']) ) {
			if ( $_SESSION['blogsfeed']['ip'] == md5($_SERVER['REMOTE_ADDR']) ) {
				global $user;

				$user = User::get((int)$_SESSION['blogsfeed']['uid']);

				if ( $user ) {
					define('USER_ID', $user->id);

					return true;
				}
			}
		}
	}

	static function access($zone) {
		switch ( strtolower($zone) ) {
			case 'logged in':
				return user::logincheck();

			case 'not logged in':
				return !user::logincheck();

			case 'log in':
				return !user::logincheck();

			case 'log out':
				return user::logincheck();

			case 'admin users':
				return !user::logincheck();

			case 'add feed':
				return !user::logincheck();

			case 'admin feeds':
				return !user::logincheck();
		}
	}

	static function check($zones) {
		foreach ( (array)$zones AS $zone ) {
			if ( !user::access($zone) ) {
				$extra = '';
				if ( 'logged in' == $zone ) {
					$extra = ' ' . l('Do it here!', 'login');
				}

				exit('Access denied (' . $zone . ').' . $extra);
			}
		}
	}

}


