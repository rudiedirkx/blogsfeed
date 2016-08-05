<?php

return array(
	'tables' => array(
		'blogs' => array(
			'id' => array('pk' => true),
			'name',
			'title',
			'url',
			'updated' => array('unsigned' => true),
			'checked' => array('unsigned' => true),
			'feed',
			'added_by_user_id' => array('unsigned' => true, 'default' => 0),
			'private' => array('unsigned' => true, 'default' => 0),
			'enabled' => array('unsigned' => true, 'default' => 1),
			'fails' => array('unsigned' => true, 'default' => 0),
		),
		'blog_posts' => array(
			'id' => array('pk' => true),
			'blog_id' => array('unsigned' => true),
			'guid',
			'title',
			'url',
			'image',
			'added' => array('unsigned' => true),
			'new' => array('unsigned' => true, 'default' => 1),
			'pubdate' => array('unsigned' => true, 'default' => 0),
		),
		'users' => array(
			'id' => array('pk' => true),
			'email',
			'send_to_email',
			'password',
			'enabled' => array('unsigned' => true, 'default' => 1),
			'display_name',
			'secret',
		),
		'subscriptions' => array(
			'user_id' => array('unsigned' => true),
			'blog_id' => array('unsigned' => true),
		),
	),
	'data' => array(
		'users' => array(
			array(
				'email' => 'admin@hoblox.nl',
				'password' => 'd678e10e7c944dc4ebe23955cce435272f134d5e',
				'display_name' => 'Admin',
			),
		),
		'blogs' => array(
			// instead of listing all default feeds here, the list
			// is in `inc.rss-feeds.php` and can be 'imported' by
			// running `reset.php` (which will do a FULL reset).
		),
	),
);
