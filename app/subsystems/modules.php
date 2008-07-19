<?php
class subsystem_modules
{
	public $dirpath = array(
		'./app/modules/',
		'./modules/'
	);
	
	public function load()
	{
		global $syslog;
		
		$loaded = array();
		$path = $this->dirpath[0] . 'order.cfg';
		if(file_exists($path))
		{
			$o = fopen($path,'r');
			$order = fread($o,filesize($path));
			$order = explode('
',$order);
			foreach($order as $file)
			{
				if(file_exists($this->dirpath[0] . $file))
				{
					$file = trim($file);
					$syslog->success('modules','load',$this->dirpath[0] . $file);
					include($this->dirpath[0] . $file);
					$loaded[] = $file;
				}
			}
		}
		
		$dir = dir($this->dirpath[0]);
		while($file = $dir->read())
		{
			if(!in_array($file,$loaded))
			{
				if($file != '.' && $file != '..')
				{
					$e = explode('.',$file);
					if(isset($e[1],$e[2]) && $e[2] == 'php' && $e[1] == 'mod')
					{
						$syslog->success('modules','load',$this->dirpath[0] . $file);
						include($this->dirpath[0] . $file);
					}
				}
			}
		}
		
		$dir = dir($this->dirpath[1]);
		while($file = $dir->read())
		{
			if($file != '.' && $file != '..')
			{
				$syslog->success('modules','load',$this->dirpath[1] . $file);
				include($this->dirpath[1] . $file);
			}
		}
	}
}

$mod = new subsystem_modules();
?>