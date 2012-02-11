<?php

require 'inc.config.php';

user::check('logged in');

unset($_SESSION['blogsfeed']);

redirect('index');
