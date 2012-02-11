<?php

$args = array();
if ( isset($_GET['args']) ) {
	$args = array_values(array_filter(explode('/', $_GET['args'])));
}


