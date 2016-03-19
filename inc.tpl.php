<?php

function postTitleToHtml($post) {
	// HTML-ize
	$html = nl2br(h(trim($post->title)));

	// Insert inner links
	$html = preg_replace('#(https?://[^\s<]+)#', '</a><a href="$1">$1</a><a href="' . h($post->url) . '">', $html);

	// Wrap with outer link
	$html = '<a href="' . h($post->url) . '">' . $html . '</a>';

	// Remove empty links
	$html = preg_replace_callback('#<a[\s\S]+?>([\s\S]*?)</a>#', function($match) {
		list($match, $label) = $match;

		// No contents
		if (!trim($label)) {
			return $label;
		}

		// Only newlines
		if (preg_match('#^(<br />\s*)+$#', trim($label))) {
			return $label;
		}

		// Valid contents
		return $match;
	}, $html);

	return $html;
}

function formError($error) {
	return $error ? 'error' : 'valid';
}

function ul($items) {
	return '<ul><li>' . implode('</li><li>', $items) . '</li></ul>';
}

function attr($attr, $except = array()) {
	$html = '';

	foreach ( $attr AS $name => $value ) {
		if ( !in_array($name, $except) ) {
			$html .= ' ' . $name . '="' . h($value) . '"';
		}
	}

	return $html;
}

function redirect($uri = null) {
	if ( null !== $uri ) {
		$uri = u($uri);
	}
	else {
		$uri = $_SERVER['REQUEST_URI'];
		/*if ( is_int($p = strpos($uri, '?')) ) {
			$uri = substr($uri, 0, $p);
		}*/
	}

	header('Location: ' . $uri);
	exit;
}

function l($label, $uri, $options = array()) {
	!empty($options['html']) or $label = h($label);

	$attr = attr($options, array('attributes', 'html', 'query'));
	return '<a' . $attr . ' href="' . u($uri) . '">' . $label . '</a>';
}

function baseUrl() {
	$dir = dirname(__FILE__);
	$docroot = $_SERVER['DOCUMENT_ROOT'];
	$doc = (string)substr($dir, strlen($docroot));

	$base = '/' . str_replace('\\', '/', $doc) . '/';
	$base = str_replace('//', '/', $base);

	return $base;
}

function u($uri, $options = array()) {
	$base = baseUrl();

	if ( 0 !== strpos($uri, 'http') && 0 !== strpos($uri, '/') ) {
		if ( @$options['absolute'] ) {
			$uri = 'http://' . $_SERVER['HTTP_HOST'] . $uri;
		}
	}

	return $uri;
}

function h($str) {
	return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
}


