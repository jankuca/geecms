<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<base href="{SITE_ROOT_PATH}" />
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" media="screen" href="./styles/default/acp/css/loginbox.css" />
	<title>{SITE_TITLE}</title>
	
	<script type="text/javascript" src="./app/lib/js/md5.js"></script>
	<script type="text/javascript">
function hmac_hash(form)
{
	document.getElementById('login_password_md5').value = hex_md5(document.getElementById('login_password').value);

	document.getElementById('login_password').disabled = true;
	form.submit();
	document.getElementById('login_password').disabled = false;
	
	return(false);
}
	</script>
</head>
<body>
<script type="text/javascript">
document.write('<form action="{LOGIN_ACTION}" method="post" onsubmit="return hmac_hash(this);">'+
'<div id="loginbox">'+
'	<h1>{L_LOGIN_WELCOME}</h1>'+
'	<label for="login_username">{L_USERS_USER_USERNAME}:</label>'+
'	<input class="text" type="text" name="login_username" id="login_username" /><br />'+
'	<label for="login_password">{L_USERS_USER_PASSWORD}:</label>'+
'	<input class="text" type="password" name="login_password" id="login_password" /><br />'+
'	<input type="hidden" name="login_challenge" id="login_challenge" value="{LOGIN_CHALLENGE}" />'+
'	<input type="hidden" name="login_password_md5" id="login_password_md5" value="" />'+
'	<input class="submit" type="submit" name="action" value="{L_LOGIN}" /><br class="clear" />'+
'</div>'+
'</form>'+
'<div id="cookies">'+
'{L_LOGIN_NEEDED_COOKIES}'+
'</div>');
</script>
<noscript>
	<div id="security">
{L_LOGIN_NEEDED_JS}
	</div>
</noscript>
</body>
</html>
