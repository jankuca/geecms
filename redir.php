<?php
if(isset($_GET['request']) && $_GET['request'] != '')
{
	define('IN_REDIR',true);
	require('./config.php');
	
	if(defined('LOC'))
	{
		$loc = SITE_ROOT_PATH . 'index.php' . LOC;
		echo(file_get_contents($loc));
	}
	else
	{
		//_error_404();
		header('Location: ' . SITE_ROOT_PATH);
	}
}
else
{
	header('Location: ' . SITE_ROOT_PATH);
}
?>