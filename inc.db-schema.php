<?php

return array(
	'blogs' => array(
		'id' => array('pk' => true),
		'name',
		'title',
		'url',
		'updated' => array('unsigned' => true),
		'checked' => array('unsigned' => true),
		'feed',
		'added_by_user_id' => array('unsigned' => true, 'default' => 0),
	),
	'blog_posts' => array(
		'id' => array('pk' => true),
		'blog_id' => array('unsigned' => true),
		'guid',
		'title',
		'url',
		'added' => array('unsigned' => true),
		'new' => array('unsigned' => true, 'default' => 1),
	),
	'users' => array(
		'id' => array('pk' => true),
		'email',
		'password',
		'enabled' => array('unsigned' => true, 'default' => 1),
		'display_name',
	),
	'subscriptions' => array(
		'user_id' => array('unsigned' => true),
		'blog_id' => array('unsigned' => true),
	),
);
