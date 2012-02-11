<link rel=stylesheet href="<?=baseUrl()?>base.css" />
<div class="service-menu">
<?php

require user::logincheck() ? 'inc.menu-authenticated.php' : 'inc.menu-anonymous.php';

?>
</div>
