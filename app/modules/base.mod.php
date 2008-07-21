<?php
class module_base
{
	public $breadcrumbs;
	
	public function __construct()
	{
		global $tpl;
		if(defined('IN_ACP') && IN_ACP)
		{
			$tpl->inc('header',1);
			
			$tpl->inc('acp_menu',1);
			
			$tpl->queue[1][] = 'global $tpl;
			$tpl->inc(\'footer\',1);';
		}
		elseif(defined('IN_SYS') && IN_SYS)
		{
			$tpl->inc('header');
			
			$tpl->queue[0][] = 'global $tpl;
			$tpl->inc(\'footer\');';
		}
	}
	
	public function _module_config()
	{
		global $tpl;
		$sql = new MySQLObject();
		$tpl->_module_config("
SELECT `name`,`value`,`type`
FROM " . $sql->table('config') . "
WHERE (`module` = 'base')");
	}
}

global $mod;
$mod->modules['base'] = new module_base();

global $tpl;
$tpl->assign('U_INDEX','./');
$tpl->assign('U_ACP','./acp.php');
if(isset($_SERVER['HTTP_REFERER']))
	$tpl->assign('U_REFERER',$_SERVER['HTTP_REFERER']);
else
	$tpl->assign('U_REFERER','./');

if(
	defined('IN_SYS')
	&& IN_SYS
	&& !isset($_GET['c'])
)
{
	$tpl->assign('SITE_TITLE','{SITE_HEADER} {SITE_SLOGAN}');
}

if(defined('IN_ACP') && IN_ACP)
{
	global $tpl;
/*	global $cfg;
	$cfg['acp_menu'][] = array(
		'LINK' => './acp.php?c=config&amp;module=base',
		'HEADER' => '{L_MODULE_BASE_CONFIG}',
		'ACTIVE' => (isset($_GET['c'],$_GET['module']) && $_GET['c'] == 'config' && $_GET['module'] == 'base')
			? $cfg['tpl']['class_active']
			: ''
	);*/
	$tpl->queue[0][] = 'global $cfg;
	$cfg[\'acp_menu\'][] = array(
		\'LINK\' => \'{U_INDEX}\',
		\'HEADER\' => \'{L_SITE_INDEX}\',
		\'ACTIVE\' => \'\'
	);
	$cfg[\'acp_menu\'][] = array(
		\'LINK\' => \'./acp.php?c=config&amp;module=base\',
		\'HEADER\' => \'{L_MODULE_BASE_CONFIG}\',
		\'ACTIVE\' => (isset($_GET[\'c\'],$_GET[\'module\']) && $_GET[\'c\'] == \'config\' && $_GET[\'module\'] == \'base\')
			? $cfg[\'tpl\'][\'class_active\']
			: \'\'
	);';

	if(!isset($_GET['c']))
	{
		$tpl->inc('acp_index',1);
		
		$tpl->assign('SITE_TITLE','{SITE_HEADER} / {L_ACP}');
		
		$tpl->assign('BREADCRUMBS',array(
			array(
				'LINK' => '{U_ACP}',
				'HEADER' => '{L_ACP_INDEX}'
			)
		),'foreach');
		
		$tpl->queue[1][] = 'global $cfg,$tpl;
		if(count($cfg[\'installed_modules\']) > 0)
		{
			$tpl->assign(\'INSTALLED_MODULES\',true,\'if\');
			$tpl->assign(\'INSTALLED_MODULES\',$cfg[\'installed_modules\'],\'foreach\');
		}
		else
		{
			$tpl->assign(\'INSTALLED_MODULES\',false,\'if\');
		}';
	}
	else
	{
		if($_GET['c'] == 'config')
		{
			$mod->modules['base']->breadcrumbs[] = array(
				'LINK' => './acp.php',
				'HEADER' => '{L_CONFIG}'
			);
				
			if($_GET['module'] == 'base')
			{
				$mod->modules['base']->breadcrumbs[] = array(
					'LINK' => './acp.php?c=config&amp;module=base',
					'HEADER' => '{L_MODULE_BASE_CONFIG}'
				);
				
				$mod->modules['base']->_module_config();
				
				$tpl->inc('config_base',1);
				
				$tpl->assign('SITE_TITLE','{L_CONFIG} &mdash; {L_MODULE_BASE_CONFIG} &ndash; {SITE_HEADER} / {L_ACP}');
			}
		
			$tpl->assign('BREADCRUMBS',$mod->modules['base']->breadcrumbs,'foreach');
		}
	}
}

if(defined('IN_IMAGES') && IN_IMAGES)
{
	global $cfg;
	$cfg['tpl']['images']['config'] = 'acp/images/config.png';
	$cfg['tpl']['images']['delete'] = 'acp/images/delete.png';
}
?>