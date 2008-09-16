	<div id="content">
		<h2>{L_MODULE_UPDATER}</h2>
		<img class="img" src="./images.php?image=module_updater" alt="{L_MODULE_UPDATER}" />
		{L_MODULE_UPDATER_DESCRIPTION}
		
		<h3>{L_UPDATER_AVAILABLE_UPDATES}</h3>
		<div id="updaterStatus"><br />{L_UPDATER_NO_REQUEST}<br /><input type="button" value="{L_UPDATER_CHECK_FOR_UPDATES}" onclick="updaterCheckUpdates();" /><br /><br /></div>
		<ul id="updaterUpdates"></ul>
	</div>
	<script type="text/javascript" src="./app/lib/js/jquery.vkfade.js"></script>
	<script type="text/javascript">
function updaterCheckUpdates(noblink)
{
	document.getElementById('updaterUpdates').innerHTML = '';
	document.getElementById('updaterStatus').innerHTML = '<br /><img src="./styles/default/acp/images/throbber.gif" class="throbber" /><br /><br />';
	createXMLHttpRequestObject();
	XHR.open('GET','{SITE_ROOT_PATH}ajaxrequest.php?c=updater&function=check_for_updates');
	XHR.onreadystatechange = function(){ updaterGetUpdatesList(noblink); };
	XHR.send(null);
}
function updaterGetUpdatesList(noblink)
{
	if(XHR.status == 200)
	{
		switch(XHR.readyState)
		{
			case(4):
				if(XHR.responseText != 'NO_UPDATES' && XHR.responseText != 'ERROR')
				{
					if(!noblink)
						Fat.fade_element('updaterStatus',false,1000,'#C7F222');
					document.getElementById('updaterStatus').innerHTML = '<br /><strong>{L_UPDATER_NEW_UPDATES_AVAILABLE}</strong><br /><br />';
					
					documentUpdater = XHR.responseXML;
					updaterUpdates = document.getElementById('updaterUpdates');
					updates = documentUpdater.firstChild.firstChild.childNodes;
					
					var stop = false;
					for(var i = 0; i < updates.length; i++)
					{
						var update = document.createElement('li');
						update.id = 'update-' + updates[i].childNodes[0].firstChild.data;
						update.className = 'updater_update';
						switch(updates[i].attributes[0].value)
						{
							case('IS'): var star = '<img src="./styles/default/acp/images/star_red.png" alt="IS" />'; break;
							case('I'): var star = '<img src="./styles/default/acp/images/star.png" alt="I" />'; break;
							case('C'): var star = '<img src="./styles/default/acp/images/star_green.png" alt="C" />'; break;
						}
						update.innerHTML = '<span class="star">' + star + '</span> <strong>' + updates[i].childNodes[1].firstChild.data + '</strong> <small>' + updates[i].childNodes[0].firstChild.data + '</small><br />{L_AFFECTED_MODULES}: ' + updates[i].childNodes[2].firstChild.data;
						
						if(!stop && (i == 0 || (updates[i].attributes[0].value == 'IS' || updates[i].attributes[0].value == 'I')))
						{
							if(updates[i].attributes[0].value == 'IS' || updates[i].attributes[0].value == 'I')
							{
								stop = true;
								update.innerHTML += '<span class="button-install-update" id="button-install-' + updates[i].childNodes[0].firstChild.data + '"><a onclick="updaterInstallUpdate(\'' + updates[i].childNodes[0].firstChild.data + '\');">{L_UPDATER_INSTALL_UPDATE}</a></span>';
							}
							else
							{
								update.innerHTML += '<span class="button-install-update" id="button-install-' + updates[i].childNodes[0].firstChild.data + '"><a onclick="updaterIgnoreUpdate(\'' + updates[i].childNodes[0].firstChild.data + '\');">{L_UPDATER_IGNORE_UPDATE}</a> | <a onclick="updaterInstallUpdate(\'' + updates[i].childNodes[0].firstChild.data + '\');">{L_UPDATER_INSTALL_UPDATE}</a></span>';
							}
						}
						update.style.display = 'none';
						updaterUpdates.appendChild(update);
						$(update).slideToggle('slow');
					}
				}
				else if(XHR.responseText == 'NO_UPDATES')
				{
					Fat.fade_element('updaterStatus',false,1000,'#FFFFAA');
					document.getElementById('updaterStatus').innerHTML = '<br /><strong>{L_UPDATER_NO_UPDATES_AVAILABLE}</strong><br /><br />';
				}
				else if(XHR.responseText == 'ERROR')
				{
					Fat.fade_element('updaterStatus',false,1000,'#FFAAAA');
					document.getElementById('updaterStatus').innerHTML = '<br /><img src="./styles/default/acp/images/close.png" alt="X" />&nbsp;&nbsp;&nbsp;<strong>{L_UPDATER_OUT_OF_SERVICE}</strong><br /><br />';
				}
				
				XHR = null;
				break;
		}
	}
}

function updaterInstallUpdate(code)
{
	createXMLHttpRequestObject();
	XHR.open('GET','{SITE_ROOT_PATH}ajaxrequest.php?c=updater&function=install_update&code=' + code,true);
	XHR.onreadystatechange = function(){ updaterGetInstallResult(code); };
	XHR.send(null);
}
function updaterIgnoreUpdate(code)
{
	createXMLHttpRequestObject();
	XHR.open('GET','{SITE_ROOT_PATH}ajaxrequest.php?c=updater&function=ignore_update&code=' + code,true);
	XHR.onreadystatechange = function(){ updaterGetInstallResult(code); };
	XHR.send(null);
}
function updaterGetInstallResult(code)
{
	switch(XHR.readyState)
	{
		case(1):
			document.getElementById('update-' + code).lastChild.innerHTML = '<img src="./styles/default/acp/images/throbber-small.gif" alt="" />';
			break;
		case(4):
			if(XHR.responseText == 'OK')
			{
				Fat.fade_element('updaterStatus',false,1000,'#C7F222');
				updateLiNode = document.getElementById('update-' + code);
				document.getElementById('updaterStatus').innerHTML = '<br /><strong>{L_UPDATER_UPDATE_INSTALLED}</strong><br /><br />';
				updateLiNode.removeChild(document.getElementById('button-install-' + code));
				$(updateLiNode).slideToggle('slow');
				updaterCheckUpdates(true);
			}
			else
				document.getElementById('update-' + code).lastChild.innerHTML = '<img src="./styles/default/acp/images/close.ico" alt="" />';
			break;
	}
}
	</script>
