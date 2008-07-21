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
			$sql->query("SELECT `header`,`link` FROM " . $q->table('menu') . " WHERE (`show` = 1) ORDER BY `order` ASC");
			foreach($sql->fetch() as $item)
			{
				$uri = explode('/',$item->link,2);
				$req = explode('/',$_SERVER['REQUEST_URI'],2);
				$this->items[] = array(
					'HEADER' => $item->header,
					'LINK' => './' . $item->link,
					'ACTIVE' => ($req[1] == $uri[0])
						? $cfg['tpl']['class_active']
						: ''
				);
			}
			$tpl->assign('SITE_MENU',$this->items,'foreach');
		}
		else
		{
			global $tpl;
			$tpl->queue[0][] = 'global $tpl;
			if(count($cfg[\'acp_menu\']) > 0)
			{
				$tpl->assign(\'ACP_MENU\',true,\'if\');
				$tpl->assign(\'ACP_MENU\',$cfg[\'acp_menu\'],\'foreach\');
			}
			else
			{
				$tpl->assign(\'ACP_MENU\',false,\'if\');
			}

			if(count($cfg[\'acp_modules_menu\']) > 0)
			{
				$tpl->assign(\'ACP_MODULES_MENU\',true,\'if\');
				$tpl->assign(\'ACP_MODULES_MENU\',$cfg[\'acp_modules_menu\'],\'foreach\');
			}
			else
			{
				$tpl->assign(\'ACP_MODULES_MENU\',false,\'if\');
			}
			
			if(count($cfg[\'acp_submenu\']) > 0)
			{
				$tpl->assign(\'ACP_SUBMENU\',true,\'if\');
				$tpl->assign(\'ACP_SUBMENU\',$cfg[\'acp_submenu\'],\'foreach\');
			}
			else
			{
				$tpl->assign(\'ACP_SUBMENU\',false,\'if\');
			}';
		}
	}
	
	public function mainmenu_items()
	{
		global $tpl,$cfg;
		
		$sql = new MySQLObject();
		if($sql->query("
SELECT `iid`,`header`,`link`,`show`
FROM " . $sql->table('menu') . "
ORDER BY `order` ASC"))
		{
			$num = $sql->num();
			
			if($num == 0)
			{
				$tpl->assign('INFOBAR',true,'if');
				$tpl->assign('INFOBAR','{L_MENU_MAINMENU_NO_ITEMS}');
			}
			
			$f_items = array();
			$i = 0;
			foreach($sql->fetch() as $item)
			{
				$f_items[] = array(
					'ITEM_HEADER' => $item->header,
					'ITEM_LINK' => './' . $item->link,
					'ITEM_ID' => $item->iid,
					'ITEM_LINK_EDIT' => './acp.php?c=menu&amp;mode=edit&amp;iid=' . $item->iid,
					'ITEM_LINK_DELETE' => './action.php?c=menu&amp;mode=delete&amp;iid=' . $item->iid,
					
					'ITEM_LINK_SHOW_HIDE' => './action.php?c=menu&amp;mode=' . (($item->show == 0) ? 'show' : 'hide') . '&amp;iid=' . $item->iid,
					'ITEM_TEXT_SHOW_HIDE' => (($item->show == 0) ? '{L_SHOW}' : '{L_HIDE}'),
					
					'ITEM_LINK_MOVEUP' => ($i == 0) ? '' : str_replace('<var(LINK)>','./action.php?c=menu&amp;mode=move&amp;dir=up&amp;iid=' . $item->iid,$cfg['tpl']['link']['moveup']),
					'ITEM_LINK_MOVEDOWN' => ($i == $num - 1) ? '' : str_replace('<var(LINK)>','./action.php?c=menu&amp;mode=move&amp;dir=down&amp;iid=' . $item->iid,$cfg['tpl']['link']['movedown']),
				);
				
				$i++;
			}
			
			if(count($f_items) > 0)
			{
				$tpl->assign('MENU_MAINMENU',true,'if');
				$tpl->assign('MENU_MAINMENU',$f_items,'foreach');
			}
			else
				$tpl->assign('MENU_MAINMENU',false,'if');
		}
	}
	
	public function edit_item()
	{
		$sql = new MySQLObject();
		if(
			$sql->query("
SELECT `header`,`link`,`show`
FROM " . $sql->table('menu') . "
WHERE (`iid` = " . intval($_GET['iid']) . ")"
			)
			&& $sql->num() > 0)
		{
			$item = $sql->fetch_one();
			
			global $tpl,$cfg;
			$tpl->assign(array(
				'ITEM.HEADER' => $item->header,
				'ITEM.LINK' => $item->link,
				'ITEM.SHOW_TRUE' => ($item->show == 1) ? $cfg['tpl']['checked'] : '',
				'ITEM.SHOW_FALSE' => ($item->show == 0) ? $cfg['tpl']['checked'] : ''
			));
		}
	}
	
	public function add_item()
	{
		global $tpl;
		$tpl->queue[0][] = 'global $cfg,$tpl;
		$tpl->assign(\'ADD\',$cfg[\'menu_add\'][\'modules\'],\'foreach\');';
	}
}

global $cfg,$mod;
$cfg['permissions']['menu']['items'] = array('edit','delete','add');

if(defined('IN_SYS') && IN_SYS)
{
	$mod->modules['menu'] = new module_menu();
}

if(defined('IN_ACP') && IN_ACP)
{
	$mod->modules['menu'] = new module_menu(true);
	
	$cfg['acp_menu'] = array();
	$cfg['acp_modules_menu'] = array();
	$cfg['acp_submenu'] = array();
	
	$cfg['acp_menu'][] = array(
		'LINK' => './acp.php',
		'HEADER' => '{L_ACP_INDEX}',
		'ACTIVE' => (!isset($_GET['c']))
		? $cfg['tpl']['class_active'] : ''
	);
	
	if(!isset($_GET['c']))
	{
		global $cfg;
		$cfg['installed_modules'][] = array(
			'MODULE_HEADER' => '{L_MODULE_MENU}',
			'MODULE_DESCRIPTION' => '{L_MODULE_MENU_DESCRIPTION}',
			'MODULE_LINK' => './acp.php?c=menu',
			'MODULE_IMAGE' => 'images.php?image=module_menu'
		);
	}

	global $tpl;
	$cfg['acp_modules_menu'][] = array(
		'LINK' => './acp.php?c=menu',
		'HEADER' => '{L_MODULE_MENU}',
		'ACTIVE' => (isset($_GET['c']) && $_GET['c'] == 'menu')
		? $cfg['tpl']['class_subactive'] : ''
	);
	
	if(isset($_GET['c']) && $_GET['c'] == 'menu')
	{
		$mod->modules['menu']->breadcrumbs[] = array(
			'LINK' => './acp.php?c=menu',
			'HEADER' => '{L_MODULE_MENU}'
		);
		
		$cfg['acp_submenu'][] = array(
			'LINK' => './acp.php?c=menu',
			'HEADER' => '{L_MENU_MAINMENU}',
			'ACTIVE' => (!isset($_GET['mode']) || (isset($_GET['mode']) && $_GET['mode'] == 'edit'))
				? $cfg['tpl']['class_active']
				: ''
		);
		if(permissions('menu','items','add'))
		{
			$cfg['acp_submenu'][] = array(
				'LINK' => './acp.php?c=menu&amp;mode=add',
				'HEADER' => '{L_MENU_ITEM_ADD}',
				'ACTIVE' => (isset($_GET['mode']) && $_GET['mode'] == 'add')
					? $cfg['tpl']['class_active']
					: ''
			);
			$cfg['acp_submenu'][] = array(
				'LINK' => './acp.php?c=menu&amp;mode=add-individual',
				'HEADER' => '{L_MENU_ITEM_ADD_INDIVIDUAL}',
				'ACTIVE' => (isset($_GET['mode']) && $_GET['mode'] == 'add-individual')
					? $cfg['tpl']['class_active']
					: ''
			);
		}
	}
	
	$tpl->queue[1][] = 'global $mod;
	$mod->modules[\'menu\'] = new module_menu(true);';
	
	if(isset($_GET['c']))
	{
		switch($_GET['c'])
		{
			case('menu'):
				if(!isset($_GET['mode']))
				{
					$mod->modules['menu']->breadcrumbs[] = array(
						'LINK' => './acp.php?c=menu',
						'HEADER' => '{L_MENU_MAINMENU}'
					);
					
					$tpl->inc('menu',1);
					
					$tpl->assign('SITE_TITLE','{L_MODULE_MENU} &ndash; {SITE_HEADER}');
					
					$mod->modules['menu']->mainmenu_items();
				}
				else
				{
					switch($_GET['mode'])
					{
						case('add'):
							$mod->modules['menu']->breadcrumbs[] = array(
								'LINK' => './acp.php?c=menu&mode=add',
								'HEADER' => '{L_MENU_ITEM_ADD}'
							);
							
							$tpl->inc('menu_item_add',1);
							$mod->modules['menu']->add_item();
							
							$tpl->assign('SITE_TITLE','{L_MODULE_MENU} &mdash; {L_MENU_ITEM_ADD} &ndash; {SITE_HEADER}');
							
							break;
						
						case('add-individual'):
							$mod->modules['menu']->breadcrumbs[] = array(
								'LINK' => './acp.php?c=menu&mode=add-individual',
								'HEADER' => '{L_MENU_ITEM_ADD_INDIVIDUAL}'
							);
							
							$tpl->inc('menu_item_add_individual',1);
							
							$tpl->assign('SITE_TITLE','{L_MODULE_MENU} &mdash; {L_MENU_ITEM_ADD_INDIVIDUAL} &ndash; {SITE_HEADER}');
							$tpl->assign('ITEM_ACTION','./action.php?c=menu&amp;mode=add-individual');
							break;
						
						case('edit'):
							$mod->modules['menu']->breadcrumbs[] = array(
								'LINK' => './acp.php?c=menu&mode=edit&amp;iid=' . intval($_GET['iid']),
								'HEADER' => '{L_MENU_ITEM_EDIT}: {ITEM.HEADER}'
							);
							
							$tpl->inc('menu_item_edit',1);
					
							$tpl->assign('SITE_TITLE','{L_MODULE_MENU} &ndash; {SITE_HEADER}');
							$tpl->assign('ITEM_ACTION','./action.php?c=menu&amp;mode=edit&amp;iid=' . intval($_GET['iid']));
					
							$mod->modules['menu']->edit_item();
							break;
					}
				}
				
				$tpl->assign('BREADCRUMBS',$mod->modules['menu']->breadcrumbs,'foreach');
				break;
		}
	}
}

if(defined('IN_IMAGES') && IN_IMAGES)
{
	$cfg['tpl']['images']['module_menu'] = 'acp/images/module_menu.png';
}

if(defined('IN_ACTION') && IN_ACTION)
{
	if(isset($_GET['c']))
	{
		switch($_GET['c'])
		{
			case('menu'):
				if(isset($_GET['mode']))
				{
					switch($_GET['mode'])
					{
						case('add-individual'):
							if(permissions('menu','items','add'))
							{
								$sql = new MySQLObject();
								
								$order = mainmenu_getorder();
								if($sql->query("
INSERT INTO " . $sql->table('menu') . "
(`header`,`link`,`module`,`show`,`order`)
VALUES
(
	'" . $sql->escape($_POST['item']['header']) . "',
	'" . $sql->escape($_POST['item']['link']) . "',
	'',
	1,
	" . ($order + 1) . "
)"
								))
								{
									// -- OK --
									$tpl->assign('REDIRECT_LOCATION','./acp.php?c=menu');
									$syslog->alert_success('{L_ALERT_MENU_ITEM_ADD_SUCCESS}');
									die();
								}
								else
								{
									$syslog->alert_error('{L_ALERT_MENU_ITEM_ADD_ERROR}');
									die();
								}
							}
							else
							{
								$syslog->permissions_error('{L_PERMISSIONS_MENU_ITEM_ADD}');
								die();
							}
							break;
						
						case('edit'):
							if(permissions('menu','items','edit'))
							{
								$sql = new MySQLObject();
								if($sql->query("
UPDATE " . $sql->table('menu') . "
SET
	`header` = '" . $sql->escape($_POST['item']['header']) . "',
	`link` = '" . $sql->escape($_POST['item']['link']) . "',
	`show` = " . intval($_POST['item']['show']) . "
WHERE (`iid` = " . intval($_GET['iid']) . ")"
								))
								{
									// -- OK --
									$tpl->assign('REDIRECT_LOCATION','./acp.php?c=menu');
									$syslog->alert_success('{L_ALERT_MENU_ITEM_EDIT_SUCCESS}');
									die();
								}
								else
								{
									$syslog->alert_error('{L_ALERT_MENU_ITEM_EDIT_ERROR}');
									die();
								}
							}
							else
							{
								$syslog->permissions_error('{L_PERMISSIONS_MENU_ITEM_EDIT}');
								die();
							}
							break;
						
						case('delete'):
							if(permissions('menu','items','delete'))
							{
								$sql = new MySQLObject();
								if($sql->query("SELECT `order` FROM " . $sql->table('menu') . " WHERE (`iid` = " . intval($_GET['iid']) . ")"))
								{
									$item = $sql->fetch_one();
									$order = $item->order;
									
									if(mainmenu_reorder($order))
									{
										if($sql->query("DELETE FROM " . $sql->table('menu') . " WHERE (`iid` = " . intval($_GET['iid']) . ")"))
										{
											// -- OK --
											$tpl->assign('REDIRECT_LOCATION','./acp.php?c=menu');
											$syslog->alert_success('{L_ALERT_MENU_ITEM_DELETE_SUCCESS}');
											die();
										}
										else
										{
											$syslog->alert_error('{L_ALERT_MENU_ITEM_DELETE_ERROR}');
											die();
										}
									}
									else
									{
										$syslog->alert_error('{L_ALERT_MENU_ITEM_DELETE_ERROR}');
										die();
									}
								}
								else
								{
									$syslog->alert_error('{L_ALERT_MENU_ITEM_DELETE_ERROR}');
									die();
								}
							}
							else
							{
								$syslog->permissions_error('{L_PERMISSIONS_MENU_ITEM_DELETE}');
								die();
							}
							break;
						
						case('show'):
							if(permissions('menu','items','edit'))
							{
								$sql = new MySQLObject();
								if($sql->query("UPDATE " . $sql->table('menu') . " SET `show` = 1 WHERE (`iid` = " . intval($_GET['iid']) . ")"))
								{
									// -- OK --
									$tpl->assign('REDIRECT_LOCATION','./acp.php?c=menu');
									$syslog->alert_success('{L_ALERT_MENU_ITEM_EDIT_SUCCESS}');
									die();
								}
								else
								{
									$syslog->alert_error('{L_ALERT_MENU_ITEM_EDIT_ERROR}');
									die();
								}
							}
							else
							{
								$syslog->permissions_error('{L_PERMISSIONS_MENU_ITEM_EDIT}');
								die();
							}
							break;
						
						case('hide'):
							if(permissions('menu','items','edit'))
							{
								$sql = new MySQLObject();
								if($sql->query("UPDATE " . $sql->table('menu') . " SET `show` = 0 WHERE (`iid` = " . intval($_GET['iid']) . ")"))
								{
									// -- OK --
									$tpl->assign('REDIRECT_LOCATION','./acp.php?c=menu');
									$syslog->alert_success('{L_ALERT_MENU_ITEM_EDIT_SUCCESS}');
									die();
								}
								else
								{
									$syslog->alert_error('{L_ALERT_MENU_ITEM_EDIT_ERROR}');
									die();
								}
							}
							else
							{
								$syslog->permissions_error('{L_PERMISSIONS_MENU_ITEM_EDIT}');
								die();
							}
							break;
						
						case('move'):
							if(permissions('menu','items','edit') && isset($_GET['dir']))
							{
								$sql = new MySQLObject();
								if($sql->query("SELECT `order` FROM " . $sql->table('menu') . " WHERE (`iid` = " . intval($_GET['iid']) . ")") && $sql->num() > 0)
								{
									$item = $sql->fetch_one();
									if(
										$sql->query("
UPDATE " . $sql->table('menu') . "
SET `order` = " . $item->order . "
WHERE (`order` = "
. (
	($_GET['dir'] == 'up')
	? $item->order - 1
	: $item->order + 1
) . ")")
										&& $sql->query("
UPDATE " . $sql->table('menu') . "
SET `order` = "
. (
	($_GET['dir'] == 'up')
	? $item->order - 1
	: $item->order + 1
) . "
WHERE (`iid` = " . intval($_GET['iid']) . ")")
									)
									{
										// -- OK --
										$tpl->assign('REDIRECT_LOCATION','./acp.php?c=menu');
										$syslog->alert_success('{L_ALERT_MENU_ITEM_EDIT_SUCCESS}');
										die();
									}
									else
									{
										$syslog->alert_error('{L_ALERT_MENU_ITEM_EDIT_ERROR}');
										die();
									}
								}
								else
								{
									$syslog->alert_error('{L_ALERT_MENU_ITEM_EDIT_ERROR}');
									die();
								}
							}
							else
							{
								$syslog->permissions_error('{L_PERMISSIONS_MENU_ITEM_EDIT}');
								die();
							}
							break;
					}
				} 
				break;
		}
	}
}
?>