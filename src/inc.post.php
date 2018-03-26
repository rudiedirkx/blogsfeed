<?php

class Post extends Model {

	static public $_table = 'blog_posts';

	function get_blog() {
		return Blog::find($this->blog_id);
	}

}
