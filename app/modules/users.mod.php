<?php
function hmac_md5($key, $data) {
    $blocksize = 64;
    if (strlen($key) > $blocksize) {
        $key = pack("H*", md5($key));
    }
    $key = str_pad($key, $blocksize, chr(0x00));
    $k_ipad = $key ^ str_repeat(chr(0x36), $blocksize);
    $k_opad = $key ^ str_repeat(chr(0x5c), $blocksize);
    return md5($k_opad . pack("H*", md5($k_ipad . $data)));
}

function permissions($module,$name,$value)
{
	if(isset($_SESSION['permissions'][$module][$name]) && in_array($value,$_SESSION['permissions'][$module][$name]))
		return(true);
	else
		return(false);
}

class module_users
{
	public $breadcrumbs;
	
	public function __construct()
	{
		global $q,$tpl;

		if(isset($_SESSION['logged'],$_SESSION['uid'],$_SESSION['authkey']) && $_SESSION['logged'])
		{
			$sql = new MySQLObject("SELECT `authkey`,`groups` FROM " . $q->table('users') . " WHERE (`uid` = " . intval($_SESSION['uid']) . ")");
			$user = $sql->fetch_one();
			if($_SESSION['authkey'] == $user->authkey)
			{
				$authkey = $this->authkey();
				$sql->query("UPDATE " . $q->table('users') . " SET `authkey` = '" . $authkey . "' WHERE (`uid` = " . intval($_SESSION['uid']) . ")");
				$_SESSION['authkey'] = $authkey;
				setcookie('authkey',$authkey);

				$_SESSION['groups'] = explode(';',$user->groups);
				$tpl->assign('CURRENT_USER.USERNAME',$_SESSION['username']);
			}
			else
			{
				$this->logout();
			}
			unset($sql);
		}
		else
		{
			if(isset($_COOKIE['authkey']))
			{
				$sql = new MySQLObject();
				$sql->query("SELECT `uid`,`username`,`groups` FROM " . $q->table('users') . " WHERE (`authkey` = '" . $_COOKIE['authkey'] . "')",'user');
				if($sql->num() != 0)
				{
					$authkey = $this->authkey();
					$sql->query("UPDATE " . $q->table('users') . " SET `authkey` = '" . $authkey . "' WHERE (`authkey` = '" . $sql->escape($_COOKIE['authkey']) . "')");
					$_SESSION['authkey'] = $authkey;
					$user = $sql->fetch_one('user');
					setcookie('authkey',$authkey);
					$_SESSION['logged'] = true;
					$_SESSION['uid'] = intval($user->uid);
					$_SESSION['username'] = $user->username;
					$_SESSION['groups'] = explode(';',$user->groups);

					$tpl->assign('CURRENT_USER.USERNAME',$user->username);
				}
				else
				{
					$this->logout();
				}
			}
			else
			{
				$this->logout();
			}
		}
	}
	
	public function logout()
	{
		session_destroy();
		session_start();
		$_SESSION['logged'] = false;
		setcookie('authkey','',time()-60);
		$_SESSION['groups'] = array(-1);
	}
	
	public function authkey()
	{
		return(
			sha1(time())
			. md5($_SERVER['REMOTE_ADDR']
			. rand(0,512))
		);
	}

	public function load_permissions()
	{
		global $cfg,$q;
		$_SESSION['permissions'] = array();

		if(count($_SESSION['groups']) > 0)
		{
			$sql = new MySQLObject();
			$query = "SELECT `name`,`value`,`module` FROM " . $q->table('permissions') . " WHERE (";
			
			for($i = 0; $i < count($_SESSION['groups']); $i++)
			{
				$query .= "`group` = " . trim($_SESSION['groups'][$i]);
				if($i == count($_SESSION['groups']) - 1)
					$query .= ")";
				else
					$query .= " || ";
			}
			if($sql->query($query))
			{
				foreach($sql->fetch() as $item)
				{
					$ps = explode(';',$item->value);
					foreach($ps as $p)
					{
						if(preg_match('#^([0-9]+)$#is',$p))
						{
							if(!isset($_SESSION['permissions'][$item->module][$item->name]) || !in_array(intval($p),$_SESSION['permissions'][$item->module][$item->name]))
							$_SESSION['permissions'][$item->module][$item->name][] = intval($p);
						}
						else
						{
							if(!isset($_SESSION['permissions'][$item->module][$item->name]) || !in_array($p,$_SESSION['permissions'][$item->module][$item->name]))
								$_SESSION['permissions'][$item->module][$item->name][] = $p;
						}
					}
				}
			}
		}
	}

