<?php

class Blog extends Model {

	static function one( $id ) {
		global $db;

		if ( $id ) {
			return $db->select('blogs', compact('id'), 'Blog')->first();
		}
	}

	static function allWithCreator($account = null) {
		global $db, $user;

		$account or $account = $user;
		$uid = $account ? (int)$account->id : -1;

		$where = array('1');
		if ( !User::access('admin feeds') ) {
			$where[] = $db->replaceholders('(b.private = 0 OR b.added_by_user_id = ?)', array($uid));
		}
		if ( !User::access('admin feeds') ) {
			$where[] = 'b.enabled = 1';
		}

		$sql = "
			SELECT b.*, (b.name <> '') AS activated, u.display_name
			FROM blogs b
			LEFT JOIN users u ON (u.id = b.added_by_user_id)
			WHERE " . implode(' AND ', $where) . "
			ORDER BY b.title
		";
		return $db->fetch_by_field($sql, 'id', array('class' => __CLASS__))->all();
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
		//if ( !User::access('admin feeds') ) {
			$where = ' WHERE (private = 0 OR added_by_user_id = ?)';
			$options['params'][] = $uid;
		//}

		$sql = '
			SELECT *, (name <> ?) AS activated
			FROM blogs
			' . $where . '
			ORDER BY title
		';
		return $db->fetch_by_field($sql, 'id', $options)->all();
	}

	static function allForCronjob() {
		global $db;

		return $db->select_by_field('blogs', 'id', "enabled = 1 AND (name <> '' OR private <> 0) ORDER BY title ASC", array(), __CLASS__)->all();
	}

}


