<?php

class Blog extends Model {

	static function allWithCreator($account = null) {
		global $db, $user;

		$account or $account = $user;
		$uid = $account ? (int)$account->id : -1;

		$options = array(
			'class' => __CLASS__,
			'params' => array(''),
		);

		$where = '';
		if ( !user::access('admin feeds') ) {
			$where = ' WHERE (private = 0 OR added_by_user_id = ?)';
			$options['params'][] = $uid;
		}

		$sql = 'SELECT b.*, (name <> ?) AS enabled, u.display_name FROM blogs b LEFT JOIN users u ON (u.id = b.added_by_user_id)' . $where . ' ORDER BY b.title';
		return $db->fetch_by_field($sql, 'id', $options)->all();
	}

	static function all($account = null) {
		global $db, $user;

		$account or $account = $user;
		$uid = $account ? (int)$account->id : -1;

		$options = array(
			'class' => __CLASS__,
			'params' => array(''),
		);

		$where = '';
		//if ( !user::access('admin feeds') ) {
			$where = ' WHERE (private = 0 OR added_by_user_id = ?)';
			$options['params'][] = $uid;
		//}

		$sql = 'SELECT *, (name <> ?) AS enabled FROM blogs' . $where . ' ORDER BY title';
		return $db->fetch_by_field($sql, 'id', $options)->all();
	}

	static function allForCronjob() {
		global $db;

		return $db->select_by_field('blogs', 'id', '(name <> ? OR private <> 0) ORDER BY id ASC', array(''), __CLASS__)->all();
	}

}