	public function generate_password($length = 8)
	{
		$password = '';
		$possible = '0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/*-+=$';

		for($i = 0; $i < $length; $i++)
		{
			$char = $possible[rand(0,strlen($possible)-1)];
			if(!strstr($password,$char))
			{ 
				$password .= $char;
			}
		}
		return($password);
	}


	public function counts()
	{
		global $q,$tpl;

		$sql = new MySQLObject();
		if($sql->query("SELECT `uid` FROM " . $q->table('users')))
			if(!$sql->num())
				$tpl->assign('USERS_COUNT','0');
			else
				$tpl->assign('USERS_COUNT',$sql->num());
		
		if($sql->query("SELECT `gid` FROM " . $q->table('users_groups')))
			if(!$sql->num())
				$tpl->assign('GROUPS_COUNT','0');
			else
				$tpl->assign('GROUPS_COUNT',$sql->num());
	}


	public function choose_permissions($edit = false)
	{
		global $cfg,$tpl,$q;
		
		// get the group's permissions
		if($edit)
		{
			$group_perm = array();
			$sql = new MySQLObject();
			$sql->query("SELECT `name`,`module`,`value` FROM " . $q->table('permissions') . " WHERE (`group` = " . intval($_GET['gid']) . ")");
			foreach($sql->fetch() as $perm)
			{
				$values = explode(';',$perm->value);
				foreach($values as $value)
				{
					if(preg_match('#^([0-9]+)$#is',$value))
						$group_perm[$perm->module][$perm->name][] = intval($value);
					else
						$group_perm[$perm->module][$perm->name][] = $value;
				}
			}
		}

		$f_modules = array();
		$f_rows = array();
		$f_cols = array();
		if(isset($cfg['permissions']) && is_array($cfg['permissions']))
		{
			foreach($cfg['permissions'] as $module => $names)
			{
				$f_modules[] = array(
					'CP_MODULE_HEADER' => '{L_MODULE_' . strtoupper($module) . '}',
					'CP_MODULE_NAME' => $module
				);
				
				$f_rows[$module] = array();

				if(is_array($names))
				{
					foreach($names as $name => $values)
					{
						$f_rows[$module][] = array(
							'CP_NAME_HEADER' => '{L_PERMISSIONS_HEADER.' . $name . '}',
							'CP_NAME_NAME' => $name
						);

						$f_cols[$name] = array();
						if(is_array($values))
						{
							foreach($values as $value)
							{
								$f_cols[$name][] = array(
									'CP_VALUE_NAME' => $value,
									'CP_VALUE_HEADER' => '{L_PERMISSIONS_VALUE.' . $value . '}',
									'CP_VALUE_CHECKED' => 
										(!$edit)
										?	''
										:	(isset($group_perm[$module][$name]) && (preg_match('#^([0-9]+)$#is',$value)) && in_array(intval($value),$group_perm[$module][$name]))
											?	'checked="checked" '
											:	(isset($group_perm[$module][$name]) && in_array($value,$group_perm[$module][$name]))
												?	'checked="checked" '
												:	''
								);
							}
						}
						$tpl->assign('CP_VALUES.' . $name,$f_cols[$name],'foreach');
					}
				}
				$tpl->assign('CP_NAMES.' . $module,$f_rows[$module],'foreach');
			}
		}
		$tpl->assign('CP_MODULES',$f_modules,'foreach');
	}

	public function groups($mode,$edit = false)
	{
		global $q,$tpl;
		
		$f_groups = array();

		$sql = new MySQLObject();

		if($edit && isset($_GET['uid']))
		{
			$user_groups = array();

			if($sql->query("SELECT `groups` FROM " . $q->table('users') . " WHERE (`uid` = " . intval($_GET['uid']) . ")"))
			{
				$user = $sql->fetch_one();
				$groups = explode(';',$user->groups);
				foreach($groups as $group)
					$user_groups[] = intval($group);
			}
		}

		if($sql->query("SELECT `gid`,`name`,`description` FROM " . $q->table('users_groups') . " ORDER BY `name` ASC"))
		{
			foreach($sql->fetch() as $group)
			{
				$f_groups[] = array(
					'GROUP_ID' => $group->gid,
					'GROUP_HEADER' => $group->name,
					'GROUP_DESCRIPTION' => $group->description,
					'CHECKED' => ($edit && isset($_GET['uid']) && in_array($group->gid,$user_groups))
					?	'checked="checked" '
					:	'',
					'GROUP_LINK_READ' => './acp.php?c=users&amp;section=group&amp;mode=read&amp;gid=' . $group->gid,
					'GROUP_LINK_EDIT' => './acp.php?c=users&amp;section=group&amp;mode=edit&amp;gid=' . $group->gid,
					'GROUP_LINK_DELETE' => './action.php?c=users&amp;section=group&amp;mode=delete&amp;gid=' . $group->gid,
				);
			}
		}

		if(count($f_groups) > 0)
		{
			$tpl->assign('USER_GROUPS',true,'if');
			$tpl->assign('USER_GROUPS',$f_groups,'foreach');
		}
		else
			$tpl->assign('USER_GROUPS',false,'if');
	}

