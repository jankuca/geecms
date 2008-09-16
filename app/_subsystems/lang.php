<?php
class subsystem_lang
{
	public $dirpath = './langs/';
	
	public function load()
	{
		global $cfg,$syslog,$tpl;
		
		$path = $this->dirpath . $cfg['etc']['lang'] . '/';
		if(file_exists($path))
		{
			$dir = dir($path);
			while($file = $dir->read())
			{
				if($file != '.' && $file != '..')
				{
					$e = explode('.',$file);
					if($e[1] == 'lang' && $e[2] == 'php')
					{
						include($path . $file);
						$syslog->success('lang','load',$path . $file);
					}
				}
			}
		}
		else
		{
			$syslog->error('lang','load','The lang directory does not exist.');
		}
	}
}

$lang = new subsystem_lang();
?>