<?php

$account = null;
if ( isset($_GET['account']) ) {
	if ( user::access('admin subscriptions') ) {
		$account = user::get($_GET['account']);
	}
}
$account or $account = $user;


function otherAccountInfo($account) {
	if ( $account->id != USER_ID ) {
		return ' : ' . h((string)$account);
	}
}
