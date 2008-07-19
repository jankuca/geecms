<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<base href="{SITE_ROOT_PATH}" />
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" media="screen" href="./styles/default/css/screen.css" />
	<title>{SITE_TITLE}</title>
</head>
<body>
<div id="header">
	<h1>{SITE_HEADER}</h1>
	<ul>
<foreach(SITE_MENU)>		<li<var(ACTIVE)>><a href="<var(LINK)>"><var(HEADER)></a></li>
</foreach(SITE_MENU)>	</ul>
</div>
<div id="web">
