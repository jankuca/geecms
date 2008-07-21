<?php
class module_blog
{
	private $counts; // call $this->counts() to get the counts
	
	public function counts()
	{
		global $tpl;
		
		$sql = new MySQLObject();
		if($sql->query("SELECT DISTINCT `tag` FROM " . $sql->table('blog_tags')))
		{
			$tpl->assign('BLOG_TAGS_COUNT',(!$sql->num()) ? '0' : $sql->num());
			if($sql->num() == 0)
			{
				$tpl->assign('INFOBAR',true,'if');
				$tpl->assign('INFOBAR','{L_BLOG_NO_TAGS}');
			}
		}
		
		if($sql->query("SELECT COUNT(*) as count FROM " . $sql->table('blog_posts')))
		{
			$count = $sql->fetch_one();
			$tpl->assign('BLOG_POSTS_COUNT',$count->count);
			if($count->count == 0)
			{
				$tpl->assign('INFOBAR',true,'if');
				$tpl->assign('INFOBAR','{L_BLOG_NO_POSTS}');
			}
		}
		
		if($sql->query("SELECT COUNT(*) as count FROM " . $sql->table('blog_categories')))
		{
			$count = $sql->fetch_one();
			$tpl->assign('BLOG_CATEGORIES_COUNT',$count->count);
			if($count->count == 0)
			{
				$tpl->assign('INFOBAR',true,'if');
				$tpl->assign('INFOBAR','{L_BLOG_NO_CATEGORIES}');
			}
		}
	}
	
	// -- ACP --
	public function acp_posts()
	{
		$sql = new MySQLObject();
		$p = new Pages();
		$p->per_page = 50;
		$p->url = './acp.php?c=blog&amp;section=post&amp;page=%page';
		$p->query = 
"SELECT
	posts.`pid` as post_id,
	posts.`header` as post_header,
	posts.`slug` as post_slug,
	categories.`header` as category_header
FROM " . $sql->table('blog_posts') . " posts
LEFT JOIN " . $sql->table('blog_categories') . " categories
	ON posts.`category` = categories.`cid`
ORDER BY posts.`date` DESC";
		$p->make();
		
		$f_posts = array();
		foreach($p->fetch() as $post)
		{
			$f_posts[] = array(
				'POST_HEADER' => $post->post_header,
				'POST_SLUG' => $post->post_slug,
				'POST_CATEGORY' => $post->category_header,
				'POST_LINK_EDIT' => './acp.php?c=blog&amp;section=post&amp;mode=edit&amp;pid=' . $post->post_id,
				'POST_LINK_DELETE' => './action.php?c=blog&amp;section=post&amp;mode=delete&amp;pid=' . $post->post_id
			);
		}
		global $tpl;
		$tpl->assign('BLOG_POSTS',$f_posts,'foreach');
		$tpl->assign('BLOG_POSTS_BROWSER',$p->browser());
	}
	
