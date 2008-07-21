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
	public function tree_get($parent,$selectbox = false,$addpage = false)
	{
		$items = array();
		
		global $q;
		$sql = new MySQLObject();
		if($sql->query("SELECT `pid`,`header` FROM " . $q->table('pages') . " WHERE (`parent` = " . intval($parent) . ") ORDER BY `header` ASC"))
		{
			if($parent == -1 && $sql->num() == 0)
			{
				global $tpl;
				$tpl->assign('INFOBAR',true,'if');
				$tpl->assign('INFOBAR','{L_PAGES_NO_PAGES}');
			}
			
			$i = 0;
			foreach($sql->fetch() as $item)
			{
				if(!$selectbox || $addpage || $item->pid != intval($_GET['pid']))
				{
					$items[$i]['this'] = $item;
					$items[$i]['childs'] = $this->tree_get($item->pid,$selectbox,
						($addpage ? true : false)
					);
					$i++;
				}
			}
		}
		
		return($items);
	}
	public function tree_parts($tpl)
	{
		$this->treeparts = explode("\n",$tpl);
	}
	private function tree_start($root = false,$selectbox = false,$addpage = false)
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
						($addpage || $tpl->assign['PAGE.PARENT'] == -1)
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
			if(!$selectbox)
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
	public function tree_make($tree,$selectbox = false,$addpage = false)
	{
		global $tpl;
		
		if(count($tree) > 0)
		{
			if($this->temptree == NULL)
			{
				if(!$selectbox)
					$this->tree_start(true);
				else
					$this->tree_start(true,true,$addpage);
			}
			else
			{
				if(!$selectbox)
					$this->tree_start();
				else
					$this->tree_start(false,true,$addpage);
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
					if($addpage || $item['this']->pid != $tpl->assign['PAGE.ID'])
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
									(!$addpage && $item['this']->pid == $tpl->assign['PAGE.PARENT'])
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
					$this->tree_make($item['childs'],true,
						($addpage ? true : false)
					);
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
					if($addpage && $this->treeinnerlevel == 1) $this->tree_end(true,true);
					else $this->tree_end(false,true);
			}
		}
		elseif($selectbox && !$addpage)
		{
			if($addpage)
				$this->tree_end(true,true);
			elseif($tpl->assign['PAGE.PARENT'] == -1 && $this->treeinnerlevel == 1)
			{
				$this->tree_start(true,true);
				$this->tree_end(true,true);
			}
		}
	}
	
	
	public function addpage()
	{
		global $tpl;
		
		$fck = new FCKeditor('page[content]');
		$fck->BasePath = './app/lib/js/fckeditor/';
		$fck->Value = '<p class="prefix">Prefix</p><p>Content</p>';
		$fck->Height = 512;
		ob_start();
		$fck->Create();
		$content = ob_get_contents();
		ob_end_clean();
		
		$tpl->assign('PAGE.CONTENT',$content);
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
	" . $q->table('pages') . ".`path`,
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
				'PAGE.PATH' => $page->path,
				'PAGE.CONTENT' => (isset($content)) ? $content : $page->content,
				'PAGE.CONTENT_GEEEDIT' => str_replace("\r\n","'+\r'",$page->content),
				'PAGE.PARENT' => $page->parent
			));
		}
	}
	
	public function menu_add_items()
	{
		$items = array();
		
		$sql = new MySQLObject();
		if($sql->query("SELECT `pid`,`header` FROM " . $sql->table('pages') . " ORDER BY `slug` ASC"))
		{
			global $cfg;
			foreach($sql->fetch() as $page)
			{
				$items[] = array(
					'ADD_TEXT' => $page->header,
					'ADD_LINK' => './action.php?c=menu&amp;mode=add&amp;module=pages&amp;pid=' . $page->pid
				);
			}
		}
		
		return($items);
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
		
		$tpl->assign('SITE_TITLE','{PAGE.HEADER} &mdash; {SITE_HEADER}');
		
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
		$cfg['acp_submenu'][] = array(
			'LINK' => './acp.php?c=pages&amp;section=page&amp;mode=add',
			'HEADER' => '{L_PAGES_PAGE_ADD}',
			'ACTIVE' => ((isset($_GET['section'],$_GET['mode']) && $_GET['section'] == 'page' && $_GET['mode'] == 'add'))
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
						if(isset($_GET['mode']))
						{
							switch($_GET['mode'])
							{
								case('add'):
									$mod->modules['pages']->breadcrumbs[] = array(
										'LINK' => './acp.php?c=pages&amp;section=page&amp;mode=add',
										'HEADER' => '{L_PAGES_PAGE_ADD}'
									);
									
									$tpl->inc('pages_page_add',1);
									$tpl->load('pages_tree',1);
									
									$tpl->assign('SITE_TITLE','{L_MODULE_PAGES} &mdash; {L_PAGES_PAGE_ADD} &ndash; {SITE_HEADER}');
									$tpl->assign('PAGE_ACTION','./action.php?c=pages&amp;section=page&amp;mode=add');
									
									$mod->modules['pages']->addpage();
									
									$mod->modules['pages']->tree_parts($tpl->get('pages_tree',1));
									$tree = $mod->modules['pages']->tree_get(-1,true,true);
									$mod->modules['pages']->tree_make($tree,true,true);
									
									$tpl->assign('PAGES_TREE',$mod->modules['pages']->inctree());
									break;
								
								case('edit'):
									if(isset($_GET['pid']))
									{
										$mod->modules['pages']->breadcrumbs[] = array(
											'LINK' => './acp.php?c=pages&amp;section=page&amp;mode=edit&amp;pid=' . intval($_GET['pid']),
											'HEADER' => '{L_PAGES_PAGE_EDIT}: {PAGE.HEADER}'
										);
										
										$tpl->inc('pages_page_edit',1);
										$tpl->load('pages_tree',1);
										
										$tpl->assign('SITE_TITLE','{L_MODULE_PAGES} &mdash; {L_PAGES_PAGE_EDIT}: {PAGE.HEADER} &ndash; {SITE_HEADER}');
										
										$mod->modules['pages']->editpage();
										
										$mod->modules['pages']->tree_parts($tpl->get('pages_tree',1));
										$tree = $mod->modules['pages']->tree_get(-1,true);
										$mod->modules['pages']->tree_make($tree,true);
										
										$tpl->assign('PAGE_ACTION','./action.php?c=pages&amp;section=page&amp;mode=edit&amp;pid=' . intval($_GET['pid']));
										$tpl->assign('PAGES_TREE',$mod->modules['pages']->inctree());
									}
									break;
							}
						}
						break;
				}
			}
		}
		
		$tpl->assign('BREADCRUMBS',$mod->modules['pages']->breadcrumbs,'foreach');
	}
	
	// -- module_menu(mode=add) --
	elseif(isset($_GET['c'],$_GET['mode']) && $_GET['c'] == 'menu' && $_GET['mode'] == 'add')
	{	
		$t = new subsystem_template();
		$t->load_module_config('pages');
		$t->append($cfg['tpl']['pages']['menu_add']);
		$t->assign('ADD',$mod->modules['pages']->menu_add_items(),'foreach');
		
		global $cfg;
		$cfg['menu_add']['modules'][] = array(
			'MODULE_NAME' => 'pages',
			'MODULE_HEADER' => '{L_MODULE_PAGES}',
			'MODULE_CONTENT' => $t->display(false)
		);
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
							case('add'):
								if(permissions('pages','page','add'))
								{
									// -- slug --
									if(intval($_POST['page_slug_generate']) == 1)
										$slug = generate_slug($_POST['page']['header']);
									else
										$slug = $_POST['page']['slug'];
									
									$sql = new MySQLObject();
									
									$path = $mod->modules['pages']->get_path(intval($_POST['page']['parent']),$sql->escape($slug));
									
									// -- add to the pages table --
									if($sql->query("
INSERT INTO " . $sql->table('pages') . "
(`parent`,`header`,`slug`,`path`,`content`)
VALUES
(
	" . intval($_POST['page']['parent']) . ",
	'" . $sql->escape($_POST['page']['header']) . "',
	'" . $sql->escape($slug) . "',
	'" . $path . "',
	'" . $sql->escape($_POST['page']['content']) . "'
)"))
									{
										if(isset($_POST['options']['addtomainmenu']) && $order = mainmenu_getorder())
										{
											if($sql->query("
INSERT INTO " . $sql->table('menu') . "
(`module`,`header`,`link`,`show`,`order`)
VALUES
(
	'pages',
	'" . $sql->escape($_POST['page']['header']) . "',
	'" . $path . "',
	0,
	" . ($order + 1) . "
)"))
											{
												// -- OK --
												$tpl->assign('REDIRECT_LOCATION','./acp.php?c=pages');
												$syslog->alert_success('{L_ALERT_PAGES_PAGE_ADD_SUCCESS}');
												die();
											}
											else
											{
												$syslog->alert_error('{L_ALERT_PAGES_PAGE_ADD_ERROR}');
												die();
											}
										}
										else
										{
											// -- OK --
											$tpl->assign('REDIRECT_LOCATION','./acp.php?c=pages');
											$syslog->alert_success('{L_ALERT_PAGES_PAGE_ADD_SUCCESS}');
											die();
										}
									}
								}
								else
								{
									$syslog->permissions_error('{L_PERMISSIONS_PAGES_PAGE_ADD}');
									die();
								}
								break;
								
							case('edit'):
								if(permissions('pages','page','edit'))
								{
									// -- slug --
									if(intval($_POST['page_slug_generate']) == 1)
										$slug = generate_slug($_POST['page']['header']);
									else
										$slug = $_POST['page']['slug'];
									
									$sql = new MySQLObject();
									// -- update the pages table --
									if($sql->query("
UPDATE " . $sql->table('pages') . "
SET
	`header` = '" . $sql->escape($_POST['page']['header']) . "',
	`slug` = '" . $sql->escape($slug) . "',
	`content` = '" . $sql->escape($_POST['page']['content']) . "',
	`parent` = " . intval($_POST['page']['parent']) . ",
	`path` = '" . $mod->modules['pages']->get_path(intval($_POST['page']['parent']),$sql->escape($slug)) . "'
WHERE (`pid` = " . intval($_GET['pid']) . ")"))
									{
										// -- update the menu table --
										if($sql->query("
UPDATE " . $sql->table('menu') . "
SET `link` = '" . $mod->modules['pages']->get_path(intval($_POST['page']['parent']),$sql->escape($slug)) . "'
WHERE (`link` = '" . $sql->escape($_POST['page']['path_original']) . "' AND `module` = 'pages')"))
										{
											// -- OK --
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
							
							case('delete'):
								if(permissions('pages','page','delete'))
								{
									if(isset($_GET['pid']))
									{
										$sql = new MySQLObject();
										if($sql->query("SELECT `pid`,`path` FROM " . $sql->table('pages') . " WHERE (`pid` = " . intval($_GET['pid']) . ")"))
										{
											$page = $sql->fetch_one();
											if($sql->query("DELETE FROM " . $sql->table('pages') . " WHERE (`pid` = " . intval($_GET['pid']) . ")"))
											{
												if($sql->query("UPDATE " . $sql->table('pages') . " SET `parent` = -1 WHERE (`parent` = " . $page->pid . ")"))
												{
													$sql->query("DELETE FROM " . $sql->table('menu') . " WHERE (`link` = '" . $page->path . "')");
													$tpl->assign('REDIRECT_LOCATION','./acp.php?c=pages');
													$syslog->alert_success('{L_ALERT_PAGES_PAGE_DELETE_SUCCESS}');
													die();
												}
												else
												{
													$syslog->alert_error('{L_ALERT_PAGES_PAGE_DELETE_ERROR}');
													die();
												}
											}
											else
											{
												$syslog->alert_error('{L_ALERT_PAGES_PAGE_DELETE_ERROR}');
												die();
											}
										}
										else
										{
											$syslog->alert_error('{L_ALERT_PAGES_PAGE_DELETE_ERROR}');
											die();
										}
									}
								}
								else
								{
									$syslog->permissions_error('{L_PERMISSIONS_PAGES_PAGE_DELETE}');
									die();
								}
								break;
						}
					}
					break;
			}
		}
	}
	
	elseif(
		isset($_GET['c'],$_GET['mode'],$_GET['module'],$_GET['pid'])
		&& $_GET['c'] == 'menu'
		&& $_GET['mode'] == 'add'
		&& $_GET['module'] == 'pages'
	)
	{
		$sql = new MySQLObject();
		$order = mainmenu_getorder();
		if(
			$sql->query("SELECT `header`,`path` FROM " . $sql->table('pages') . " WHERE (`pid` = " . intval($_GET['pid']) . ")") 
			&& $sql->num() > 0
		)
		{
			$page = $sql->fetch_one();
			if($sql->query("
INSERT INTO " . $sql->table('menu') . "
(`header`,`link`,`show`,`order`)
VALUES
(
	'" . $page->header . "',
	'" . $page->path . "',
	1,
	" . ($order + 1) . "
)"
			))
			{
				// -- OK --
				$tpl->assign('REDIRECT_LOCATION','./acp.php?c=menu');
				$syslog->alert_success('{L_ALERT_MENU_ITEM_ADD_SUCCESS}');
				die();
			}
			else
			{
				$syslog->alert_error('{L_ALERT_MENU_ITEM_ADD_ERROR}');
				die();
			}
		}
		else
		{
			$syslog->alert_error('{L_ALERT_MENU_ITEM_ADD_ERROR}');
			die();
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