<?php

class Blog extends Model {

	static function allWithCreator() {
		global $db;

		$options = array(
			'class' => __CLASS__,
			'params' => array(''),
		);
		return $db->fetch_by_field('SELECT b.*, (name <> ?) AS enabled, u.display_name FROM blogs b LEFT JOIN users u ON (u.id = b.added_by_user_id) ORDER BY b.title', 'id', $options);
	}

	static function all() {
		global $db;

		$options = array(
			'class' => __CLASS__,
			'params' => array(''),
		);
		return $db->fetch_by_field('SELECT *, (name <> ?) AS enabled FROM blogs ORDER BY title', 'id', $options);
	}

	static function allForCronjob() {
		global $db;

		return $db->select_by_field('blogs', 'id', 'name <> ? ORDER BY RAND()', array(''), __CLASS__);
	}

}


