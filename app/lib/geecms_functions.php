<?php
function rm_magicquotes()
{
	if(get_magic_quotes_gpc())
	{
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST, &$_FILES);
		while(list($key, $val) = each($process))
		{
			foreach ($val as $k => $v)
			{
				unset($process[$key][$k]);
				if (is_array($v))
				{
					$process[$key][($key < 5 ? $k : stripslashes($k))] = $v;
					$process[] =& $process[$key][($key < 5 ? $k : stripslashes($k))];
				}
				else
				{
					$process[$key][stripslashes($k)] = stripslashes($v);
   			}
			}
		}
	}
}

function rksort($array)
{
	//$keys = array_keys($array); var_dump($keys);
	$num = count($array) - 1;
	
	$b = array();
	for($i = $num; $i >= 0; $i--)
	{
		$b[($num - $i)] = $array[$i];
	}
	return($b);
}

function generate_slug($string)
{
	$string = strtolower($string);
	
	$r[0] = array('ě','š','č','ř','ž','ý','á','í','é','ó','ů','ú','ň','ť','ď',' ','_');
	$r[1] = array('e','s','c','r','z','y','a','i','e','o','u','u','n','t','d','-','-');
	$string = str_replace($r[0],$r[1],$string);
	
	$slug = '';
	$pattern = '#([a-z0-9\-]+)#is';
	for($i = 0; $i < strlen($string); $i++)
	{
		if(preg_match($pattern,$string[$i]))
			$slug .= $string[$i];
	}
	return($slug);
}

// init
rm_magicquotes();
?>