	public function user()
	{
		global $q,$tpl;

		$sql = new MySQLObject();
		if($sql->query("SELECT `username`,`email` FROM " . $q->table('users') . " WHERE (`uid` = " . intval($_GET['uid']) . ")"))
		{
			$user = $sql->fetch_one();
			$tpl->assign(array(
				'USER.USERNAME' => $user->username,
				'USER.EMAIL' => $user->email
			));
		}
	}

	public function group()
	{
		global $q,$tpl;

		$sql = new MySQLObject();
		if($sql->query("SELECT `name`,`description` FROM " . $q->table('users_groups') . " WHERE (`gid` = " . intval($_GET['gid']) . ")"))
		{
			$group = $sql->fetch_one();
			$tpl->assign(array(
				'GROUP.HEADER' => $group->name,
				'GROUP.DESCRIPTION' => $group->description,
			));
		}
	}

	public function group_edit()
	{
		global $cfg,$q;

		$count = 0;

		// get the changed permissions
		foreach($cfg['permissions'] as $module => $names)
		{
			foreach($names as $name => $values)
			{
				if(isset($_POST['group_permissions'][$module][$name]))
					$out[$module][$name] = implode(';',$_POST['group_permissions'][$module][$name]);
				else
					$out[$module][$name] = '';
				$count++;
			}
		}

		// get the old permissions
		$sql = new MySQLObject();
		$sql->query("SELECT `name`,`module` FROM " . $q->table('permissions') . " WHERE (`group` = " . intval($_GET['gid']) . ")");
		$to_update = array();
		foreach($sql->fetch() as $perm)
		{
			$to_update[$perm->module][$perm->name] = true;
		}

		// update/insert the changed permissions

		$query = "INSERT INTO " . $q->table('permissions') . " (`name`,`group`,`module`,`value`) VALUES";
		$i = 0;
		foreach($out as $module => $names)
		{
			foreach($names as $name => $value)
			{
				if(isset($to_update[$module][$name]))
					$sql->query("UPDATE " . $q->table('permissions') . " SET `value` = '" . $sql->escape($value) . "' WHERE (`module` = '" . $module . "' AND `name` = '" . $name . "' AND `group` = " . intval($_GET['gid']) . ")");
				else
				{
					$query .= " ('" . $name . "'," . intval($_GET['gid']) . ",'" . $module . "','" . $sql->escape($value) . "')";
					if($i != $count - count($to_update) - 1)
					{
						$query .= ",";
					}
					$i++;
				}
			}
		}
		
		if($i != 0)
			$sql->query($query);

		global $syslog,$tpl,$action;
		if(!$action)
		{
			$action = true;

			$tpl->assign('REDIRECT_LOCATION','./acp.php?c=users');
			$tpl->load('alert_success');
			$tpl->inc('alert_success');
			$tpl->assign('ALERT_SUCCESS_MESSAGE','{L_ALERT_USERS_GROUP_EDIT_SUCCESS}');
		}
	}

	public function userlist()
	{
		global $q,$tpl;

		$p = new Pages();
		$p->url = './acp.php?c=users&amp;section=users&amp;mode=userlist&amp;page=%page';
		$p->per_page = 50;
		$p->query = "SELECT `uid`,`username` FROM " . $q->table('users') . " ORDER BY `uid` ASC";

		$f_users = array();

		if($p->make())
		{
			foreach($p->fetch() as $user)
			{
				$f_users[] = array(
					'USER_ID' => $user->uid,
					'USER_USERNAME' => $user->username,
					'USER_LINK_READ' => './acp.php?c=users&amp;section=user&amp;mode=read&amp;uid=' . $user->uid,
					'USER_LINK_EDIT' => './acp.php?c=users&amp;section=user&amp;mode=edit&amp;uid=' . $user->uid,
					'USER_LINK_DELETE' => './action.php?c=users&amp;section=user&amp;mode=delete&amp;uid=' . $user->uid,
				);
			}
		}

		$tpl->assign('USERS_USERLIST',$f_users,'foreach');
	}
}

session_start();
//var_dump($_SESSION);
global $cfg;
$cfg['permissions']['users']['user'] = array('read','edit','delete','add','change_groups');
$cfg['permissions']['users']['group'] = array('read','edit','delete','add');

