<?php
class subsystem_mysql
{
	public $connected = false;
	public $dbname;
	public $prefix;
	
	public function connect($host,$user,$password,$dbname)
	{
		global $syslog;
		
		if(!$this->connected)
		{
			if(!@mysql_connect($host,$user,$password))
			{
				$syslog->error('mysql','mysql_connect',mysql_error());
				die();
			}
			else
			{
				$syslog->success('mysql','mysql_connect',$user.'&#64;'.$host);
				
				if(!@mysql_select_db($dbname))
				{
					$syslog->error('mysql','mysql_select_db',mysql_error());
					die();
				}
				else
				{
					$this->connected = true;
					$this->dbname = $dbname;
					$syslog->success('mysql','mysql_select_db',$dbname);
					
					$sql = new MySQLObject();
					$sql->query('SET NAMES utf8');
					$sql->query('SET CHARACTER SET utf8');
					echo(mysql_error());
				}
			}
		}
	}
	
	public function table($tablename)
	{
		return('`' . $this->dbname . '`.`' . $this->prefix . $tablename . '`');
	}
}

class MySQLObject
{
	public $query;
	private $resource;
	private $result;
	private $saved_resources;
	
	public function __construct($query = false,$savename = false)
	{
		if($query)
		{
			$this->query($query,$savename);
		}
	}
	public function query($query,$savename = false)
	{
		global $q,$syslog;
		
		$this->resource = mysql_query($query);
		
		if($savename != false)
		{
			$this->saved_resources[$savename] = $this->resource;
		}
		
		if(!$this->resource)
		{
			$syslog->error('mysql','mysql_query("' . $query . '")',mysql_error());
			return(false);
		}
		else
		{
			$syslog->success('mysql','mysql_query',$query);
			return(true);
		}
	}
	public function fetch($savename = false)
	{
		if(!$savename) $resource = $this->resource;
		elseif(isset($this->saved_resources[$savename])) $resource = $this->saved_resources[$savename];
		else return(array());
		
		for($i = 0; @$item = mysql_fetch_object($resource); $i++)
		{
			$this->result[$i] = $item;
			unset($item);
		}
		unset($resource);
		
		if(count($this->result) > 0)
			return($this->result);
		else
			return(array());
	}
	public function fetch_one($savename = false)
	{
		if(!$savename) return(mysql_fetch_object($this->resource));
		elseif(isset($this->saved_resources[$savename])) return(mysql_fetch_object($this->saved_resources[$savename]));
		else return(false);
	}
	
	public function num()
	{
		if(@mysql_num_rows($this->resource) != 0)
			return(mysql_num_rows($this->resource));
		else
			return(false);
	}
	
	public function insert_id()
	{
		if(mysql_insert_id() != 0)
			return(mysql_insert_id());
		else
			return(false);
	}
	
	public function affected()
	{
		if(mysql_affected_rows() != 0)
			return(mysql_affected_rows());
		else
			return(false);
	}
	
	public function table($tablename)
	{
		global $q;
		return('`' . $q->dbname . '`.`' . $q->prefix . $tablename . '`');
	}
	
	public function escape($string)
	{
		return(mysql_real_escape_string($string));
	}
}

$q = new subsystem_mysql();
?>