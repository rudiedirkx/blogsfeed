<?php

return array(
	'tables' => array(
		'blogs' => array(
			'id' => array('pk' => true),
			'name' => array('type' => 'text'),
			'title' => array('type' => 'text'),
			'url' => array('type' => 'text'),
			'updated' => array('type' => 'int'),
			'checked' => array('type' => 'int'),
			'feed' => array('type' => 'text'),
		),
		'blog_posts' => array(
			'id' => array('pk' => true),
			'blog_id' => array('type' => 'int'),
			'guid' => array('type' => 'text'),
			'title' => array('type' => 'text'),
			'url' => array('type' => 'text'),
			'added' => array('type' => 'int'),
			'new' => array('type' => 'int', 'default' => 1),
		),
		'users' => array(
			'id' => array('pk' => true),
			'username' => array('type' => 'text'),
			'password' => array('type' => 'text'),
		),
	),
);
