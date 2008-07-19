<?php
error_reporting(6143);
#header('Content-Type: application/xhtml+xml; charset=UTF-8');

// load the functions
include_once('./app/lib/geecms_functions.php');

// load the subsystems
include_once('./app/subsystems/syslog.php');
include_once('./app/subsystems/mysql.php');
include_once('./app/subsystems/template.php');
include_once('./app/subsystems/lang.php');
include_once('./app/subsystems/modules.php');

// mysql: connection
$q->connect('localhost','blackpig','vGVbTen9y*:Ue7PW','blackpig');
$q->prefix = 'geecms_';

// mysql: select the configuration
$cfg = array();
$cfg['etc'] = array();
$cfg['tpl'] = array();
if(defined('IN_IMAGES') && IN_IMAGES)
	$cfg['tpl']['images'] = array();

$sql = new MySQLObject();
$sql->query("SELECT `name`,`value`,`assign` FROM " . $q->table('config') . "");
foreach($sql->fetch() as $item)
{
	$cfg['etc'][$item->name] = $item->value;
	if(intval($item->assign) == true)
		$tpl->assign($item->name,$item->value);
}
unset($sql);

define('SITE_ROOT_PATH',$cfg['etc']['SITE_ROOT_PATH']);

// load the libraries
include_once('./app/lib/pages.class.php');
include_once('./app/lib/js/fckeditor/fckeditor.php');

/*$p = new Pages();
$p->query = "SELECT `uid`,`username` FROM " . $q->table('users') . " ORDER BY `uid` ASC";
$p->make();*/

// modules: load
$lang->load();
$mod->load();
?>