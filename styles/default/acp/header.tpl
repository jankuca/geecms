<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<base href="{SITE_ROOT_PATH}" />
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" media="screen" href="./styles/default/acp/css/screen.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="./styles/default/acp/sifr/sIFR-screen.css" />
	
	<script type="text/javascript" src="./app/lib/js/jquery.js"></script>
	<script type="text/javascript" src="./app/lib/js/sifr/sifr.js"></script>
	<script type="text/javascript" src="./app/lib/js/sifr/sifr-addons.js"></script>

	<title>{SITE_TITLE}</title>
</head>
<body>
<if(INFOBAR)><div id="infobar" style="display: none;">{INFOBAR}</div>
<script type="text/javascript"><!--
$(document).ready(function(){
	$("#infobar").slideToggle("slow");
	$("#infobar").click(function(){
	  $("#infobar").slideToggle("slow");
	});
});
--></script></if(INFOBAR)>

<div id="header"><div class="wrapper">
	<h1><a href="{U_ACP}">{SITE_HEADER} / {L_ACP}</a></h1>
	<div id="logininfo">{L_USERS_CURRENT_USER}</div>
</div></div>
<div id="breadcrumbs"><div class="wrapper"><a href="{U_ACP}">{SITE_HEADER} / {L_ACP}</a><foreach(BREADCRUMBS)> <span></span> <a href="<var(LINK)>"><var(HEADER)></a></foreach(BREADCRUMBS)></div></div>
<div class="wrapper">
