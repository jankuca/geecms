<?php
class module_menu
{
	public $items = array();
	
	public function __construct($acp = false)
	{
		global $q,$cfg,$tpl;
		
		if(!$acp)
		{
			$sql = new MySQLObject();
			$sql->query("SELECT `header`,`link` FROM " . $q->table('menu') . " ORDER BY `order` ASC");
			foreach($sql->fetch() as $item)
			{
				$uri = explode('/',$item->link,2);
				$req = explode('/',$_SERVER['REQUEST_URI'],2);
				$this->items[] = array(
					'HEADER' => $item->header,
					'LINK' => $item->link,
					'ACTIVE' => ($req[1] == $uri[1])
						? $cfg['tpl']['class_active']
						: ''
				);
			}
			$tpl->assign('SITE_MENU',$this->items,'foreach');
		}
		else
		{
			if(count($cfg['acp_menu']) > 0)
			{
				$tpl->assign('ACP_MENU',true,'if');
				$tpl->assign('ACP_MENU',$cfg['acp_menu'],'foreach');
			}
			else
			{
				$tpl->assign('ACP_MENU',false,'if');
			}

			if(count($cfg['acp_modules_menu']) > 0)
			{
				$tpl->assign('ACP_MODULES_MENU',true,'if');
				$tpl->assign('ACP_MODULES_MENU',$cfg['acp_modules_menu'],'foreach');
			}
			else
			{
				$tpl->assign('ACP_MODULES_MENU',false,'if');
			}
			
			if(count($cfg['acp_submenu']) > 0)
			{
				$tpl->assign('ACP_SUBMENU',true,'if');
				$tpl->assign('ACP_SUBMENU',$cfg['acp_submenu'],'foreach');
			}
			else
			{
				$tpl->assign('ACP_SUBMENU',false,'if');
			}
		}
	}
}

global $cfg;
$cfg['tpl']['class_active'] = ' class="active"';
$cfg['tpl']['class_subactive'] = ' class="subactive"';

if(defined('IN_SYS') && IN_SYS)
{
	global $mod;
	$mod->modules['menu'] = new module_menu();
}

if(defined('IN_ACP') && IN_ACP)
{
	$cfg['acp_menu'] = array();
	$cfg['acp_modules_menu'] = array();
	$cfg['acp_submenu'] = array();
	
	$cfg['acp_menu'][] = array(
		'LINK' => './acp.php',
		'HEADER' => '{L_ACP_INDEX}',
		'ACTIVE' => (!isset($_GET['c']))
		? $cfg['tpl']['class_active'] : ''
	);

	global $tpl;
	$tpl->queue[0][] = 'global $cfg;
	$cfg[\'acp_modules_menu\'][] = array(
		\'LINK\' => \'./acp.php?c=menu\',
		\'HEADER\' => \'{L_MODULE_MENU}\',
		\'ACTIVE\' => (isset($_GET[\'c\']) && $_GET[\'c\'] == \'menu\')
		? $cfg[\'tpl\'][\'class_active\'] : \'\'
	);';
	$tpl->queue[1][] = 'global $mod;
	$mod->modules[\'menu\'] = new module_menu(true);';
}
?>