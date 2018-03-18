<?php

require __DIR__ . '/inc.bootstrap.php';

User::check('logged in');

unset($_SESSION['blogsfeed']);

redirect('index.php');
