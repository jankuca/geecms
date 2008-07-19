<?php
class Pages
{
	public $GET_param = 'page';
	public $browser_class = 'pages';
	public $per_page = 10;
	public $query = false;
	public $url = false;

	private $made = false;

	public function make()
	{
		if(!$this->query || !$this->url)
			return(false);

		// get the current page
		if(!isset($this->current_page))
		{
			if(isset($_GET[$this->GET_param]))
				$this->current_page = $_GET[$this->GET_param];
			else
				$this->current_page = 1;
		}

		// get count of all items
		if(!preg_match('#^SELECT (.*?) FROM (.*?)$#is',$this->query))
			return(false);

		$query = preg_replace('#SELECT (.*?) FROM#is','SELECT COUNT(*) FROM',$this->query);
		$sql = new MySQLObject();
		if(!$sql->query($query))
			return(false);

		$count = $sql->fetch_one();
		$this->count = $count->{'COUNT(*)'};

		// get count of all pages
		$this->pages_count = ceil($this->count / $this->per_page);

		$this->made = true;
		return(true);
	}

	public function fetch()
	{
		if(!$this->made)
			return(array());

		$limit[1] = $this->current_page * $this->per_page;
		$limit[0] = $limit[1] - $this->per_page;
		$query = $this->query . " LIMIT " . $limit[0] . "," . $limit[1];

		$sql = new MySQLObject();
		if(!$sql->query($query))
			return(array());

		return($sql->fetch());
	}

	public function browser()
	{
		if(!$this->made)
			return(false);

		$browser = '<div class="' . $this->browser_class . '">';

		// 1st && PREV
		if($this->current_page == 1)
			$browser .= '<strong>1</strong> ';
		else
			$browser .= '<a href="' . str_replace('%page',$this->current_page - 1,$this->url) . '">{L_PREV}</a> <a href="' . str_replace('%page',1,$this->url) . '">1</a> ';

		if($this->pages_count <= 10)
		{
			for($i = 2; $i <= $this->pages_count - 1; $i++)
			{
				if($this->current_page == $i)
					$browser .= '<strong>' . $i . '</strong> ';
				else
					$browser .= '<a href="' . str_replace('%page',$i,$this->url) . '">' . $i . '</a> ';
			}
		}
		else
		{
			if($this->current_page > 1 && $this->current_page < 4)
			{
				for($i = 2; $i < 4; $i++)
				{
					if($this->current_page == $i)
						$browser .= '<strong>' . $i . '</strong> ';
					else
						$browser .= '<a href="' . str_replace('%page',$i,$this->url) . '">' . $i . '</a> ';
				}
				$browser .= '... ';
			}
			elseif($this->current_page >= 4 && $this->current_page <= ($this->pages_count - 4))
			{
				$browser .= '... ';
				for($i = $this->current_page - 2; $i < $this->current_page + 2; $i++)
				{
					if($this->current_page == $i)
						$browser .= '<strong>' . $i . '</strong> ';
					else
						$browser .= '<a href="' . str_replace('%page',$i,$this->url) . '">' . $i . '</a> ';
				}
				$browser .= '... ';
			}
			elseif($this->current_page > ($this->pages_count - 4) && $this->current_page < $this->pages_count)
			{
				$browser .= '... ';
				for($i = ($this->pages_count - 3); $i < $this->pages_count; $i++)
				{
					if($this->current_page == $i)
						$browser .= '<strong>' . $i . '</strong> ';
					else
						$browser .= '<a href="' . str_replace('%page',$i,$this->url) . '">' . $i . '</a> ';
				}
			}
		}

		if($this->current_page == $this->pages_count)
			$browser .= '<strong>' . $this->current_page . '</strong> ';
		else
			$browser .= '<a href="' . str_replace('%page',$this->pages_count,$this->url) . '">' . $this->pages_count . '</a> <a href="' . str_replace('%page',$this->current_page + 1,$this->url) . '">{L_NEXT}</a> ';

		return($browser);
	}
}
?>