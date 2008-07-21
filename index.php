<?php
$starttime = microtime();
// define the current location
define('IN_SYS',true);

require_once('./config.php');

$tpl->display();

/*$endtime = microtime(); echo((1000 * round($endtime - $starttime,3)) . ' ms');
$syslog->log();/**/
?>