<?php

require 'inc.config.php';

user::check('logged in');

require 'inc.menu.php';

echo '<pre>';
print_r($args);
echo '</pre>';


