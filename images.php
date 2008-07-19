<?php
define('IN_IMAGES',true);
if(isset($_GET['image']))
{
	require_once('./config.php');

	if(isset($cfg['tpl']['images']) && is_array($cfg['tpl']['images']))
	{
		$keys = array_keys($cfg['tpl']['images']);
		if(in_array($_GET['image'],$keys))
		{
			global $tpl;
			header('Location: ' . $tpl->dirpath . $cfg['tpl']['images'][$_GET['image']]);
		}
	}
}
?>