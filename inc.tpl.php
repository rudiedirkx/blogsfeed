<?php

function attr($attr, $except = array()) {
	$html = '';

	foreach ( $attr AS $name => $value ) {
		if ( !in_array($name, $except) ) {
			$html .= ' ' . $name . '="' . h($value) . '"';
		}
	}

	return $html;
}

function redirect($uri) {
	$uri = u($uri);

	header('Location: ' . $uri);
	exit;
}

function l($label, $uri, $options = array()) {
	!empty($options['html']) or $label = h($label);

	$attr = attr($options, array('attributes', 'html', 'query'));
	return '<a' . $attr . ' href="' . u($uri) . '">' . $label . '</a>';
}

function u($uri) {
	static $base;

	if ( !$base ) {
		$doc = substr(__FILE__, strlen($_SERVER['DOCUMENT_ROOT']));
		$base = '/' . str_replace('\\', '/', dirname($doc)) . '/';
	}

	if ( 0 !== strpos($uri, 'http') && 0 !== strpos($uri, '/') ) {
		$uri = str_replace('//', '/', $base . 'app/' . $uri);
	}

	return $uri;
}

function h($str) {
	return htmlspecialchars($str, ENT_COMPAT | ENT_HTML401, 'UTF-8');
}


