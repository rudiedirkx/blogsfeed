<?php

class RSSReader {

	static function parse( $feedUrl, &$error = null ) {
		// get feed
		$context = stream_context_create(array(
			'http' => array(
				'user_agent' => 'Blogsfeed 1.0',
			),
		));
		$text = trim(@file_get_contents($feedUrl, false, $context));
		if ( !$text ) {
			$error = array(
				'error' => 'download:' . __LINE__,
			);
			return false;
		}

		libxml_use_internal_errors(true);
		$xml = @simplexml_load_string($text);
		if ( !$xml ) {
			$errors = libxml_get_errors();
			$error = array(
				'error' => 'xml:' . __LINE__,
				'response' => substr($text, 0, 80),
				'xmlerrors' => $errors,
			);
			return false;
		}

// echo '<pre>';
// echo h(print_r($xml, 1));
// echo '</pre>';

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
			$postImage = self::imageFromEnclosure($blogPost) ?: self::imageFromImage($blogPost) ?: self::imageFromDescription($blogPost) ?: self::imageFromContent($blogPost) ?: '';
			$pubDate = (string)$blogPost->pubDate ?: (string)$blogPost->updated;

			if ( $postImage && $postImage[0] == '/' ) {
				$_url = parse_url($postUrl);
				$postImage = $_url['scheme'] . '://' . $_url['host'] . $postImage;
			}

			// save post
			$data = array(
				'guid' => $postGuid,
				'title' => $postTitle,
				'url' => $postUrl,
				'image' => $postImage,
				'pubdate' => $pubDate ? strtotime($pubDate) : 0,
			);
			$blog['posts'][] = $data;
		}

		return $blog;
	}

	static function imageFromEnclosure( $blogPost ) {
		if ( $_url = (string)@$blogPost->enclosure['url'] ) {
			if ( strpos((string)@$blogPost->enclosure['type'], 'image/') === 0 ) {
				return $_url;
			}
		}
	}

	static function imageFromImage( $blogPost ) {
		if ( $_url = (string)@$blogPost->image->url ) {
			return $_url;
		}
	}

	static function imageFromDescription( $blogPost ) {
		return self::imageFromString((string)@$blogPost->description);
	}

	static function imageFromContent( $blogPost ) {
		return self::imageFromString((string)@$blogPost->content);
	}

	static function imageFromString( $string ) {
		if ( $string ) {
			// Starts with image
			if ( preg_match('#^<img .*?src="([^"]+)"#', trim($string), $match) ) {
				return $match[1];
			}

			// Only HTML, no content
			if ( trim(strip_tags($string)) == '' ) {
				if ( preg_match('#<img .*?src="([^"]+)"#', $string, $match) ) {
					return $match[1];
				}
			}
		}
	}

	static function fakeLink( $xml ) {
		foreach ( $xml->link AS $link ) {
			$rel = (string)$link['rel'];
			if ( !$rel || 'alternate' == $rel ) {
				return (string)$link['href'];
			}
		}
	}

}


