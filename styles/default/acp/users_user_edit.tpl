	<div id="content">
		<h2>{L_MODULE_USERS} &mdash; {L_USERS_USER_EDIT}: <strong>{USER.USERNAME}</strong></h2>
		<form action="{USER_ACTION}" method="post" onsubmit="return hmac_hash(this);">
		<div>
			<label for="user_username">{L_USERS_USER_USERNAME}:</label>
			<input type="text" name="user_username" id="user_username" value="{USER.USERNAME}" /><br />
			
			<label for="user_email">{L_USERS_USER_EMAIL}:</label>
			<input type="text" name="user_email" id="user_email" value="{USER.EMAIL}" /><br />

			<h3>{L_USERS_USER_PASSWORD_CHANGE}:</h3>
			<fieldset>
				<legend><input type="checkbox" name="user_password_change" value="1" id="user_password_change" /> <label for="user_password_change" class="none">{L_USERS_USER_PASSWORD_CHANGE}</label></legend>
				<input type="radio" name="user_password_generate" value="1" id="user_password_generate" checked="checked" />
				<label for="user_password_generate" class="none">{L_USERS_USER_PASSWORD_GENERATE}</label><br />
				
				<input type="radio" name="user_password_generate" value="0" id="user_password_input" />
				<label for="user_password_input" class="none">{L_USERS_USER_PASSWORD_INPUT}</label>
				
				<div id="user_password_div" style="display:none;padding:2px 0 0 20px;">
					<label for="user_password" style="float: left; width:100px;padding: 0 10px 0 0;text-align:right;">{L_USERS_USER_PASSWORD}:</label>
					<input type="password" name="user_password" id="user_password" onkeyup="getPasswordStrength()" style="float: left;" />
					<div style="background: url('./images.php?image=password_strength_weak') no-repeat 10px center; color:#FF0000; padding-left: 30px; float:left;" id="password_strength">Weak password</div>
					<label for="user_password_confirm" style="float: left; clear:left;width:100px;padding: 0 10px 0 0;text-align:right;">{L_USERS_USER_PASSWORD_CONFIRM}:</label>
					<input type="password" name="user_password_confirm" id="user_password_confirm" style="float: left;" />
					<input type="hidden" name="user_password_md5" id="user_password_md5" />
					<br class="clear" /><br />
				</div><br />
			</fieldset>

			<h3>{L_USERS_GROUPS}</h3>
<if(USER_GROUPS)>			<ul>
<foreach(USER_GROUPS)>			<li><input type="checkbox" name="user_groups[]" value="<var(GROUP_ID)>" id="user_groups_<var(GROUP_ID)>"<var(CHECKED)> /> <label for="user_groups_<var(GROUP_ID)>" class="none"><var(GROUP_HEADER)></label></li>
</foreach(USER_GROUPS)>		</ul>
<else(USER_GROUPS)>		<p>{L_USERS_NO_GROUPS}</p>
</if(USER_GROUPS)>
			<input type="submit" name="action" value="{L_USERS_USER_EDIT}" id="user_submit" />
		</div>
		</form>
		<script type="text/javascript" src="./app/lib/js/md5.js"></script>
		<script type="text/javascript" src="./app/lib/js/password.js"></script>
		<script type="text/javascript"><!--
function hmac_hash(form)
{
	if(document.getElementById('user_password').value == document.getElementById('user_password_confirm').value)
	{
		document.getElementById('user_password_md5').value = hex_md5(document.getElementById('user_password').value);
		form.submit();
	}
	else
	{
		alert('{L_USERS_USER_PASSWORD_MATCH}');
	}
	return(false);
}

$(document).ready(function(){
	$("#user_password_input").change(function(){
	  $("#user_password_div").slideToggle("slow");
	  document.getElementById('user_submit').disabled = true;
	});
	$("#user_password_generate").change(function(){
	  $("#user_password_div").slideToggle("slow");
	  document.getElementById('user_submit').disabled = false;
	});
});
		--></script>
	</div>
