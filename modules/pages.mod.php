<?php
class module_pages
{
	public $breadcrumbs = array();
	
	private $actual;
	
	private $temptree = NULL;
	private $treeinnerlevel = 1;
	private $treeparts = array();
	
	public function getpage()
	{
		global $q,$tpl;
		$sql = new MySQLObject();
		if($sql->query("
SELECT `pid`,`header`,`content`,`parent`
FROM " . $q->table('pages') . "
WHERE (`slug` = '" . $sql->escape($_GET['slug']) . "')"))
		{
			if($sql->num() > 0)
			{
				$tpl->assign('PAGE',true,'if');
				$tpl->assign('ERROR',false,'if');
			}
			else
			{
				$tpl->assign('PAGE',false,'if');
				$tpl->assign('ERROR',true,'if');
			}
			$page = $sql->fetch_one();
			$tpl->assign(array(
				'PAGE.HEADER' => $page->header,
				'PAGE.CONTENT' => $page->content
			));
			
			$this->actual = array(
				'parent' => $page->parent,
				'id' => $page->pid,
				'slug' => $_GET['slug']
			);
		}
	}
	private function getbreadcrumbs($pid)
	{
		global $q;
		
		$sql = new MySQLObject();
		if($sql->query("
SELECT `pid`,`slug`,`parent`,`header`
FROM " . $q->table('pages') . "
WHERE (`pid` = " . intval($pid) . ")"))
		{
			$page = $sql->fetch_one();
			$this->breadcrumbs[$page->pid] = array(
				'LINK' => '%parent/' . $page->slug,
				'HEADER' => $page->header,
				'SLUG' => $page->slug
			);
			
			if($page->parent != -1)
				$this->getbreadcrumbs($page->parent);
		}
	}
	private function getparentlink($pid)
	{
		if($pid == -1)
			return('./');
		else
		{
			return($this->getparentlink . $this->breadcrumbs[$pid]['SLUG'] . '/');
		}
	}
	public function getchildpages()
	{
		global $q,$tpl;
		
		$sql = new MySQLObject();
		if($sql->query("
SELECT `header`,`path`
FROM " . $q->table('pages') . "
WHERE (`parent` = " . intval($this->actual['id']) . ")"))
		{
			if($sql->num() > 0)
			{
				$tpl->assign('PAGE_CHILDS',true,'if');
				
				$f_childs = array();
				foreach($sql->fetch() as $child)
				{
					$f_childs[] = array(
						//'CHILD_LINK' => $this->get_path($this->actual['parent'],$this->actual['slug']) . '/' . $child->slug,
						'CHILD_LINK' => $child->path,
						'CHILD_HEADER' => $child->header,
					);
				}
				
				$tpl->assign('PAGE_CHILDS',$f_childs,'foreach');
			}
			else
			{
				$tpl->assign('PAGE_CHILDS',false,'if');
			}
		}
	}
	
	// path
	public function get_path($parent,$slug)
	{
		if($parent != -1)
		{
			global $q;
			
			$sql = new MySQLObject();
			if($sql->query("SELECT `path` FROM " . $q->table('pages') . " WHERE (`pid` = " . intval($parent) . ")"))
			{
				$parent_page = $sql->fetch_one();
				return($parent_page->path . '/' . $slug);
			}
			else
			{
				return(false);
			}
		}
		else
		{
			return($slug);
		}
	}
	
	
	
	// admin
	public function tree_get($parent)
	{
		$items = array();
		
		global $q;
		$sql = new MySQLObject();
		if($sql->query("SELECT `pid`,`header` FROM " . $q->table('pages') . " WHERE (`parent` = " . intval($parent) . ") ORDER BY `header` ASC"))
		{
			$i = 0;
			foreach($sql->fetch() as $item)
			{
				$items[$i]['this'] = $item;
				$items[$i]['childs'] = $this->tree_get($item->pid);
				$i++;
			}
		}
		
		return($items);
	}
	public function tree_parts($tpl)
	{
		$this->treeparts = explode("\n",$tpl);
	}
	private function tree_start($root = false,$selectbox = false)
	{
		global $tpl;
		
		if($root)
			if(!$selectbox)
				$this->temptree .= $this->treeparts[0];
			else
			{
				$this->temptree .= $this->treeparts[7];
				$this->temptree .= str_replace(
					'<var(SELECTED)>',
					(
						($tpl->assign['PAGE.PARENT'] == -1)
						? ' selected="selected"'
						: ''
					),
					$this->treeparts[10]
				);
			}
		elseif(!$selectbox)
			$this->temptree .= $this->treeparts[2];
	}
	private function tree_end($root = false,$selectbox = false)
	{
		if($root)
			if(!$selecetbox)
				$this->temptree .= $this->treeparts[1];
			else
				$this->temptree .= $this->treeparts[8];
		elseif(!$selectbox)
			$this->temptree .= $this->treeparts[3];
	}
	public function inctree()
	{
		$tree = $this->temptree;
		$this->temptree = NULL;
		return($tree);
	}
	public function tree_make($tree,$selectbox = false)
	{
		global $tpl;
		
		if(count($tree) > 0)
		{
			if($this->temptree == NULL)
			{
				if(!$selectbox)
					$this->tree_start(true);
				else
					$this->tree_start(true,true);
			}
			else
			{
				if(!$selectbox)
					$this->tree_start();
				else
					$this->tree_start(false,true);
			}
			
			foreach($tree as $item)
			{
				if(!$selectbox)
				{
					$this->temptree .= str_replace(
						array(
							'<var(LINK_EDIT)>',
							'<var(LINK_DELETE)>',
							'<var(HEADER)>'
						),
						array(
							'./acp.php?c=pages&amp;section=page&amp;mode=edit&amp;pid=' . $item['this']->pid,
							'./action.php?c=pages&amp;section=page&amp;mode=delete&amp;pid=' . $item['this']->pid,
							$item['this']->header
						),
						$this->treeparts[4]
					);
				}
				else
				{
					if($item['this']->pid != $tpl->assign['PAGE.ID'])
					{
						$prefix = NULL;
						for($i = 0; $i < $this->treeinnerlevel; $i++)
							$prefix .= '- '; 
						$this->temptree .= str_replace(
							array(
								'<var(HEADER)>',
								'<var(VALUE)>',
								'<var(SELECTED)>'
							),
							array(
								$prefix . $item['this']->header,
								$item['this']->pid,
								(
									($item['this']->pid == $tpl->assign['PAGE.PARENT'])
									? ' selected="selected"'
									: ''
								)
							),
							$this->treeparts[9]
						);
					}
				}
					
				if(!$selectbox)
				{
					$this->tree_make($item['childs']);
					$this->temptree .= $this->treeparts[5];
				}
				else
				{
					$this->treeinnerlevel++;
					$this->tree_make($item['childs'],true);
					$this->treeinnerlevel--;
				}
			}
			
			if($this->temptree == NULL)
			{
				if(!$selectbox)
					$this->tree_end(true);
				else
					$this->tree_end(true,true);
			}
			else
			{
				if(!$selectbox)
					$this->tree_end();
				else
					$this->tree_end(false,true);
			}
		}
		//$this->treeinnerlevel--;
	}
	
	public function editpage()
	{
		global $q,$tpl;
		$sql = new MySQLObject();
		if($sql->query("
SELECT
	" . $q->table('pages') . ".`pid`,
	" . $q->table('pages') . ".`header`,
	" . $q->table('pages') . ".`slug`,
	" . $q->table('pages') . ".`content`,
	" . $q->table('pages') . ".`parent`
FROM " . $q->table('pages') . "
WHERE (" . $q->table('pages') . ".`pid` = " . intval($_GET['pid']) . ")
"))
		{
			$page = $sql->fetch_one();
			
			$fck = new FCKeditor('page[content]');
			$fck->BasePath = './app/lib/js/fckeditor/';
			$fck->Value = $page->content;
			$fck->Height = 512;
			ob_start();
			$fck->Create();
			$content = ob_get_contents();
			ob_end_clean();
			
			$tpl->assign(array(
				'PAGE.ID' => $page->pid,
				'PAGE.HEADER' => $page->header,
				'PAGE.SLUG' => $page->slug,
				'PAGE.CONTENT' => (isset($content)) ? $content : $page->content,
				'PAGE.CONTENT_GEEEDIT' => str_replace("\r\n","'+\r'",$page->content),
				'PAGE.PARENT' => $page->parent
			));
		}
	}
}

global $mod,$tpl;
$mod->modules['pages'] = new module_pages();

global $cfg;
$cfg['permissions']['pages']['page'] = array('read','edit','delete','add','change_parent');

if(defined('IN_SYS') && IN_SYS)
{
	if(isset($_GET['c'],$_GET['slug']) && $_GET['c'] == 'pages')
	{
		$tpl->inc('pages');
		
		$mod->modules['pages']->getpage();
		$mod->modules['pages']->getchildpages();
	}
}

if(defined('IN_ACP') && IN_ACP)
{
	global $cfg;
	$cfg['acp_modules_menu'][] = array(
		'LINK' => './acp.php?c=pages',
		'HEADER' => '{L_MODULE_PAGES}',
		'ACTIVE' => (isset($_GET['c']) && $_GET['c'] == 'pages')
			? $cfg['tpl']['class_subactive']
			: ''
	);
	
	if(!isset($_GET['c']))
	{
		$cfg['installed_modules'][] = array(
			'MODULE_HEADER' => '{L_MODULE_PAGES}',
			'MODULE_DESCRIPTION' => '{L_MODULE_PAGES_DESCRIPTION}',
			'MODULE_LINK' => './acp.php?c=pages',
			'MODULE_IMAGE' => 'images.php?image=module_pages'
		);
	}
	
	if(isset($_GET['c']) && $_GET['c'] == 'pages')
	{
		$mod->modules['pages']->breadcrumbs[] = array(
			'LINK' => './acp.php?c=pages',
			'HEADER' => '{L_MODULE_PAGES}'
		);
		
		$cfg['acp_submenu'][] = array(
			'LINK' => './acp.php?c=pages',
			'HEADER' => '{L_PAGES_PAGES}',
			'ACTIVE' => (!isset($_GET['mode']) || (isset($_GET['section'],$_GET['mode']) && $_GET['section'] == 'page' && $_GET['mode'] == 'edit'))
				? $cfg['tpl']['class_active']
				: ''
		);
		
		if(!isset($_GET['mode']))
		{
			$tpl->inc('pages',1);
			$tpl->load('pages_tree',1);
			
			$mod->modules['pages']->tree_parts($tpl->get('pages_tree',1));
			
			$tree = $mod->modules['pages']->tree_get(-1);
			$mod->modules['pages']->tree_make($tree);
			$tpl->assign('PAGES_TREE',$mod->modules['pages']->inctree());
			
			$tpl->assign('SITE_TITLE','{L_MODULE_PAGES} &ndash; {SITE_HEADER} / {L_ACP}');
		}
		else
		{
			if(isset($_GET['section']))
			{
				switch($_GET['section'])
				{
					case('page'):
						if(isset($_GET['pid']))
						{
							$tpl->inc('pages_page_edit',1);
							$tpl->load('pages_tree',1);
							
							$mod->modules['pages']->editpage();
							
							$mod->modules['pages']->tree_parts($tpl->get('pages_tree',1));
							$tree = $mod->modules['pages']->tree_get(-1);
							$mod->modules['pages']->tree_make($tree,true);
							
							$tpl->assign('PAGE_ACTION','./action.php?c=pages&amp;section=page&amp;mode=edit&amp;pid=' . intval($_GET['pid']));
							$tpl->assign('PAGES_TREE',$mod->modules['pages']->inctree());
						}
						break;
				}
			}
		}
		
		$tpl->assign('BREADCRUMBS',$mod->modules['pages']->breadcrumbs,'foreach');
	}
}

if(defined('IN_IMAGES') && IN_IMAGES)
{
	$cfg['tpl']['images']['module_pages'] = 'acp/images/module_pages.png';
}

if(defined('IN_ACTION') && IN_ACTION)
{
	global $q,$syslog;
	
	if(isset($_GET['c']) && $_GET['c'] == 'pages')
	{
		if(isset($_GET['section']))
		{
			switch($_GET['section'])
			{
				case('page'):
					if(isset($_GET['mode']))
					{
						switch($_GET['mode'])
						{
							case('edit'):
								if(permissions('pages','page','edit'))
								{
									if(intval($_POST['page_slug_generate']) == 1)
										$slug = generate_slug($_POST['page']['header']);
									else
										$slug = $_POST['page']['slug'];
									$sql = new MySQLObject();
									if($sql->query("
UPDATE " . $q->table('pages') . "
SET
	`header` = '" . $sql->escape($_POST['page']['header']) . "',
	`slug` = '" . $sql->escape($slug) . "',
	`content` = '" . $sql->escape($_POST['page']['content']) . "',
	`parent` = " . intval($_POST['page']['parent']) . ",
	`path` = '" . $mod->modules['pages']->get_path(intval($_POST['page']['parent']),$sql->escape($slug)) . "'
WHERE (`pid` = " . intval($_GET['pid']) . ")"))
									{
										$tpl->assign('REDIRECT_LOCATION','./acp.php?c=pages');
										$syslog->alert_success('{L_ALERT_PAGES_PAGE_EDIT_SUCCESS}');
										die();
									}
									else
									{
										$syslog->alert_error('{L_ALERT_PAGES_PAGE_EDIT_ERROR}');
										die();
									}
								}
								else
								{
									$syslog->permissions_error('{L_PERMISSIONS_PAGES_PAGE_EDIT}');
									die();
								}
								break;
						}
					}
					break;
			}
		}
	}
}

if(defined('IN_REDIR') && IN_REDIR)
{
	global $q;
	
	$sql = new MySQLObject();
	if($sql->query("SELECT `path` FROM " . $q->table('pages') . " WHERE (`path` = '" . $sql->escape($_GET['request']) . "')"))
	{
		if($sql->num() > 0)
		{
			$slugs = explode('/',$_GET['request']);
			$slugs = rksort($slugs);
			define('LOC','?c=pages&slug=' . $slugs[0]);
		}
	}
}
?>