<?php
function sendPostRequest($url, $data, $optional_headers = null)
{
	$params = array(
		'http' => array(
			'method' => 'POST',
			'content' => $data
		)
	);
	
	if($optional_headers !== null)
		$params['http']['header'] = $optional_headers;
	
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if(!$fp)
		die('ERROR');
	
	$response = @stream_get_contents($fp);
	if($response === false)
	   die('ERROR');
	
	return($response);
}

class module_updater
{
	
}

global $mod,$cfg;
$mod->modules['updater'] = new module_updater();

if(defined('IN_IMAGES') && IN_IMAGES)
{
	$cfg['tpl']['images']['module_updater'] = '../../styles/default/acp/images/module_updater.png';
	$cfg['tpl']['images']['module_updater_small'] = '../../styles/default/acp/images/module_updater_small.gif';
}

if(defined('IN_ACP') && IN_ACP)
{
	global $cfg,$tpl;
	
	$tpl->queue[0][] = 'global $cfg;
	$cfg[\'acp_modules_menu\'][] = array(
		\'LINK\' => \'./acp.php?c=updater\',
		\'HEADER\' => \'{L_MODULE_UPDATER} <img src="./images.php?image=module_updater_small" alt="" style="position: absolute; top: 6px; right: 10px;" />\',
		\'ACTIVE\' => (isset($_GET[\'c\']) && $_GET[\'c\'] == \'updater\')
		? $cfg[\'tpl\'][\'class_active\'] : \'\'
	);';
	
	if(!isset($_GET['c']))
	{
		global $cfg;
		$cfg['installed_modules'][] = array(
			'MODULE_HEADER' => '{L_MODULE_UPDATER}',
			'MODULE_DESCRIPTION' => '{L_MODULE_UPDATER_DESCRIPTION}',
			'MODULE_LINK' => './acp.php?c=updater',
			'MODULE_IMAGE' => './images.php?image=module_updater'
		);
	}
	elseif($_GET['c'] == 'updater')
	{
		$tpl->inc('updater',1);
	}
}

if(
	defined('IN_AJAXREQUEST') && IN_AJAXREQUEST
	&& isset($_GET['c'],$_GET['function'])
	&& $_GET['c'] == 'updater'
){
	switch($_GET['function'])
	{
		case('check_for_updates'):
			$sql = new MySQLObject();
			if(!$sql->query("SELECT `code` FROM " . $sql->table('updates') . " ORDER BY `date` DESC"))
				echo('ERROR');
			else
			{
				$xml = 
'<?xml version="1.0" encoding="utf-8"?>
<root>
	<modules>';
				foreach($mod->modules as $name => $module)
				{
					$xml .= '
		<module>' . $name . '</module>';
				}
				$xml .= '
	</modules>
	<installed>';
				foreach($sql->fetch() as $update)
				{
					$xml .= '
		<update code="' . $update->code . '" />';
				}
				$xml .= '
	</installed>
</root>';
			
				$data = array(
					'XML' => $xml
				);
				
				$response = sendPostRequest('http://cmsupdate.blackpig.cz/updates_global.php?function=check_for_updates',http_build_query($data));
				
				if($response == 'ERROR')
					echo('ERROR');
				elseif($response == 'NO_UPDATES')
					echo('NO_UPDATES');
				else
				{
					header('Content-type: text/xml;charset=utf-8');
					print($response);
				}
			}
			break;
		
		case('install_update'):
			if(isset($_GET['code']) && $_GET['code'] != '')
			{
				$xml = 
'<?xml version="1.0" encoding="utf-8"?>
<root>
	<update>
		<param type="code" value="' . $_GET['code'] . '" />
	</update>
	<ftp>
		<param type="server" value="' . $cfg['etc']['ftp_server'] . '" />
		<param type="port" value="' . $cfg['etc']['ftp_port'] . '" />
		<param type="username" value="' . $cfg['etc']['ftp_username'] . '" />
		<param type="password" value="' . $cfg['etc']['ftp_password'] . '" />
		<param type="start" value="' . $cfg['etc']['ftp_start'] . '" />
	</ftp>
</root>';
				$data = array(
					'XML' => $xml
				);
				
				$response = sendPostRequest('http://cmsupdate.blackpig.cz/updates_global.php?function=request_update',http_build_query($data));
				
				if($response == 'ERROR')
					print('ERROR');
				else
				{
					$sql = new MySQLOBject();
					if($sql->query("
INSERT INTO " . $sql->table('updates') . "
(`code`,`date`)
VALUES
('" . $sql->escape($_GET['code']) . "'," . time() . ")"))
						print('OK');
					else
						print('ERROR');
				}
			}
			break;
		
		case('ignore_update'):
			if(isset($_GET['code']) && $_GET['code'] != '')
			{
				$sql = new MySQLOBject();
				if($sql->query("
INSERT INTO " . $sql->table('updates') . "
(`code`,`date`)
VALUES
('" . $sql->escape($_GET['code']) . "'," . time() . ")"))
					print('OK');
				else
					print('ERROR');
			}
			break;
	}
}
?>