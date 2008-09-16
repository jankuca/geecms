<?php
class module_imgmanager
{
	public $dirpath = './images';
	public $tree = '';
	
	public function fullTree()
	{
		// -- load --
		$structure = array(
			'dirs' => $this->treeGetChildDirs($this->dirpath),
			'images' => $this->treeGetChildImages($this->dirpath)
		);
		
		$this->tree = '';
		$this->fullTreeMakeDirs($structure['dirs']);
		$this->fullTreeMakeImages($structure['images']);
		
		global $tpl;
		$tpl->assign(array(
			'IMGMANAGER_TREE' => $this->tree,
			'IMGMANAGER_ROOT_DIRS' => count($structure['dirs']),
			'IMGMANAGER_ROOT_IMAGES' => count($structure['images'])
		));
	}
	
	private function treeGetChildDirs($parent_dirpath)
	{
		$parent = dir($parent_dirpath);
		$items = array();
		while($file = $parent->read())
		{
			if($file != '.' && $file != '..')
			{
				if(is_dir($parent_dirpath . '/' . $file))
				{
					$items[] = array(
						'this' => $parent_dirpath . '/' . $file,
						'dirs' => $this->treeGetChildDirs($parent_dirpath . '/' . $file),
						'images' => $this->treeGetChildImages($parent_dirpath . '/' . $file),
					);
				}
			}
		}
		return($items);
	}
	private function treeGetChildImages($parent_dirpath)
	{
		$parent = dir($parent_dirpath);
		$items = array();
		while($file = $parent->read())
		{
			if($file != '.' && $file != '..')
			{
				if(is_file($parent_dirpath . '/' . $file))
				{
					$items[] = $parent_dirpath . '/' . $file;
				}
			}
		}
		return($items);
	}
	
	private function fullTreeMakeDirs($dirs)
	{
		foreach($dirs as $dir)
		{
			$this->fullTreeAdd('dirStart',$dir['this'],count($dir['dirs']),count($dir['images']));
			$this->fullTreeMakeDirs($dir['dirs']);
			$this->fullTreeMakeImages($dir['images']);
			$this->fullTreeAdd('dirEnd');
		}
	}
	private function fullTreeMakeImages($images)
	{
		foreach($images as $image)
		{
			$filename = explode('/',$image);
			$filename = $filename[count($filename) - 1];
			
			$ext = explode('.',$filename);
			$ext = $ext[count($ext) - 1];
			if(!in_array($ext,array('jpeg','jpg','gif','png')))
				$ext = 'other';
			
			$this->tree .= '<li class="image-' . $ext . '" title="' . $image . '"><strong>' . $filename . '</strong> (' . round(filesize($image) / 1024,1) . ' kB) <span class="buttons"><a><img src="http://tbn0.google.com/images?q=tbn:tUvpWTf9jFbmuM:http://www.koffice.org/developer/icons/hi16-action-item_rename.png" alt="{L_RENAME}" /></a> <a><img src="./styles/default/acp/images/delete.png" alt="{L_DELETE}" /></a></span></li>';
		}
	}
	
	private function fullTreeAdd($part,$dirPath = false,$countDirs = 0,$countImages = 0)
	{
		switch($part)
		{
			case('dirStart'):
				$dirPath = substr($dirPath,2);
				$dirId = str_replace('/','--',$dirPath);
				$dirName = explode('/',$dirPath);
				$dirName = $dirName[count($dirName)-1];
				
				$this->tree .=
'<li class="folder">' .
	'<div class="item">' .
		'<span class="expand" title="dir--' . $dirId . '"></span>' .
		'<strong>' . $dirName . '</strong>' .
		$countDirs . ' subsložek, ' . $countImages . ' obrázků ' .
		'<span class="buttons">' .
			'<a><img src="http://tbn0.google.com/images?q=tbn:tUvpWTf9jFbmuM:http://www.koffice.org/developer/icons/hi16-action-item_rename.png" alt="{L_RENAME}" /></a><a><img src="./styles/default/acp/images/delete.png" alt="{L_DELETE}" /></a>' .
		'</span>' .
	'</div>' .
	'<ul id="dir--' . $dirId . '">';
				break;
			
			case('dirEnd'):
				$this->tree .= 
	'</ul>' .
'</li>';
				break; 
		}
	}
}

global $mod;
$mod->modules['imgmanager'] = new module_imgmanager();

global $cfg;
//$mod->modules['imgmanager']->dirpath = $cfg['etc']['imgmanager_dirpath'];

if(defined('IN_SYS') && IN_SYS)
{
	if(!isset($_GET['c']))
	{
		
	}
}

if(defined('IN_IMGMANAGER') && IN_IMGMANAGER)
{
	global $tpl;
	$tpl->inc('imgmanager_window',1);
	$mod->modules['imgmanager']->fullTree();
	
	if(isset($_GET['targetid'])) { $tpl->assign('IMGMANAGER_TARGET_ID',$_GET['targetid']); }
	else { $tpl->assign('IMGMANAGER_TARGET_ID','imgmanager_target'); }
	
	if(isset($_GET['targetwindow'])) { $tpl->assign('IMGMANAGER_TARGET_WINDOW',$_GET['targetwindow']); }
	else { $tpl->assign('IMGMANAGER_TARGET_WINDOW','opener'); }
}

