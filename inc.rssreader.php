<?php

class RSSReader {

	static function parse($feedUrl) {
		// get feed
		$xml = @simplexml_load_file($feedUrl);
		if ( !$xml ) {
			return false;
		}

		// blog info
		$blogTitle = self::ternary(
			(string)$xml->channel->title,
			(string)$xml->title
		);
		$blogUrl = self::ternary(
			(string)$xml->channel->link,
			self::fakeLink($xml)
		);
		$blog = array(
			'title' => $blogTitle,
			'url' => $blogUrl,
			'feed' => $feedUrl,
			'posts' => array(),
		);

		// get posts source
		if ( $xml->channel && $xml->channel->item ) {
			$posts = $xml->channel->item;
		}
		else {
			$posts = $xml->entry;
		}

		// cycle posts
		foreach ( $posts AS $blogPost ) {
			// post info
			$postUrl = self::ternary(
				(string)$blogPost->link,
				self::fakeLink($blogPost)
			);
			$postGuid = self::ternary(
				(string)$blogPost->guid,
				(string)$blogPost->id,
				$postUrl
			);
			$postTitle = (string)$blogPost->title;

			// save post
			$data = array(
				'guid' => $postGuid,
				'title' => $postTitle,
				'url' => $postUrl,
			);
			$blog['posts'][] = $data;
		}

		return $blog;
	}

	static function fakeLink($xml) {
		foreach ( $xml->link AS $link ) {
			$rel = (string)$link['rel'];
			if ( !$rel || 'alternate' == $rel ) {
				return (string)$link['href'];
			}
		}
	}

	function ternary($a, $b) {
		$args = func_get_args();
		foreach ( $args AS $arg ) {
			if ( $arg ) {
				return $arg;
			}
		}
	}

}


