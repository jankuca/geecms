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
	# Sorts the (array) $array by keys in the reverse order.
	
	$num = count($array) - 1;
	
	$b = array();
	for($i = $num; $i >= 0; $i--)
	{
		$b[($num - $i)] = $array[$i];
	}
	return($b);
}

function array_rmempty($array,$keep_keys = true)
{
	# Removes the empty values from (array) $array.
	# If $keep_keys is false, the function returns array with numeric keys starting with 0.
	
	$out = array();
	foreach($array as $key => $value)
	{
		if($value != '' && $value != NULL)
		{
			if($keep_keys)
				$out[$key] = $value;
			else
				$out[] = $value;
		}
	}
	return($out);
}

function generate_slug($string)
{
	# Generates a clean string for URL's from (string) $string.
	
	// -- remove diacritics -- (not complete list - MUST be extended!)
	$r[0] = array('ě','š','č','ř','ž','ý','á','í','é','ó','ů','ú','ň','ť','ď',' ','_');
	$r[1] = array('e','s','c','r','z','y','a','i','e','o','u','u','n','t','d','-','-');
	$string = str_ireplace($r[0],$r[1],$string);
	
	$string = strtolower($string);
	
	$slug = '';
	
	// -- allowed characters --
	$pattern = '#([a-z0-9\-]+)#is';
	
	$previous = NULL;
	for($i = 0; $i < strlen($string); $i++)
	{
		if(preg_match($pattern,$string[$i]))
		{
			if($string[$i] == '-' && $previous == '-'){}
			else
				$slug .= $string[$i];
			
			$previous = $string[$i];
		}
	}
	return($slug);
}

function javascript_prepare_string($string)
{
	# Escapes the $string
	# -- Adds a backslash before each single quote (')
	# -- Replaces new line with escaped new line ('+\n')
	
	return(str_replace(array("'","\r\n"),array("\'","'+\n'"),$string));
}

function permissions($module,$name,$value)
{
	if(isset($_SESSION['permissions'][$module][$name]) && in_array($value,$_SESSION['permissions'][$module][$name]))
		return(true);
	else
		return(false);
}

function mainmenu_reorder($missing)
{
	$sql = new MySQLObject();
	if($sql->query("UPDATE " . $sql->table('menu') . " SET `order` = `order` - 1 WHERE (`order` > " . intval($missing) . ")"))
		return(true);
	else
		return(false);
}

function mainmenu_getorder()
{
	$sql = new MySQLObject();
	if($sql->query("SELECT `order` FROM " . $sql->table('menu') . " ORDER BY `order` DESC LIMIT 0,1"))
	{
		if($sql->num() > 0)
		{
			$order = $sql->fetch_one();
			return($order->order);
		}
		else
			return(0);
	}
	else
		return(false);
}

function _error_404()
{
	
}


// init
rm_magicquotes();
?>