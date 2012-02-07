<?php

return array(
	'codinghorror' => array(
		'url' => 'http://www.codinghorror.com/blog/',
		'extraction' => array(
			'type' => 'match all',
			'pattern' => '#<h3[^>]*>\s*(<[^<]+)<#is',
			'first' => true,
			'next' => array(
				array(
					'type' => 'alter',
					'functions' => array(
						'strip_tags' => array(array(), 0),
						'trim' => array(array(), 0),
					),
					'save' => 'titles',
				),
				array(
					'type' => 'match one',
					'pattern' => '/href=(?:\'|")([^\'"]+)/',
					'save' => 'urls',
				),
			),
		),
	),
	'snook.ca' => array(
		'url' => 'http://snook.ca/',
		'extraction' => array(
			'type' => 'match all',
			'pattern' => '#<div class="article">\s*<h2>(.+?)<#is',
			'first' => true,
			'next' => array(
				array(
					'type' => 'alter',
					'functions' => array(
						'strip_tags' => array(array(), 0),
						'trim' => array(array(), 0),
					),
					'save' => 'titles',
				),
				array(
					'type' => 'match one',
					'pattern' => '/href=(?:\'|")([^\'"]+)/',
					'save' => 'urls',
				),
			),
		),
	),
	'mediaqueri.es' => array(
		'url' => 'http://mediaqueri.es/',
		'extraction' => array(
			'type' => 'match all',
			'pattern' => '#<h3[^>]*>(.+?)<\/h3>#is',
			'first' => true,
			'next' => array(
				array(
					'type' => 'alter',
					'functions' => array(
						'strip_tags' => array(array(), 0),
						'trim' => array(array(), 0),
					),
					'save' => 'titles',
				),
				array(
					'type' => 'match one',
					'pattern' => '/href=(?:\'|")([^\'"]+)/',
					'save' => 'urls',
				),
			),
		),
	),
	'css3.info' => array(
		'url' => 'http://www.css3.info/',
		'extraction' => array(
			'type' => 'match all',
			'pattern' => '#<div class="name">(.+?)<\/a>#is',
			'first' => true,
			'next' => array(
				array(
					'type' => 'alter',
					'functions' => array(
						'strip_tags' => array(array(), 0),
						'trim' => array(array(), 0),
					),
					'save' => 'titles',
				),
				array(
					'type' => 'match one',
					'pattern' => '/href=(?:\'|")([^\'"]+)/',
					'save' => 'urls',
				),
			),
		),
	),
	'useragentman' => array(
		'url' => 'http://www.useragentman.com/blog/',
		'extraction' => array(
			'type' => 'split',
			'pattern' => '#excerpts">#',
			'slice' => array(1, 1),
			'next' => array(
				array(
					'type' => 'match all',
					'pattern' => '#<h3>(.+?)<\/h3>#is',
					'first' => true,
					'next' => array(
						array(
							'type' => 'alter',
							'functions' => array(
								'strip_tags' => array(array(), 0),
								'trim' => array(array(), 0),
							),
							'save' => 'titles',
						),
						array(
							'type' => 'match one',
							'pattern' => '/href=(?:\'|")([^\'"]+)/',
							'save' => 'urls',
						),
					),
				),
			),
		),
	),
	'schemehostport' => array(
		'url' => 'http://www.schemehostport.com/',
		'extraction' => array(
			'type' => 'split',
			'pattern' => '#class=[\'"]blog-posts#',
			'slice' => array(1, 1),
			'next' => array(
				array(
					'type' => 'match all',
					'pattern' => '#<h3[^>]*>(.+?)<\/h3>#is',
					'first' => true,
					'next' => array(
						array(
							'type' => 'alter',
							'functions' => array(
								'strip_tags' => array(array(), 0),
								'trim' => array(array(), 0),
							),
							'save' => 'titles',
						),
						array(
							'type' => 'match one',
							'pattern' => '/href=(?:\'|")([^\'"]+)/',
							'save' => 'urls',
						),
					),
				),
			),
		),
	),
	'leaverou' => array(
		'url' => 'http://lea.verou.me/',
		'extraction' => array(
			'type' => 'match all',
			'pattern' => '#<article[^>]*>(.+?)<\/h1>#is',
			'first' => true,
			'next' => array(
				array(
					'type' => 'alter',
					'functions' => array(
						'strip_tags' => array(array(), 0),
						'trim' => array(array(), 0),
					),
					'save' => 'titles',
				),
				array(
					'type' => 'match one',
					'pattern' => '/href=(?:\'|")([^\'"]+)/',
					'save' => 'urls',
				),
			),
		),
	),
	'nicolasgallagher' => array(
		'url' => 'http://nicolasgallagher.com/',
		'extraction' => array(
			'type' => 'match all',
			'pattern' => '#<article[^>]*>.*?<h1[^>]*>(.+?)<\/h1>#is',
			'first' => true,
			'next' => array(
				array(
					'type' => 'alter',
					'functions' => array(
						'strip_tags' => array(array(), 0),
						'trim' => array(array(), 0),
						'html_entity_decode' => array(array(), 0),
					),
					'save' => 'titles',
				),
				array(
					'type' => 'match one',
					'pattern' => '/href=(?:\'|")([^\'"]+)/',
					'save' => 'urls',
				),
			),
		),
	),
);


