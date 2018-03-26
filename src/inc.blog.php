<?php

class Blog extends Model {

	static public $_table = 'blogs';

	function renderPosts( array $posts ) {
		$blog = $this;

		ob_start();
		include 'tpl.new-posts.php';
		return ob_get_clean();
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

	static function allForUser($account) {
		global $db;

		$uid = $account->id;

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


