<?php

$account = null;
if ( isset($_GET['account']) ) {
	if ( User::access('admin subscriptions') ) {
		$account = User::get($_GET['account']);
	}
}
$account or $account = $user;


function otherAccountInfo($account) {
	if ( $account->id != USER_ID ) {
		return ' : ' . h((string)$account);
	}
}
