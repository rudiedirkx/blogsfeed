<?php

class RSSReader {

	static function parse($feedUrl, &$error = 0) {
		// get feed
		$context = stream_context_create(array(
			'http' => array(
				'user_agent' => 'Blogsfeed 1.0',
			),
		));
		$xml = trim(@file_get_contents($feedUrl, false, $context));
		if ( !$xml ) {
			$error = 'download:' . __LINE__;
			return false;
		}

		$xml = @simplexml_load_string($xml);
		if ( !$xml ) {
			$error = 'xml:' . __LINE__;
			return false;
		}

		// blog info
		$blogTitle = (string)$xml->channel->title ?: (string)$xml->title;
		$blogUrl = (string)$xml->channel->link ?: self::fakeLink($xml);
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
			$postUrl = (string)$blogPost->link ?: self::fakeLink($blogPost);
			$postGuid = (string)$blogPost->guid ?: (string)$blogPost->id ?: $postUrl;
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

}