	public function acp_categories($active = -1)
	{
		global $cfg;
		
		$sql = new MySQLObject();
		if($sql->query("
SELECT `header`,`slug`
FROM " . $sql->table('blog_categories') . "
WHERE (`cid` = -1)"))
		{
			$f_categories = array();
			$category = $sql->fetch_one();
			$f_categories[] = array(
				'CATEGORY_ID' => -1,
				'CATEGORY_HEADER' => $category->header,
				'CATEGORY_SLUG' => $category->slug,
				'CATEGORY_LINK_EDIT' => './acp.php?c=blog&amp;section=category&amp;mode=edit&amp;cid=-1',
				'CATEGORY_LINK_DELETE' => './action.php?c=blog&amp;section=category&amp;mode=delete&amp;cid=-1',
				'CATEGORY_ACTIVE' => ($active == -1) ? $cfg['tpl']['selected'] : ''
			);
			
			if($sql->query("
		SELECT `cid`,`header`,`slug`
		FROM " . $sql->table('blog_categories') . "
		WHERE (`cid` != -1)
		ORDER BY `slug` ASC"))
			{
				foreach($sql->fetch() as $category)
				{
					$f_categories[] = array(
						'CATEGORY_ID' => $category->cid,
						'CATEGORY_HEADER' => $category->header,
						'CATEGORY_SLUG' => $category->slug,
						'CATEGORY_LINK_EDIT' => './acp.php?c=blog&amp;section=category&amp;mode=edit&amp;cid=' . $category->cid,
						'CATEGORY_LINK_DELETE' => './action.php?c=blog&amp;section=category&amp;mode=delete&amp;cid=' . $category->cid,
						'CATEGORY_ACTIVE' => ($active == $category->cid) ? $cfg['tpl']['selected'] : ''
					);
				}
			}
			
			global $tpl;
			$tpl->assign('BLOG_CATEGORIES',$f_categories,'foreach');
		}
	}
	
	public function acp_tags()
	{
		$sql = new MySQLObject();
		$p = new Pages();
		$p->per_page = 50;
		$p->url = './acp.php?c=blog&amp;section=tag&amp;page=%page';
		$p->query = 
"SELECT `tag`,`header`,COUNT(*) as count
FROM " . $sql->table('blog_tags') . "
GROUP BY `tag`
ORDER BY count DESC";
		$p->make();
		
		$f_tags = array();
		foreach($p->fetch() as $tag)
		{
			$f_tags[] = array(
				'TAG_HEADER' => $tag->header,
				'TAG_TAG' => $tag->tag,
				'TAG_LINK_EDIT' => './acp.php?c=blog&amp;section=tag&amp;mode=edit&amp;tag=' . $tag->tag,
				'TAG_LINK_DELETE' => './action.php?c=blog&amp;section=tag&amp;mode=delete&amp;tag=' . $tag->tag,
				'TAG_COUNT' => $tag->count
			);
		}
		global $tpl;
		$tpl->assign('BLOG_TAGS',$f_tags,'foreach');
		$tpl->assign('BLOG_TAGS_BROWSER',$p->browser());
	}
	
	public function acp_post_add()
	{
		global $tpl;
		
		$fck = new FCKeditor('page[prologue]');
		$fck->BasePath = './app/lib/js/fckeditor/';
		$fck->Value = '<p>Lorem ipsum dolor sit amet...</p>';
		$fck->Height = 320;
		$prologue = $fck->CreateHtml();
		
		$fck = new FCKeditor('page[content]');
		$fck->BasePath = './app/lib/js/fckeditor/';
		$fck->Value = '<p>Lorem ipsum dolor sit amet...</p>';
		$fck->Height = 512;
		$content = $fck->CreateHtml();
		
		$tpl->assign(array(
			'POST.PROLOGUE' => $prologue,
			'POST.CONTENT' => $content
		));
	}

	public function acp_post_edit()
	{
		global $tpl;
		
		$sql = new MySQLObject();
		if($sql->query("
SELECT `category`,`date`,`header`,`slug`,`prologue`,`content`
FROM " . $sql->table('blog_posts') . "
WHERE (`pid` = " . intval($_GET['pid']) . ")"))
		{
			$post = $sql->fetch_one();
			
			$fck = new FCKeditor('page[prologue]');
			$fck->BasePath = './app/lib/js/fckeditor/';
			$fck->Value = $post->prologue;
			$fck->Height = 320;
			ob_start();
			$fck->Create();
			$prologue = ob_get_contents();
			ob_end_clean();
			
			$fck = new FCKeditor('page[content]');
			$fck->BasePath = './app/lib/js/fckeditor/';
			$fck->Value = $post->content;
			$fck->Height = 512;
			ob_start();
			$fck->Create();
			$content = ob_get_contents();
			ob_end_clean();
			
			$date = date('Y-m-d H:i:s',$post->date);
			
			$tpl->assign(array(
				'POST.ID' => intval($_GET['pid']),
				'POST.HEADER' => $post->header,
				'POST.SLUG' => $post->slug,
				'POST.PROLOGUE' => $prologue,
				'POST.CONTENT' => $content,
				'POST.DATE' => $date
			));
			
			if($sql->query("SELECT `tag`,`header` FROM " . $sql->table('blog_tags') . " WHERE (`post` = " . intval($_GET['pid']) . ")"))
			{
				$f_tags = array();
				foreach($sql->fetch() as $tag)
				{
					$f_tags[] = array(
						'TAG_TAG' => $tag->tag,
						'TAG_HEADER' => $tag->header
					);
				}
				$tpl->assign('POST.TAGS',$f_tags,'foreach');
			}
			
			$this->acp_categories($post->category);
		}
	}
	
	public function acp_category_edit()
	{
		if(isset($_GET['cid']))
		{
			$sql = new MySQLObject();
			if($sql->query("SELECT `header`,`slug` FROM " . $sql->table('blog_categories') . " WHERE (`cid` = " . intval($_GET['cid']) . ")") && $sql->num() > 0)
			{
				$category = $sql->fetch_one();
				
				global $tpl;
				$tpl->assign(array(
					'CATEGORY.HEADER' => $category->header,
					'CATEGORY.SLUG' => $category->slug
				));
			}
		}
	}
	
	public function acp_tag_edit()
	{
		if(isset($_GET['tag']))
		{
			$sql = new MySQLObject();
			if($sql->query("SELECT `header`,`tag` FROM " . $sql->table('blog_tags') . " WHERE (`tag` = '" . $sql->escape($_GET['tag']) . "')") && $sql->num() > 0)
			{
				$tag = $sql->fetch_one();
				
				global $tpl;
				$tpl->assign(array(
					'TAG.HEADER' => $tag->header,
					'TAG.TAG' => $tag->tag
				));
			}
		}
	}
	
	// -- menu (mode=add) --
	public function menu_add_items_categories()
	{
		$items = array();
		
		$sql = new MySQLObject();
		if($sql->query("SELECT `header` FROM " . $sql->table('blog_categories') . " WHERE (`cid` = -1)"))
		{
			$category = $sql->fetch_one();
			$items[] = array(
				'ADD_TEXT' => $category->header,
				'ADD_LINK' => './action.php?c=menu&amp;mode=add&amp;module=blog&amp;cid=-1'
			);
			
			if($sql->query("SELECT `cid`,`header` FROM " . $sql->table('blog_categories') . " WHERE (`cid` != -1) ORDER BY `slug` ASC"))
			foreach($sql->fetch() as $category)
			{
				$items[] = array(
					'ADD_TEXT' => $category->header,
					'ADD_LINK' => './action.php?c=menu&amp;mode=add&amp;module=blog&amp;cid=' . $category->cid
				);
			}
		}
		
		return($items);
	}
	
	public function menu_add_items_posts()
	{
		$items = array();
		
		$sql = new MySQLObject();
		if($sql->query("SELECT `pid`,`header` FROM " . $sql->table('blog_posts') . " ORDER BY `pid` DESC"))
		{
			global $cfg;
			foreach($sql->fetch() as $post)
			{
				$items[] = array(
					'ADD_TEXT' => $post->header,
					'ADD_LINK' => './action.php?c=menu&amp;mode=add&amp;module=blog&amp;pid=' . $post->pid
				);
			}
		}
		
		return($items);
	}
}

global $cfg;
$cfg['permissions']['blog']['post'] = array('read','edit','delete','add');
$cfg['permissions']['blog']['category'] = array('read','edit','delete','add');
$cfg['permissions']['blog']['tag'] = array('edit','delete');

global $mod;
$mod->modules['blog'] = new module_blog();

if(defined('IN_ACP') && IN_ACP)
{
	global $cfg;
	$cfg['acp_modules_menu'][] = array(
		'LINK' => './acp.php?c=blog',
		'HEADER' => '{L_MODULE_BLOG}',
		'ACTIVE' => (isset($_GET['c']) && $_GET['c'] == 'blog')
			? $cfg['tpl']['class_subactive']
			: ''
	);
	
	if(!isset($_GET['c']))
	{
		$cfg['installed_modules'][] = array(
			'MODULE_HEADER' => '{L_MODULE_BLOG}',
			'MODULE_DESCRIPTION' => '{L_MODULE_BLOG_DESCRIPTION}',
			'MODULE_LINK' => './acp.php?c=blog',
			'MODULE_IMAGE' => 'images.php?image=module_pages'
		);
	}
	elseif($_GET['c'] == 'blog')
	{
		$cfg['acp_submenu'][] = array(
			'LINK' => './acp.php?c=blog',
			'HEADER' => '{L_OVERVIEW}',
			'ACTIVE' => (!isset($_GET['section']))
				? $cfg['tpl']['class_active']
				: ''
		);
		$cfg['acp_submenu'][] = array(
			'LINK' => './acp.php?c=blog&amp;section=post',
			'HEADER' => '{L_BLOG_POSTS}',
			'ACTIVE' => (
			(
				isset($_GET['section'])
				&& $_GET['section'] == 'post'
				&& (
					!isset($_GET['mode'])
					|| (
						isset($_GET['mode'])
						&& $_GET['mode'] == 'edit'
					)
				)
			))
				? $cfg['tpl']['class_active']
				: ''
		);
		$cfg['acp_submenu'][] = array(
			'LINK' => './acp.php?c=blog&amp;section=post&amp;mode=add',
			'HEADER' => '{L_BLOG_POST_ADD}',
			'ACTIVE' => (isset($_GET['section'],$_GET['mode']) && $_GET['section'] == 'post' && $_GET['mode'] == 'add')
				? $cfg['tpl']['class_active']
				: ''
		);
		$cfg['acp_submenu'][] = array(
			'LINK' => './acp.php?c=blog&amp;section=category',
			'HEADER' => '{L_BLOG_CATEGORIES}',
			'ACTIVE' => (
			(
				isset($_GET['section'])
				&& $_GET['section'] == 'category'
				&& (
					!isset($_GET['mode'])
					|| (
						isset($_GET['mode'])
						&& $_GET['mode'] == 'edit'
					)
				)
			))
				? $cfg['tpl']['class_active']
				: ''
		);
		$cfg['acp_submenu'][] = array(
			'LINK' => './acp.php?c=blog&amp;section=category&amp;mode=add',
			'HEADER' => '{L_BLOG_CATEGORY_ADD}',
			'ACTIVE' => (isset($_GET['section'],$_GET['mode']) && $_GET['section'] == 'category' && $_GET['mode'] == 'add')
				? $cfg['tpl']['class_active']
				: ''
		);
		$cfg['acp_submenu'][] = array(
			'LINK' => './acp.php?c=blog&amp;section=tag',
			'HEADER' => '{L_BLOG_TAGS}',
			'ACTIVE' => (isset($_GET['section']) && $_GET['section'] == 'tag')
				? $cfg['tpl']['class_active']
				: ''
		);
		
		
		global $tpl;
		
		if($_GET['c'] == 'blog')
		{
			$mod->modules['blog']->breadcrumbs[] = array(
				'LINK' => './acp.php?c=blog',
				'HEADER' => '{L_MODULE_BLOG}'
			);
			
			if(!isset($_GET['section']))
			{
				$tpl->inc('blog',1);
				$mod->modules['blog']->counts();
				
				$tpl->assign('SITE_TITLE','{L_MODULE_BLOG} &ndash; {SITE_HEADER}');
			}
			else
			{
				switch($_GET['section'])
				{
					case('post'):
						$mod->modules['blog']->breadcrumbs[] = array(
							'LINK' => './acp.php?c=blog&amp;section=post',
							'HEADER' => '{L_BLOG_POSTS}'
						);
						
						if(!isset($_GET['mode']))
						{
							$tpl->inc('blog_posts',1);
							$mod->modules['blog']->acp_posts();
											
							$tpl->assign('SITE_TITLE','{L_MODULE_BLOG} &mdash; {L_BLOG_POSTS} &ndash; {SITE_HEADER}');
						}
						else
						{
							switch($_GET['mode'])
							{
								case('add'):
									$mod->modules['blog']->breadcrumbs[] = array(
										'LINK' => './acp.php?c=blog&amp;section=post&mode=add',
										'HEADER' => '{L_BLOG_POST_ADD}'
									);
									
									$tpl->inc('blog_post_add',1);
									$mod->modules['blog']->acp_post_add();
									$mod->modules['blog']->acp_tags();
									$mod->modules['blog']->acp_categories();
									
									$tpl->assign('POST_ACTION','./action.php?c=blog&amp;section=post&amp;mode=add');
									break;
								
								case('edit'):
									if(isset($_GET['pid']))
									{
										$mod->modules['blog']->breadcrumbs[] = array(
											'LINK' => './acp.php?c=blog&amp;section=post&mode=edit&amp;pid=' . intval($_GET['pid']),
											'HEADER' => '{L_BLOG_POST_EDIT}: {POST.HEADER}'
										);
										
										$tpl->inc('blog_post_edit',1);
										$mod->modules['blog']->acp_post_edit();
										$mod->modules['blog']->acp_tags();
										
										$tpl->assign('POST_ACTION','./action.php?c=blog&amp;section=post&amp;mode=edit&amp;pid=' . intval($_GET['pid']));
									}
									break;
							}
						}
						break;
					
					case('category'):
						$mod->modules['blog']->breadcrumbs[] = array(
							'LINK' => './acp.php?c=blog&amp;section=category',
							'HEADER' => '{L_BLOG_CATEGORIES}'
						);
						
						if(!isset($_GET['mode']))
						{
							$tpl->inc('blog_categories',1);
							$mod->modules['blog']->acp_categories();
							
							$tpl->assign('SITE_TITLE','{L_MODULE_BLOG} &mdash; {L_BLOG_CATEGORIES} &ndash; {SITE_HEADER}');
						}
						else
						{
							switch($_GET['mode'])
							{
								case('add'):
									$mod->modules['blog']->breadcrumbs[] = array(
										'LINK' => './acp.php?c=blog&amp;section=category&amp;mode=add',
										'HEADER' => '{L_BLOG_CATEGORY_ADD}'
									);
									
									$tpl->inc('blog_category_add',1);
									
									$tpl->assign('SITE_TITLE','{L_MODULE_BLOG} &mdash; {L_BLOG_CATEGORY_ADD} &ndash; {SITE_HEADER}');
									$tpl->assign('CATEGORY_ACTION','./action.php?c=blog&amp;section=category&amp;mode=add');
									break;
								
								case('edit'):
									$mod->modules['blog']->breadcrumbs[] = array(
										'LINK' => './acp.php?c=blog&amp;section=category&amp;mode=edit&amp;cid=' . intval($_GET['cid']),
										'HEADER' => '{L_BLOG_CATEGORY_EDIT}: {CATEGORY.HEADER}'
									);
									
									$tpl->inc('blog_category_edit',1);
									$mod->modules['blog']->acp_category_edit();
									
									
									$tpl->assign('SITE_TITLE','{L_MODULE_BLOG} &mdash; {L_BLOG_CATEGORY_EDIT}: {CATEGORY.HEADER} &ndash; {SITE_HEADER}');
									$tpl->assign('CATEGORY_ACTION','./action.php?c=blog&amp;section=category&amp;mode=edit&amp;cid=' . intval($_GET['cid']));
									break;
							}
						}
						break;
					
					case('tag'):
						if(!isset($_GET['mode']))
						{
							$tpl->inc('blog_tags',1);
							$mod->modules['blog']->acp_tags();
							
							$tpl->assign('SITE_TITLE','{L_MODULE_BLOG} &mdash; {L_BLOG_TAGS} &ndash; {SITE_HEADER}');
						}
						elseif($_GET['mode'] == 'edit')
						{
							$mod->modules['blog']->breadcrumbs[] = array(
								'LINK' => './acp.php?c=blog&amp;section=tag&amp;mode=edit&amp;tag=' . $_GET['tag'],
								'HEADER' => '{L_BLOG_TAG_EDIT}: {TAG.HEADER}'
							);
							
							$tpl->inc('blog_tag_edit',1);
							$mod->modules['blog']->acp_tag_edit();
							
							
							$tpl->assign('SITE_TITLE','{L_MODULE_BLOG} &mdash; {L_BLOG_TAG_EDIT}: {TAG.HEADER} &ndash; {SITE_HEADER}');
							$tpl->assign('TAG_ACTION','./action.php?c=blog&amp;section=tag&amp;mode=edit&amp;tag=' . $_GET['tag']);
							break;
						}
						break;
				}
			}
			
			$tpl->assign('BREADCRUMBS',$mod->modules['blog']->breadcrumbs,'foreach');
		}
	}
	
	// -- module_menu(mode=add) --
	elseif(isset($_GET['c'],$_GET['mode']) && $_GET['c'] == 'menu' && $_GET['mode'] == 'add')
	{	
		$t = new subsystem_template();
		$t->load_module_config('blog');
		$t->append($cfg['tpl']['blog']['menu_add']);
		$t->assign('CATEGORIES',$mod->modules['blog']->menu_add_items_categories(),'foreach');
		$t->assign('POSTS',$mod->modules['blog']->menu_add_items_posts(),'foreach');
		
		global $cfg;
		$cfg['menu_add']['modules'][] = array(
			'MODULE_NAME' => 'blog',
			'MODULE_HEADER' => '{L_MODULE_BLOG}',
			'MODULE_CONTENT' => $t->display(false)
		);
	}
}

if(defined('IN_ACTION') && IN_ACTION)
{
	if(isset($_GET['c']) && $_GET['c'] == 'blog')
	{
		if(isset($_GET['section']))
		{
			switch($_GET['section'])
			{
				case('post'):
					if(isset($_GET['mode']))
					{
						switch($_GET['mode'])
						{
							case('add'):
								if(permissions('blog','post','add') && isset($_POST['post_slug_generate']))
								{
									// -- slug --
									switch(intval($_POST['post_slug_generate']))
									{
										case(0): $slug = $_POST['post']['slug']; break;
										case(1): $slug = generate_slug($_POST['post']['header']); break;
									}
									
									// -- update the posts table --
									$sql = new MySQLObject();
									if($sql->query("
INSERT INTO " . $sql->table('blog_posts') . "
(`category`,`date`,`header`,`slug`,`prologue`,`content`)
VALUES
(
	" . intval($_POST['post']['category']) . ",
	" . time() . ",
	'" . $sql->escape($_POST['post']['header']) . "',
	'" . $sql->escape($slug) . "',
	'" . $sql->escape($_POST['page']['prologue']) . "',
	'" . $sql->escape($_POST['page']['content']) . "'
)"))
									{
										$post_id = $sql->insert_id();
										
										// -- get the new tags --
										$tags = array_rmempty(explode(', ',$_POST['post']['tags']));
										
										// -- insert the new tags --
										$tags_insert = array();
										$tags_used = array();
										foreach($tags as $tag)
										{
											if(!in_array($tag,$tags_used))
											{
												$tags_insert[] = array(
													'tag' => generate_slug($tag),
													'header' => $tag
												);
												$tags_used[] = $tag;
											}
										}
										if(count($tags_insert) != 0)
										{
											$query = "
INSERT INTO " . $sql->table('blog_tags') . "
(`tag`,`header`,`post`)
VALUES";
$i = 0;
foreach($tags_insert as $tag)
{
$query .= "
('" . $sql->escape($tag['tag']) . "','" . $sql->escape($tag['header']) . "'," . $post_id . ")";
if($i < count($tags_insert) - 1) $query .= ",";
$i++;
}
											if($sql->query($query))
											{
												// -- OK --
												$tpl->assign('REDIRECT_LOCATION','./acp.php?c=blog&section=post');
												$syslog->alert_success('{L_ALERT_BLOG_POST_ADD_SUCCESS}');
												die();
											}
											else
											{
												$syslog->alert_error('{L_ALERT_BLOG_POST_ADD_ERROR}');
												die();
											}
										}
									}
								}
								else
								{
									$syslog->permissions_error('{L_PERMISSIONS_BLOG_POST_ADD}');
									die();
								}
								break;
							
							case('edit'):
								if(permissions('blog','post','edit') && isset($_POST['post_slug_generate']))
								{
									// -- slug --
									switch(intval($_POST['post_slug_generate']))
									{
										case(0): $slug = $_POST['post']['slug']; break;
										case(1): $slug = generate_slug($_POST['post']['header']); break;
										case(2): $slug = $_POST['post_slug_original']; break;
									}
									
									// -- date --
									$data = explode(' ',$_POST['post']['date']);
									$data[0] = explode('-',$data[0]);
									$data[1] = explode(':',$data[1]);
									$date = mktime($data[1][0],$data[1][1],$data[1][2],$data[0][1],$data[0][2],$data[0][0]);
									
									// -- update the posts table --
									$sql = new MySQLObject();
									if($sql->query("
UPDATE " . $sql->table('blog_posts') . "
SET
	`date` = " . $date . ",
	`category` = " . intval($_POST['post']['category']) . ",
	`header` = '" . $sql->escape($_POST['post']['header']) . "',
	`slug` = '" . $sql->escape($slug) . "',
	`prologue` = '" . $sql->escape($_POST['page']['prologue']) . "',
	`content` = '" . $sql->escape($_POST['page']['content']) . "'
WHERE (`pid` = " . intval($_GET['pid']) . ")"))
									{
										// -- delete the old tags --
										if($sql->query("DELETE FROM " . $sql->table('blog_tags') . " WHERE (`post` = " . intval($_GET['pid']) . ")"))
										{
											// -- get the new tags --
											$tags = array_rmempty(explode(', ',$_POST['post']['tags']));
											
											// -- insert the new tags --
											$tags_insert = array();
											$tags_used = array();
											foreach($tags as $tag)
											{
												if(!in_array($tag,$tags_used))
												{
													$tags_insert[] = array(
														'tag' => generate_slug($tag),
														'header' => $tag
													);
													$tags_used[] = $tag;
												}
											}
											if(count($tags_insert) != 0)
											{
												$query = "
INSERT INTO " . $sql->table('blog_tags') . "
(`tag`,`header`,`post`)
VALUES";
$i = 0;
foreach($tags_insert as $tag)
{
	$query .= "
('" . $sql->escape($tag['tag']) . "','" . $sql->escape($tag['header']) . "'," . intval($_GET['pid']) . ")";
	if($i < count($tags_insert) - 1) $query .= ",";
	$i++;
}
												if($sql->query($query))
												{
													// -- OK --
													$tpl->assign('REDIRECT_LOCATION','./acp.php?c=blog&section=post');
													$syslog->alert_success('{L_ALERT_BLOG_POST_EDIT_SUCCESS}');
													die();
												}
												else
												{
													$syslog->alert_error('{L_ALERT_BLOG_POST_EDIT_ERROR}');
													die();
												}
											}
										}
									}
								}
								else
								{
									$syslog->permissions_error('{L_PERMISSIONS_BLOG_POST_EDIT}');
									die();
								}
								break;
							
							case('delete'):
								if(permissions('blog','post','delete'))
								{
									$sql = new MySQLObject();
									if($sql->query("
DELETE FROM " . $sql->table('blog_posts') . "
WHERE (`pid` = " . intval($_GET['pid']) . ")"))
									{
										// -- OK --
										$tpl->assign('REDIRECT_LOCATION','./acp.php?c=blog&section=post');
										$syslog->alert_success('{L_ALERT_BLOG_POST_DELETE_SUCCESS}');
										die();
									}
									else
									{
										$syslog->alert_error('{L_ALERT_BLOG_POST_DELETE_ERROR}');
										die();
									}
								}
								else
								{
									$syslog->permissions_error('{L_PERMISSIONS_BLOG_POST_DELETE}');
									die();
								}
								break;
						}
					}
					break;
				
				case('category'):
					if(isset($_GET['mode']))
					{
						switch($_GET['mode'])
						{
							case('add'):
								if(permissions('blog','category','add') && isset($_POST['category_slug_generate']))
								{
									// -- slug --
									switch(intval($_POST['category_slug_generate']))
									{
										case(0): $slug = $_POST['category']['slug']; break;
										case(1): $slug = generate_slug($_POST['category']['header']); break;
									}
									
									$sql = new MySQLObject();
									if($sql->query("
INSERT INTO " . $sql->table('blog_categories') . "
(`header`,`slug`)
VALUES
('" . $sql->escape($_POST['category']['header']) . "','" . $sql->escape($slug) . "')"))
									{
										// -- OK --
										$tpl->assign('REDIRECT_LOCATION','./acp.php?c=blog&section=category');
										$syslog->alert_success('{L_ALERT_BLOG_CATEGORY_ADD_SUCCESS}');
										die();
									}
									else
									{
										$syslog->alert_error('{L_ALERT_BLOG_CATEGORY_ADD_ERROR}');
										die();
									}
								}
								else
								{
									$syslog->permissions_error('{L_PERMISSIONS_BLOG_CATEGORY_ADD}');
									die();
								}
								break;
							
							case('edit'):
								if(permissions('blog','category','edit') && isset($_POST['category_slug_generate']))
								{
									// -- slug --
									switch(intval($_POST['category_slug_generate']))
									{
										case(0): $slug = $_POST['category']['slug']; break;
										case(1): $slug = generate_slug($_POST['category']['header']); break;
										case(2): $slug = $_POST['category_slug_original']; break;
									}
									
									$sql = new MySQLObject();
									if($sql->query("
UPDATE " . $sql->table('blog_categories') . "
SET
	`header` = '" . $sql->escape($_POST['category']['header']) . "',
	`slug` = '" . $sql->escape($slug) . "'
WHERE (`cid` = " . intval($_GET['cid']) . ")"))
									{
										// -- OK --
										$tpl->assign('REDIRECT_LOCATION','./acp.php?c=blog&section=category');
										$syslog->alert_success('{L_ALERT_BLOG_CATEGORY_EDIT_SUCCESS}');
										die();
									}
									else
									{
										$syslog->alert_error('{L_ALERT_BLOG_CATEGORY_EDIT_ERROR}');
										die();
									}
								}
								else
								{
									$syslog->permissions_error('{L_PERMISSIONS_BLOG_CATEGORY_EDIT}');
									die();
								}
								break;
							
							case('delete'):
								if(permissions('blog','category','delete'))
								{
									$sql = new MySQLObject();
									if($sql->query("DELETE FROM " . $sql->table('blog_categories') . " WHERE (`cid` = " . intval($_GET['cid']) . ")"))
									{
										// -- OK --
										$tpl->assign('REDIRECT_LOCATION','./acp.php?c=blog&section=category');
										$syslog->alert_success('{L_ALERT_BLOG_CATEGORY_DELETE_SUCCESS}');
										die();
									}
									else
									{
										$syslog->alert_error('{L_ALERT_BLOG_CATEGORY_DELETE_ERROR}');
										die();
									}
								}
								else
								{
									$syslog->permissions_error('{L_PERMISSIONS_BLOG_CATEGORY_DELETE}');
									die();
								}
								break;
						}
					}
					break;
				
				case('tag'):
					if(isset($_GET['mode']))
					{
						switch($_GET['mode'])
						{
							case('edit'):
								if(permissions('blog','tag','edit') && isset($_POST['tag_tag_generate']))
								{
									// -- slug --
									switch(intval($_POST['tag_tag_generate']))
									{
										case(0): $tag = $_POST['tag']['tag']; break;
										case(1): $tag = generate_slug($_POST['tag']['header']); break;
										case(2): $tag = $_POST['tag_tag_original']; break;
									}
									
									$sql = new MySQLObject();
									if($sql->query("
UPDATE " . $sql->table('blog_tags') . "
SET
	`header` = '" . $sql->escape($_POST['tag']['header']) . "',
	`tag` = '" . $sql->escape($tag) . "'
WHERE (`tag` = '" . $sql->escape($_GET['tag']) . "')"))
									{
										// -- OK --
										$tpl->assign('REDIRECT_LOCATION','./acp.php?c=blog&section=tag');
										$syslog->alert_success('{L_ALERT_BLOG_TAG_EDIT_SUCCESS}');
										die();
									}
									else
									{
										$syslog->alert_error('{L_ALERT_BLOG_TAG_EDIT_ERROR}');
										die();
									}
								}
								else
								{
									$syslog->permissions_error('{L_PERMISSIONS_BLOG_TAG_EDIT}');
									die();
								}
								break;
							
							case('delete'):
								if(permissions('blog','tag','delete'))
								{
									$sql = new MySQLObject();
									if($sql->query("DELETE FROM " . $sql->table('blog_tags') . " WHERE (`tag` = '" . $sql->escape($_GET['tag']) . "')"))
									{
										// -- OK --
										$tpl->assign('REDIRECT_LOCATION','./acp.php?c=blog&section=tag');
										$syslog->alert_success('{L_ALERT_BLOG_TAG_DELETE_SUCCESS}');
										die();
									}
									else
									{
										$syslog->alert_error('{L_ALERT_BLOG_TAG_DELETE_ERROR}');
										die();
									}
								}
								else
								{
									$syslog->permissions_error('{L_PERMISSIONS_BLOG_TAG_DELETE}');
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

if(defined('IN_AJAXREQUEST') && IN_AJAXREQUEST)
{
	if(isset($_GET['c'],$_GET['function'],$_GET['q']) && $_GET['c'] == 'blog')
	{
		// -- load the template for this request --
		$tpl->load_module_config('blog');
		
		if($_GET['function'] == 'post_find')
		{
			if(isset($_GET['id_posts'],$_GET['id_posts_found']))
			{
				if($_GET['q'] != '')
				{
					$terms = array_rmempty(explode(' ',$_GET['q']));
				
					$sql = new MySQLObject();
					// -- SQL query --
					$query = "
SELECT
	posts.`pid` as post_id,
	posts.`header` as post_header,
	posts.`slug` as post_slug,
	categories.`header` as category_header
FROM " . $sql->table('blog_posts') . " posts
LEFT JOIN " . $sql->table('blog_categories') . " categories
	ON posts.`category` = categories.`cid`
WHERE (";
	
					$i = 1;
					foreach($terms as $term)
					{
						$query .= "posts.`header` LIKE '%" . $term . "%'";
						if($i < count($terms))
						{
							$query .= " AND ";
							$i++;
						}
						else
							$query .= ") ORDER BY posts.`date` DESC";
					}
					
					if($sql->query($query))
					{
						$out = "
var posts = document.getElementById('" . $_GET['id_posts'] . "');
var postsFound = document.getElementById('" . $_GET['id_posts_found'] . "');";
	
						$out .= "
if(posts.style.display != 'none')
{
	posts.style.display = 'none';
}

if(postsFound.style.display != 'block')
{
	postsFound.style.display = 'block';
}";
				
						$num = $sql->num();
						if($num > 0)
						{
							$f_posts = array();
							foreach($sql->fetch() as $post)
							{
								$f_posts[] = array(
									'POST_HEADER' => $post->post_header,
									'POST_SLUG' => $post->post_slug,
									'POST_CATEGORY' => $post->category_header,
									'POST_LINK_EDIT' => './acp.php?c=blog&amp;section=post&amp;mode=edit&amp;pid=' . $post->post_id,
									'POST_LINK_DELETE' => './action.php?c=blog&amp;section=post&amp;mode=delete&amp;pid=' . $post->post_id
								);
							}
							$tpl->assign('BLOG_POSTS_FOUND',$f_posts,'foreach');
							$tpl->assign('BLOG_POSTS_FOUND',true,'if');
							$tpl->append($cfg['ajax']['tpl']['blog']['posts_find']);
						}
						else
						{
							$tpl->assign('BLOG_POSTS_FOUND',false,'if');
							$tpl->append($cfg['ajax']['tpl']['blog']['posts_find_no_result']);
						}
						
						$out .= "
var output = '" . javascript_prepare_string($tpl->display(false)) . "';
postsFound.innerHTML = output;";
					}
				}
				else
				{
					$out = "
posts.style.display = 'block';
postsFound.innerHTML = '';
postsFound.style.display = 'none';";
				}
				
				print($out);
			}
		}
		
		elseif($_GET['function'] == 'tag_find')
		{
			if(isset($_GET['id_tags'],$_GET['id_tags_found']))
			{
				if($_GET['q'] != '')
				{
					$terms = array_rmempty(explode(' ',$_GET['q']));
				
					$sql = new MySQLObject();
					// -- SQL query --
					$query = "
SELECT `tag`,`header`,COUNT(*) as count
FROM " . $sql->table('blog_tags') . "
WHERE (";
					$ors = "";
	
					$i = 1;
					foreach($terms as $term)
					{
						$query .= "`header` LIKE '%" . $term . "%'";
						$ors .= "`tag` LIKE '%" . $term . "%'";
						if($i < count($terms))
						{
							$query .= " AND ";
							$ors .= " AND ";
							$i++;
						}
						else
							$query .= " OR " . $ors . ")
GROUP BY `tag`
ORDER BY count DESC";
					}
					
					if($sql->query($query))
					{
						$out = "
var tags = document.getElementById('" . $_GET['id_tags'] . "');
var tagsFound = document.getElementById('" . $_GET['id_tags_found'] . "');";
	
						$out .= "
if(tags.style.display != 'none')
{
	tags.style.display = 'none';
}

if(tagsFound.style.display != 'block')
{
	tagsFound.style.display = 'block';
}";
				
						$num = $sql->num();
						if($num > 0)
						{
							$f_tags = array();
							foreach($sql->fetch() as $tag)
							{
								$f_tags[] = array(
									'TAG_HEADER' => $tag->header,
									'TAG_TAG' => $tag->tag,
									'TAG_LINK_EDIT' => './acp.php?c=blog&amp;section=post&amp;mode=edit&amp;tag=' . $tag->tag,
									'TAG_LINK_DELETE' => './action.php?c=blog&amp;section=post&amp;mode=delete&amp;tag=' . $tag->tag,
									'TAG_COUNT' => $tag->count
								);
							}
							$tpl->assign('BLOG_TAGS_FOUND',$f_tags,'foreach');
							$tpl->assign('BLOG_TAGS_FOUND',true,'if');
							
							if(!isset($_GET['mode']))
								$tpl->append($cfg['ajax']['tpl']['blog']['tags_find']);
							elseif($_GET['mode'] == 'selectbox')
								$tpl->append($cfg['ajax']['tpl']['blog']['tags_find_selectbox']);
						}
						else
						{
							$tpl->assign('BLOG_TAGS_FOUND',false,'if');
							
							if(!isset($_GET['mode']))
								$tpl->append($cfg['ajax']['tpl']['blog']['tags_find_no_result']);
							elseif($_GET['mode'] == 'selectbox')
								$tpl->append($cfg['ajax']['tpl']['blog']['tags_find_selectbox_no_result']);
						}
						
						$out .= "
var output = '" . javascript_prepare_string($tpl->display(false)) . "';
tagsFound.innerHTML = output;";
					}
				}
				else
				{
					$out = "
tags.style.display = 'block';
tagsFound.innerHTML = '';
tagsFound.style.display = 'none';";
				}
				
				print($out);
			}
		}
	}
}
?>