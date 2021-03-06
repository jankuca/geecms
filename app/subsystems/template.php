<?php
class subsystem_template
{
	public $dirpath = './styles/default/';
	public $dirpath_acp = './styles/default/acp/';
	public $configpath = './styles/default/acp/config.cfg';

	private $tpls;
	private $output = '';

	public $assign = array();
	private $ifs = array();
	private $foreaches = array();

	public $queue = array(0 => array(),1 => array());

	public function load_config()
	{
		if(file_exists($this->configpath))
		{
			global $cfg;
			include($this->configpath);
		}
	}

	public function load($filename,$type = 0)
	{
		global $syslog;

		($type == 1)
		? $path = $this->dirpath_acp . $filename . '.tpl'
		: $path = $this->dirpath . $filename . '.tpl';

		if(!file_exists($path))
		{
			$syslog->error('template','load',$path);
			return(false);
		}
		else
		{
			$o = fopen($path,'r');
			$file = fread($o,filesize($path));
			$this->tpls[$type][$filename] = $file;
			$syslog->success('template','load',$path);
			return(true);
		}
	}

	public function load_module_config($module)
	{
		global $syslog;

		$path = $this->dirpath_acp . 'config.' . $module . '.cfg';
		if(!file_exists($path))
		{
			$syslog->error('template','load_module_config',$path);
			return(false);
		}
		else
		{
			global $cfg;
			include($path);
			$syslog->success('template','load_module_config',$path);
			return(true);
		}
	}

	public function inc($filename,$type = 0)
	{
		global $syslog;

		if($this->load($filename,$type))
		{
			if(!isset($this->tpls[$type][$filename]))
			{
				$syslog->error('template','inc',$filename);
			}
			else
			{
				$this->output .= $this->tpls[$type][$filename];
				$syslog->success('template','inc',$filename);
			}
		}
	}
	public function get($filename,$type)
	{
		if(isset($this->tpls[$type][$filename]))
			return($this->tpls[$type][$filename]);
	}

	public function display($print = true)
	{
		foreach($this->queue[0] as $command) { eval($command); }

		$this->apply('assign');

		foreach($this->queue[1] as $command) { eval($command); }

		while($this->foreach_count() > 0)
			$this->apply('foreach');
		$this->apply('assign');
		$this->apply('if');

		if($print)
			print($this->output);
		else
			return($this->output);
	}

	public function queue($id)
	{
		foreach($this->queue[$id] as $command) { eval($command); }
		$this->queue[$id] = array();
	}

	public function append($string)
	{
		$this->output .= $string;
	}

	public function assign($data,$value = 'falseNULL',$type = false)
	{
		if($type == 'foreach' && is_array($value))
			$this->foreaches[$data] = $value;
		elseif($type == 'if' && is_bool($value))
			$this->ifs[$data] = $value;
		else
		{
			if($value != 'falseNULL')
				$this->assign[$data] = $value;
			else
				foreach($data as $name => $val)
					$this->assign[$name] = $val;
		}
	}

	private function foreach_count()
	{
		preg_match_all('#<foreach\((.*?)\)>#is',$this->output,$arr);
		return(count($arr[1]));
	}
	private function apply($type)
	{
		switch($type)
		{
			case('assign'):
				foreach($this->assign as $name => $value)
					$this->output = str_replace('{' . $name . '}',$value,$this->output);
				break;

			case('foreach'):
				preg_match_all('#<foreach\(([\w\-\_\.]+)\)>(.*?)</foreach\(\\1\)>#is',$this->output,$arr);
				for($i = 0; $i < count($arr[0]); $i++)
				{
					$out = NULL;
					if(isset($this->foreaches[$arr[1][$i]]))
					{
						for($p = 0; $p < count($this->foreaches[$arr[1][$i]]); $p++)
						{
							preg_match_all('#<var\(([\w\_\.]+)\)>#is',$arr[2][$i],$vars);
							$r = array(array(),array());
							foreach($vars[1] as $key)
							{
								if(isset($this->foreaches[$arr[1][$i]][$p][$key]))
								{
									$r[0][] = '<var(' . $key . ')>';
									$r[1][] = $this->foreaches[$arr[1][$i]][$p][$key];
								}
							}
							$out .= str_replace($r[0],$r[1],$arr[2][$i]);
							unset($r);
						}
					}
					$this->output = str_replace($arr[0][$i],$out,$this->output);
				}
				break;

			case('if'):
				// <if()>...<else()>...</if()>
				preg_match_all('#<if\(([\w\-\_\.]+)\)>(.*?)<else\(\\1\)>(.*?)</if\(\\1\)>#is',$this->output,$arr);
				for($i = 0; $i < count($arr[0]); $i++)
				{
					if(isset($this->ifs[$arr[1][$i]]))
					{
						if($this->ifs[$arr[1][$i]])
							$this->output = str_replace($arr[0][$i],$arr[2][$i],$this->output);
						else
							$this->output = str_replace($arr[0][$i],$arr[3][$i],$this->output);
					}
					else
						$this->output = str_replace($arr[0][$i],$arr[3][$i],$this->output);
				}

				// <if()>...</if()>
				preg_match_all('#<if\(([\w\-\_\.]+)\)>(.*?)</if\(\\1\)>#is',$this->output,$arr);
				for($i = 0; $i < count($arr[0]); $i++)
				{
					if(isset($this->ifs[$arr[1][$i]]))
					{
						if($this->ifs[$arr[1][$i]])
							$this->output = str_replace($arr[0][$i],$arr[2][$i],$this->output);
						else
							$this->output = str_replace($arr[0][$i],'',$this->output);
					}
					else
						$this->output = str_replace($arr[0][$i],'',$this->output);
				}
				break;
		}
	}

	public function _module_config($query)
	{
		global $tpl,$cfg;

		$sql = new MySQLObject();
		if($sql->query($query))
		{
			foreach($sql->fetch() as $item)
			{
				switch($item->type)
				{
					case('string'):
						$tpl->assign('CONFIG.' . strtoupper($item->name),$item->value);
						break;
					case('bool'):
						$tpl->assign(array(
							'CONFIG.' . strtoupper($item->name) . '.TRUE.CHECKED'
							=>	((intval($item->value) == 1)
								?	$cfg['tpl']['checked'] : ''),

							'CONFIG.' . strtoupper($item->name) . '.FALSE.CHECKED'
							=>	((intval($item->value) == 0)
								?	$cfg['tpl']['checked'] : '')
						));
						break;
				}
			}
		}
	}
}

$tpl = new subsystem_template();
?>
