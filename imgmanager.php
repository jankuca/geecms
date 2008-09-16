<?php
if(!isset($_GET['form']))
	define('IN_IMGMANAGER',true);
else
	define('IN_IMGMANAGER_FORM',true);

require_once('./config.php');
$tpl->display();
?>