if(defined('IN_IMGMANAGER_FORM') && IN_IMGMANAGER_FORM)
{
	global $tpl;
	$tpl->inc('imgmanager_form',1);
}

if(defined('IN_ACP') && IN_ACP)
{
	global $cfg;
	$cfg['acp_modules_menu'][] = array(
		'LINK' => './acp.php?c=imgmanager',
		'HEADER' => '{L_MODULE_IMGMANAGER}',
		'ACTIVE' => (isset($_GET['c']) && $_GET['c'] == 'imgmanager')
			? $cfg['tpl']['class_subactive']
			: ''
	);
	
	if(!isset($_GET['c']))
	{
		$cfg['installed_modules'][] = array(
			'MODULE_HEADER' => '{L_MODULE_IMGMANAGER}',
			'MODULE_DESCRIPTION' => '{L_MODULE_IMGMANAGER_DESCRIPTION}',
			'MODULE_LINK' => './acp.php?c=imgmanager',
			'MODULE_IMAGE' => 'images.php?image=module_imgmanager'
		);
	}
	elseif($_GET['c'] == 'imgmanager' || ($_GET['c'] == 'config' && $_GET['module'] == 'blog'))
	{
		$cfg['acp_submenu'][] = array(
			'LINK' => './acp.php?c=imgmanager',
			'HEADER' => '{L_IMGMANAGER_MANAGER}',
			'ACTIVE' => (!isset($_GET['section']) && $_GET['c'] != 'config')
				? $cfg['tpl']['class_active']
				: ''
		);
		$cfg['acp_submenu'][] = array(
			'LINK' => './acp.php?c=config&amp;module=imgmanager',
			'HEADER' => '{L_CONFIG}',
			'ACTIVE' => ($_GET['c'] == 'config')
				? $cfg['tpl']['class_active']
				: ''
		);
		
		global $tpl;
		
		if($_GET['c'] == 'imgmanager')
		{
			$mod->modules['blog']->breadcrumbs[] = array(
				'LINK' => './acp.php?c=imgmanager',
				'HEADER' => '{L_MODULE_IMGMANAGER}'
			);
			
			$tpl->inc('imgmanager',1);
			
			$tpl->assign('SITE_TITLE','{L_MODULE_IMGMANAGER} &ndash; {SITE_HEADER}');
		}
	}
	
	if(isset($_GET['c']) && $_GET['c'] == 'config')
	{		
		if($_GET['module'] == 'imgmanager')
		{
			$mod->modules['base']->breadcrumbs[] = array(
				'LINK' => './acp.php?c=config&amp;module=imgmanager',
				'HEADER' => '{L_MODULE_IMGMANAGER_CONFIG}'
			);
			
			$mod->modules['imgmanager']->_module_config();
			
			$tpl->inc('config_imgmanager',1);
			
			$tpl->assign('CONFIG_ACTION','./action.php?c=config&amp;module=imgmanager');
			$tpl->assign('SITE_TITLE','{L_CONFIG} &mdash; {L_MODULE_IMGMANAGER_CONFIG} &ndash; {SITE_HEADER} / {L_ACP}');
			$tpl->assign('BREADCRUMBS',$mod->modules['base']->breadcrumbs,'foreach');
		}
	}
}

if(defined('IN_IMAGES') && IN_IMAGES)
{
	//$cfg['tpl']['images']['module_blog'] = '../../styles/default/acp/images/module_blog.png';
}

if(defined('IN_AJAXREQUEST') && IN_AJAXREQUEST)
{
	if(isset($_GET['c'],$_GET['function']) && $_GET['c'] == 'imgmanager')
	{
		switch($_GET['function'])
		{
			case('upload'):
				if(preg_match('#^image/(.*?)$#is',$_FILES['upload_file']['type']) && preg_match('#^/images(.*?)$#is',$_POST['upload_dir']) && file_exists('.' . $_POST['upload_dir']))
				{
					move_uploaded_file($_FILES['upload_file']['tmp_name'],'.' . $_POST['upload_dir'] . '/' . $_FILES['upload_file']['name']);
					
					$ext = explode('.',$_FILES['upload_file']['name']);
					$ext = $ext[count($ext) - 1];
					if(!in_array($ext,array('jpeg','jpg','gif','png')))
						$ext = 'other';
					
					echo('
<script type="text/javascript">
	parent.uploadEnd();
	parent.addImageItem(
		"dir' . str_replace('/','--',$_POST['upload_dir']) . '",
		"' . $_FILES['upload_file']['name'] . '",
		' . round($_FILES['upload_file']['size'] / 1024,1) . ',
		"' . $ext . '"
	);
</script>');
				}
				else
				{
					echo('
<script type="text/javascript">
	parent.uploadError();
</script>');
				}	
				break;
		}
	}
}
?>