global $mod;
$mod->modules['users'] = new module_users();
$mod->modules['users']->load_permissions();

global $tpl,$syslog;
$tpl->assign('U_LOGOUT','./login.php?logout');

if(defined('IN_ACP') && IN_ACP)
{
	if(!$_SESSION['logged'])
	{
		header('Location: ./loginbox.php');
		die();
	}

	global $cfg;
	$cfg['acp_modules_menu'][] = array(
		'LINK' => './acp.php?c=users',
		'HEADER' => '{L_MODULE_USERS}',
		'ACTIVE' => (isset($_GET['c']) && $_GET['c'] == 'users')
		? $cfg['tpl']['class_subactive'] : ''
	);
	$tpl->assign('MODULE_USERS_IMAGE','./images.php?image=module_users');

	global $tpl;
	
	if(!isset($_COOKIE['authkey']))
	{
		$tpl->assign('INFOBAR',true,'if');
		$tpl->assign('INFOBAR','{L_INFOBAR_COOKIES_DISABLED}');
	}
	else
		$tpl->assign('INFOBAR',false,'if');

	if(isset($_GET['c']))
	{
		switch($_GET['c'])
		{
			case('users'):
				//--f breadcrumbs ]---
				$mod->modules['users']->breadcrumbs[] = array(
					'LINK' => './acp.php?c=users',
					'HEADER' => '{L_MODULE_USERS}'
				);
				
				//--[ module's menu ]---
				$cfg['acp_submenu'][] = array(
					'LINK' => './acp.php?c=users',
					'HEADER' => '{L_OVERVIEW}',
					'ACTIVE' => (!isset($_GET['section']))
					? $cfg['tpl']['class_active'] : ''
				);
				
				$cfg['acp_submenu'][] = array(
					'LINK' => './acp.php?c=users&amp;section=users&amp;mode=userlist',
					'HEADER' => '{L_USERS_USERS}',
					'ACTIVE' => (isset($_GET['section']) && ((isset($_GET['mode']) && ($_GET['section'] == 'user') && ($_GET['mode'] == 'edit')) || ($_GET['section'] == 'users')))
					? $cfg['tpl']['class_active'] : ''
				);
				
				if(isset($_SESSION['permissions']['users']['user']) && in_array('add',$_SESSION['permissions']['users']['user']))
				{
					$cfg['acp_submenu'][] = array(
						'LINK' => './acp.php?c=users&amp;section=user&amp;mode=add',
						'HEADER' => '{L_USERS_USER_ADD}',
						'ACTIVE' => (isset($_GET['section'],$_GET['mode']) && ($_GET['section'] == 'user') && ($_GET['mode'] == 'add'))
						? $cfg['tpl']['class_active'] : ''
					);
				}

				$cfg['acp_submenu'][] = array(
					'LINK' => './acp.php?c=users&amp;section=groups',
					'HEADER' => '{L_USERS_GROUPS}',
					'ACTIVE' => (isset($_GET['section']) && ((isset($_GET['mode']) && ($_GET['section'] == 'group') && ($_GET['mode'] == 'edit')) || ($_GET['section'] == 'groups')))
					? $cfg['tpl']['class_active'] : ''
				);

				if(isset($_SESSION['permissions']['users']['group']) && in_array('add',$_SESSION['permissions']['users']['group']))
				{
					$cfg['acp_submenu'][] = array(
						'LINK' => './acp.php?c=users&amp;section=group&amp;mode=add',
						'HEADER' => '{L_USERS_GROUP_ADD}',
						'ACTIVE' => (isset($_GET['section'],$_GET['mode']) && ($_GET['section'] == 'group') && ($_GET['mode'] == 'add'))
						? $cfg['tpl']['class_active'] : ''
					);
				}
				//----

				if(!isset($_GET['section']))
				{
					$tpl->inc('users',1);

					$tpl->assign('SITE_TITLE','{L_MODULE_USERS} &ndash; {SITE_HEADER} / {L_ACP}');

					$mod->modules['users']->counts();
				}
				else
				{
					switch($_GET['section'])
					{
						case('user'):
							if(isset($_GET['mode']))
							{
								switch($_GET['mode'])
								{
									case('add'):
										$mod->modules['users']->breadcrumbs[] = array(
											'LINK' => './acp.php?c=users&amp;section=user&amp;mode=add',
											'HEADER' => '{L_USERS_USER_ADD}'
										);
										
										$tpl->inc('users_user_add',1);

										$tpl->assign('SITE_TITLE','{L_MODULE_USERS} &mdash; {L_USERS_USER_ADD} &ndash; {SITE_HEADER} / {L_ACP}');
										$tpl->assign('USER_ACTION','./action.php?c=users&amp;section=user&amp;mode=add');

										$mod->modules['users']->groups('read');
										break;

									case('edit'):
										if(isset($_GET['uid']))
										{
											$mod->modules['users']->breadcrumbs[] = array(
												'LINK' => './acp.php?c=users&amp;section=user&amp;mode=edit&amp;uid=' . intval($_GET['uid']),
												'HEADER' => '{L_USERS_USER_EDIT}: {USER.USERNAME}'
											);
											$tpl->inc('users_user_edit',1);

											$tpl->assign('SITE_TITLE','{L_MODULE_USERS} &mdash; {L_USERS_USER_EDIT}: {USER.USERNAME} &ndash; {SITE_HEADER} / {L_ACP}');
											$tpl->assign('USER_ACTION','./action.php?c=users&amp;section=user&amp;mode=edit&amp;uid=' . intval($_GET['uid']));

											$mod->modules['users']->groups('read',true);
											$mod->modules['users']->user();
										}
										break;
								}
							}
							else
								header('Location: ./acp.php?c=users');
							break;

						case('users'):
							if(isset($_GET['mode']))
							{
								switch($_GET['mode'])
								{
									case('userlist'):
										$mod->modules['users']->breadcrumbs[] = array(
											'LINK' => './acp.php?c=users&amp;section=users&amp;mode=userlist',
											'HEADER' => '{L_USERS_USERS}'
										);
										
										$tpl->inc('users_userlist',1);
										
										$tpl->assign('SITE_TITLE','{L_MODULE_USERS} &mdash; {L_USERS_USERS} &ndash; {SITE_HEADER} / {L_ACP}');
										
										$mod->modules['users']->userlist();
										break;
								}
							}
							break;

						case('group'):
							if(isset($_GET['mode']))
							{
								switch($_GET['mode'])
								{
									case('add'):
										$mod->modules['users']->breadcrumbs[] = array(
											'LINK' => './acp.php?c=users&amp;section=group&amp;mode=add',
											'HEADER' => '{L_USERS_GROUP_ADD}'
										);
										
										$tpl->inc('users_group_add',1);

										$tpl->assign('SITE_TITLE','{L_MODULE_USERS} &mdash; {L_USERS_GROUP_ADD} &ndash; {SITE_HEADER} / {L_ACP}');
										$tpl->assign('GROUP_ACTION','./action.php?c=users&amp;section=group&amp;mode=add');

										$tpl->queue[0][] = 'global $mod;
										$mod->modules[\'users\']->choose_permissions();';
										break;
									
									case('edit'):
										if(isset($_GET['gid']))
										{
											$mod->modules['users']->breadcrumbs[] = array(
												'LINK' => './acp.php?c=users&amp;section=group&amp;mode=edit&amp;gid=' . intval($_GET['gid']),
												'HEADER' => '{L_USERS_GROUP_EDIT}: {GROUP.HEADER}'
											);
											
											$tpl->inc('users_group_edit',1);

											$tpl->assign('SITE_TITLE','{L_MODULE_USERS} &mdash; {L_USERS_GROUP_EDIT}: {GROUP.HEADER} &ndash; {SITE_HEADER} / {L_ACP}');
											$tpl->assign('GROUP_ACTION','./action.php?c=users&amp;section=group&amp;mode=edit&amp;gid=' . intval($_GET['gid']));

											$mod->modules['users']->group();
											$tpl->queue[0][] = 'global $mod;
											$mod->modules[\'users\']->choose_permissions(true);';
										}
										break;
								}
							}
							else
							{
								header('Location: ./acp.php?c=users');
							}
							break;

						case('groups'):
							$mod->modules['users']->breadcrumbs[] = array(
								'LINK' => './acp.php?c=users&amp;section=user&amp;mode=add',
								'HEADER' => '{L_USERS_GROUPS}'
							);
							$tpl->inc('users_groups',1);

							$tpl->assign('SITE_TITLE','{L_MODULE_USERS} &mdash; {L_USERS_GROUPS} &ndash; {SITE_HEADER} / {L_ACP}');

							$mod->modules['users']->groups('edit');
							break;
					}
				}
				break;
		}
	}
	else
	{
		$cfg['installed_modules'][] = array(
			'MODULE_HEADER' => '{L_MODULE_USERS}',
			'MODULE_DESCRIPTION' => '{L_MODULE_USERS_DESCRIPTION}',
			'MODULE_LINK' => './acp.php?c=users',
			'MODULE_IMAGE' => '{MODULE_USERS_IMAGE}'
		);
	}
	
	$tpl->assign('BREADCRUMBS',$mod->modules['users']->breadcrumbs,'foreach');
}

if(defined('IN_LOGINBOX') && IN_LOGINBOX)
{
	global $tpl,$q;
	$tpl->load('loginbox',1);
	$tpl->inc('loginbox',1);
	
	$sql = new MySQLObject();
	$sql->query("DELETE FROM " . $q->table('login_challenges') . " WHERE (`ip` = '" . $_SERVER['REMOTE_ADDR'] . "')");
	$sql->query("INSERT INTO " . $q->table('login_challenges') . " (`date`,`ip`) VALUES (NOW(),'" . $_SERVER['REMOTE_ADDR'] . "')");
	if($sql->insert_id() != 0)
	{
		$tpl->assign('LOGIN_CHALLENGE',$sql->insert_id());
		unset($sql);
	}
	else
	{
		$syslog->error('login','challenge','Challenge has not been set! Security error!');
		die();
	}
	
	$tpl->assign('SITE_TITLE','{L_LOGIN_WELCOME} &mdash; {SITE_HEADER}');
	$tpl->assign('LOGIN_ACTION','./login.php');
}

if(defined('IN_LOGIN') && IN_LOGIN)
{
	if(isset($_GET['logout']))
	{
		$mod->modules['users']->logout();
		header('Location: ./acp.php');
	}
	
	
	if(!$_SESSION['logged'])
	{
		if(isset($_POST['login_username'],$_POST['login_password_md5'],$_POST['login_challenge']))
		{
			global $q,$syslog;
			$sql = new MySQLObject();
			$sql->query("DELETE FROM " . $q->table('login_challenges') . " WHERE (`chid` = " . intval($_POST['login_challenge']) . " AND `ip` = '" . $_SERVER['REMOTE_ADDR'] . "')");
			if(!$sql->affected())
			{
				header('Location: ./loginbox.php');
			}
			else
			{
				$sql->query("DELETE FROM " . $q->table('login_challenges') . " WHERE (`ip` = '" . $_SERVER['REMOTE_ADDR'] . "')");
				$sql->query("SELECT `uid`,`username`,`password` FROM " . $q->table('users') . " WHERE (`username` = '" . $sql->escape($_POST['login_username']) . "')");
				if($sql->num())
				{
					$user = $sql->fetch_one();
					if($_POST['login_password_md5'] == $user->password)
					{
						session_destroy();
						session_start();
						$authkey = $mod->modules['users']->authkey();
						if($sql->query("UPDATE " . $q->table('users') . " SET `authkey` = '" . $authkey . "' WHERE (`uid` = " . $user->uid . ")"))
						{
							$_SESSION['authkey'] = $authkey;
							setcookie('authkey',$authkey);
							$_SESSION['logged'] = true;
							$_SESSION['uid'] = intval($user->uid);
							$_SESSION['username'] = $user->username;

							$tpl->assign('CURRENT_USER.USERNAME',$user->username);

							$tpl->assign('REDIRECT_LOCATION','./acp.php');
							$syslog->alert_success('{L_ALERT_USERS_LOGIN_SUCCESS}');
						}
						else
						{
							$syslog->error('login','authkey',mysql_error());
						}
					}
					else{
						$syslog->alert_error('{L_ALERT_USERS_PASSWORD_WRONG}');
						die();
					}
				}
				else
				{
					$syslog->alert_error('{L_ALERT_USERS_PASSWORD_WRONG}');
					die();
				}
			}
		}
		else
			header('Location: ./loginbox.php');
	}
	else
		header('Location: ./acp.php');
}

if(defined('IN_IMAGES') && IN_IMAGES)
{
	global $cfg;
	$cfg['tpl']['images']['module_users'] = 'acp/images/module_users.png';

	$cfg['tpl']['images']['password_strength_weak'] = 'images/password_strength_weak.png';
	$cfg['tpl']['images']['password_strength_normal'] = 'images/password_strength_normal.png';
	$cfg['tpl']['images']['password_strength_strong'] = 'images/password_strength_strong.png';

	$cfg['tpl']['images']['error_alert'] = 'images/error_alert.png';
}

if(defined('IN_ACTION') && IN_ACTION)
{
	if(isset($_GET['c']))
	{
		global $q;
		switch($_GET['c'])
		{
			case('users'):
				if(isset($_GET['section']))
				{
					switch($_GET['section'])
					{
						case('user'):
							if(isset($_GET['mode']))
							{
								switch($_GET['mode'])
								{
									case('add'):
										if(permissions('users','user','add'))
										{
											if(!isset($_POST['user_email']) || $_POST['user_email'] == '')
											{
												$syslog->alert_error('{L_ALERT_USERS_USER_EMAIL_REQUIRED}');
												die();
											}

											if(isset($_POST['user_password_generate'],$_POST['user_username'],$_POST['user_password_md5']))
											{
												$sql = new MySQLObject();

												if(intval($_POST['user_password_generate']) == 1)
													$password = $mod->modules['users']->generate_password();
												else
												{
													if($_POST['user_password'] != $_POST['user_password_confirm'])
													{
														$syslog->alert_error('{L_USERS_USER_PASSWORD_MATCH}');
														die();
													}
													$password = $_POST['user_password'];
												}
												$password_md5 = md5($password);

												if(
													isset($_SESSION['premissions']['users']['user'])
													&& is_array($_SESSION['premissions']['users']['group'])
													&& in_array('change_groups',$_SESSION['premissions']['users']['group'])
													&& isset($_POST['user_groups'])
													&& is_array($_POST['user_groups'])
												)
													$groups = implode(';',$_POST['user_groups']);
												else
													$groups = '';

												if($sql->query("INSERT INTO " . $q->table('users') . " (`username`,`password`,`email`,`groups`) VALUES ('" . $sql->escape($_POST['user_username']) . "','" . $password_md5 . "','" . $sql->escape($_POST['user_email']) . "','" . $sql->escape($groups) . "')"))
												{
													mail(
														$_POST['user_email'],
														$tpl->assign['L_EMAIL_SUBJECT_USERS_USER_REGISTRATION'],
														'<html>' . "\r\n" .
														'<head>' . "\r\n" .
														'	<title>' . $tpl->assign['L_EMAIL_SUBJECT_USERS_USER_REGISTRATION'] . '</title>' . "\r\n" .
														'</head>' . "\r\n" .
														'<body>' . "\r\n" .
														'<h1 style="color: #069; font-size: 150%; font-family: Calibri,\'Segoe Ui\',\'Trebuchet MS\',Arial,sans-serif">' . $tpl->assign['SITE_HEADER'] . ' ' . $tpl->assign['SITE_SLOGAN'] . '</h1>' . "\r\n" .
														'<p>' . $tpl->assign['L_EMAIL_USERS_USER_REGISTRATION'] . '</p>' . "\r\n" .
														'<label style="float: left; width: 120px; text-align: right">' . $tpl->assign['L_USERS_USER_USERNAME'] . ':&nbsp;</label> <strong>' . $_POST['user_username'] . '</strong><br />' . "\r\n" .
														'<label style="float: left; width: 120px; text-align: right">' . $tpl->assign['L_USERS_USER_PASSWORD'] . ':&nbsp;</label> <strong>' . $password . '</strong><br />' . "\r\n" .
														'</body>' . "\r\n" .
														'</html>' . "\r\n",
														'MIME-Version: 1.0' . "\r\n" .
														'Content-type: text/html; charset=utf-8' . "\r\n" .
														'From: ' . $tpl->assign['SITE_HEADER'] . ' <' . $tpl->assign['SITE_EMAIL'] . '>' . "\r\n"
													);

													$tpl->assign('REDIRECT_LOCATION','./acp.php?c=users');
													$tpl->assign('NO_MENU',true,'if');
													$syslog->alert_success('{L_ALERT_USERS_USER_ADD_SUCCESS}');
													die();
												}
												else
												{
													$syslog->alert_error('{L_ALERT_USERS_USER_ADD_ERROR}');
													die();
												}
											}
										}
										else
										{
											$syslog->permissions_error('{L_PERMISSIONS_USERS_USER_ADD}');
											die();
										}
										break;

									case('edit'):
										if(permissions('users','user','edit') || $_GET['uid'] == $_SESSION['uid'])
										{
											if(isset($_GET['uid'],$_POST['user_username'],$_POST['user_email']))
											{
												$sql = new MySQLObject();
												$query = "UPDATE " . $q->table('users') . " SET `username` = '" . $sql->escape($_POST['user_username']) . "',`email` = '" . $sql->escape($_POST['user_email']) . "'";
												if(permissions('users','user','change_groups'))
												{
													if(isset($_POST['user_groups']) && is_array($_POST['user_groups']))
														$query .= ",`groups` = '" . $sql->escape(implode(';',$_POST['user_groups'])) . "'";
													else
														$query .= ",`groups` = ''";
												}
												$query .= " WHERE (`uid` = " . intval($_GET['uid']) . ")";
//echo($query);die();
												if($sql->query($query))
												{
													$tpl->assign('REDIRECT_LOCATION','./acp.php?c=users&section=user&mode=read&uid=' . intval($_GET['uid']));
													$syslog->alert_success('{L_ALERT_USERS_USER_EDIT_SUCCESS}');
													die();
												}
												else
												{
													$syslog->alert_error('{L_ALERT_USERS_USER_EDIT_ERROR}');
													die();
												}
											}
										}
										else
										{
											$syslog->permissions_error('{L_PERMISSIONS_USERS_USER_EDIT}');
											die();
										}
										break;

									case('delete'):
										if(permissions('users','user','delete'))
										{
											if(isset($_GET['uid']))
											{
												$sql = new MySQLObject();
												if($sql->query("DELETE FROM " . $q->table('users') . " WHERE (`uid` = " . intval($_GET['uid']) . ")"))
												{
													$tpl->assign('REDIRECT_LOCATION','./acp.php?c=users&section=users&mode=userlist');
													$syslog->alert_success('{L_ALERT_USERS_USER_DELETE_SUCCESS}');
													die();
												}
												else
												{
													$syslog->alert_error('{L_ALERT_USERS_USER_DELETE_ERROR}');
													die();
												}
											}
										}
										else
										{
											$syslog->permissions_error('{L_PERMISSIONS_USERS_USER_DELETE}');
											die();
										}
										break;
								}
							}
							break;

						case('group'):
							if(isset($_GET['mode']))
							{
								switch($_GET['mode'])
								{
									case('add'):
										if(permissions('users','group','add'))
										{
											$sql = new MySQLObject();

											if($sql->query("INSERT INTO " . $q->table('users_groups') . " (`name`,`description`) VALUES ('" . $sql->escape($_POST['group_header']) . "','" . $sql->escape($_POST['group_description']) . "')"))
											{
												if(isset($_POST['group_permissions']) && is_array($_POST['group_permissions']))
												{
													$query = "INSERT INTO " . $q->table('permissions') . " (`name`,`group`,`module`,`value`) VALUES";
													foreach($_POST['group_permissions'] as $module => $names)
													{
														if(is_array($names))
														{
															$o = 0;
															foreach($names as $name => $values)
															{
																$query .= " ('" . $sql->escape($name) . "'," . $sql->insert_id() . ",'" . $sql->escape($module) . "','";
																if(is_array($values))
																	$query .= implode(';',$values);
																$query .= "')";
																if($o != count($names) - 1)
																	$query .= ",";
																$o++;
															}
														}
													}
													if(!$sql->query($query))
													{
														$syslog->alert_error('{L_ALERT_USERS_PERMISSIONS_SET}');
														die();
													}
												}

												$tpl->assign('REDIRECT_LOCATION','./acp.php?c=users&section=group&mode=read&uid=' . $sql->insert_id());
												$syslog->alert_success('{L_ALERT_USERS_GROUP_ADD_SUCCESS}');
												die();
											}
											else
											{
												$syslog->alert_error('{L_ALERT_USERS_GROUP_ADD_ERROR}');
												die();
											}
										}
										else
										{
											$syslog->permissions_error('{L_PERMISSIONS_USERS_GROUP_ADD}');
											die();
										}
										break;

									case('edit'):
										if(permissions('users','group','edit'))
										{
											$sql = new MySQLObject();
											$sql->query("UPDATE " . $q->table('users_groups') . " SET `name` = '" . $sql->escape($_POST['group_header']) . "',`description` = '" . $sql->escape($_POST['group_description']) . "' WHERE (`gid` = " . intval($_GET['gid']) . ")");

											global $tpl;
											$tpl->queue[0][] = '
											global $mod;
											$mod->modules[\'users\']->group_edit();';
										}
										else
										{
											$syslog->permissions_error('{L_PERMISSIONS_USERS_GROUP_EDIT}');
											die();
										}
										break;
									
									case('delete'):
										if(permissions('users','group','delete'))
										{
											if(isset($_GET['gid']))
											{
												$sql = new MySQLObject();
												if($sql->query("DELETE FROM " . $q->table('users_groups') . " WHERE (`gid` = " . intval($_GET['gid']) . ")"))
												{
													$tpl->assign('REDIRECT_LOCATION','./acp.php?c=users&section=groups');
													$syslog->alert_success('{L_ALERT_USERS_GROUP_DELETE_SUCCESS}');
													die();
												}
												else
												{
													$syslog->alert_error('{L_ALERT_USERS_GROUP_DELETE_ERROR}');
													die();
												}
											}
										}
										else
										{
											$syslog->permissions_error('{L_PERMISSIONS_USERS_GROUP_DELETE}');
											die();
										}
										break;
								}
							}
							break;
					}
				}
				break;
		}
	}
}
?>