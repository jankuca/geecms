<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<base href="{SITE_ROOT_PATH}" />
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
	<title>{L_MODULE_IMGMANAGER}</title>
	<link rel="stylesheet" media="screen,projection,tv" type="text/css" href="./styles/default/acp/css/imgmanager.css" />
	<script type="text/javascript">
var L_SUBDIRS_COUNT = '{L_SUBDIRS_COUNT}';
var L_IMAGES_COUNT = '{L_IMAGES_COUNT}';
<if(IMGMANAGER_TARGET)>
var parentWindowTargetId = '{IMGMANAGER_TARGET}';<else(IMGMANAGER_TARGET)>
var parentWindowTargetId = false;</if(IMGMANAGER_TARGET)>
	</script>
	<script type="text/javascript" src="./app/lib/js/jquery.js"></script>
	<script type="text/javascript" src="./styles/default/acp/js/imgmanager.js"></script>
</head>
<body>
<div id="toolbar"><a onclick="showUploadDialog();" id="uploadFile">{L_IMGMANAGER_UPLOAD_FILE}</a><a onclick="showCreateDirDialog();" id="createDir">{L_IMGMANAGER_CREATE_DIR}</a></div>
<div id="root">
	<div class="folder" id="rootdir"><div class="item" id="selectedDir"><span id="expand-pack--dir--images" title="dir--images"></span><strong>images</strong>{IMGMANAGER_ROOT_DIRS} {L_SUBDIRS_COUNT}, {IMGMANAGER_ROOT_IMAGES} {L_IMAGES_COUNT}</div></div>
	<ul id="dir--images">{IMGMANAGER_TREE}</ul>
</div>
<iframe id="target_frame" name="target_frame" src="about:blank"></iframe>
<div id="dialog">
	<a onclick="hideDialog();hideHider();" id="dialogClose">&nbsp;</a>
	<div id="dialogSpace">
		<div id="dialogUpload">
			<h2>{L_IMGMANAGER_UPLOAD_FILE}</h2>
			<form action="./ajaxrequest.php?c=imgmanager&amp;function=upload" method="post" enctype="multipart/form-data" target="target_frame" onsubmit="return(uploadStart());">
			<div id="dialogUploadForm">
				<label for="upload_file">{L_FILE}:</label>
				<input type="file" name="upload_file" id="upload_file" accept="image/*" /><br />
				<label for="upload_dir">{L_TARGET_DIR}:</label>
				<input type="text" name="upload_dir" id="upload_dir" value="/images" readonly="readonly" /><br />
				<input type="submit" value="{L_UPLOAD}" id="upload_submit" />
				<span class="warning">{L_IMGMANAGER_UPLOAD_MIMETYPES}</span>
			</div>
			</form>
		</div>
		<div id="dialogDelete">
			<h2>{L_IMGMANAGER_DELETE_FILE}</h2>
			<form action="./ajaxrequest.php?c=imgmanager&amp;function=delete" method="post" target="target_frame" onsubmit="return(deleteStart());">
			<div id="dialogDeleteForm">
				{L_IMGMANAGER_DELETE_FILE_CONFIRM}
				<input type="hidden" name="delete_file" id="delete_file" value="" />
				<input type="submit" value="{L_YES}" id="delete_submit" />
				<input type="button" value="{L_NO}" onclick="deleteEnd(true);" />
				<span class="warning">{L_IMGMANAGER_ACTION_UNDOABLE}</span>
			</div>
			</form>
		</div>
		<div id="dialogCreateDir">
			<h2>{L_IMGMANAGER_CREATE_DIR}</h2>
			<form action="./ajaxrequest.php?c=imgmanager&amp;function=createdir" method="post" target="target_frame" onsubmit="return(createDirStart());">
			<div id="dialogCreateDirForm">
				<label for="createdir_dirname">{L_DIRNAME}:</label>
				<input type="text" name="createdir_dirname" id="createdir_dirname" value="" /><br />
				<label for="createdir_parent">{L_TARGET_DIR}:</label>
				<input type="text" name="createdir_parent" id="createdir_parent" value="" readonly="readonly" /><br />
				<input type="submit" value="{L_CREATE}" id="createdir_submit" />
				<input type="button" value="{L_STORNO}" onclick="createDirEnd(true);" />
			</div>
			</form>
		</div>
		<span id="dialogStatus"></span>
	</div>
</div>
</body>
</html>
