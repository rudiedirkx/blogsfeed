<?php

class Model {

	function __get($name) {
		$method = array($this, 'get' . $name);
		if ( is_callable($method) ) {
			return $this->$name = call_user_func($method);
		}

		return null;
	}

}


