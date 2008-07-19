<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<base href="{SITE_ROOT_PATH}" />
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" media="screen" href="./styles/default/acp/css/loginbox.css" />
	<title>{SITE_TITLE}</title>
</head>
<body>
<div id="success" class="alert">
{ALERT_SUCCESS_MESSAGE}
<if(NO_MENU)>
<else(NO_MENU)>	<ul>
		<li><a href="{U_REFERER}">{L_BACK}</a></li>
		<li><a href="{U_INDEX}">{L_INDEX}</a></li>
		<if(PERMISSION_ACP)><li><a href="{U_ACP}">{L_ACP}</a></li></if(PERMISSION_ACP)>
	</ul></if(NO_MENU)>
</div>
<script type="text/javascript"><!--
setTimeout("window.location.href = '{REDIRECT_LOCATION}';",2000);
--></script>
</body>
</html>
