<?php
$tpl->assign(array(
	// MODULE
	'L_MODULE_USERS' => 'Uživatelé',
	'L_MODULE_USERS_DESCRIPTION' => '<p>Kompletní správa uživatelských účtů. Možnost přidávat, mazat nebo upravovat uživatele a uživatelské skupiny. Podrobné přidělování oprávnění jednotlivým uživatelům nebo celým uživatelským skupinám.</p>',

	// LOGIN
	'L_LOGIN_WELCOME' => 'Přihlášení do systému GeeCMS',
	'L_LOGIN' => 'Přihlásit',
	'L_LOGOUT' => 'Odhlásit se',
	'L_USERS_CURRENT_USER' => 'Jste přihlášen jako <strong>{CURRENT_USER.USERNAME}</strong>.<br /><a href="{U_LOGOUT}">{L_LOGOUT}</a>',

	'L_LOGIN_NEEDED_JS' => '<p>K bezpečnému přihlášení je potřeba JavaScript. Váš prohlížeč nepodporuje JavaScript nebo ho máte vypnutý.</p>
<p><strong>Zapněte si JavaScript</strong> nebo si <strong>nainstalujte moderní prohlížeč</strong>:</p>
<ul>
	<li><a href="http://www.opera.com/products/">Opera</a></li>
	<li><a href="http://www.mozilla.com/en-US/firefox/">FireFox</a></li>
	<li><a href="http://www.microsoft.com/windows/downloads/ie/getitnow.mspx">Internet Explorer</a></li>
</ul>',
	'L_LOGIN_NEEDED_COOKIES' => '<p>K bezpečnému přihlášení jsou pořeba Cookies. Pokud se Vám nepodaří přihlásit, zkontrolujte, jestli máte Cookies povolené.</p>',

	// ERRORS,SUCCESSES
	'L_ALERT_USERS_USER_ADD_SUCCESS' => '<p>Uživatel byl úspěšně přidán.</p>',
	'L_ALERT_USERS_USER_ADD_ERROR' => '<p>Uživatel nebyl přidán.</p>',
	'L_ALERT_USERS_USER_EDIT_SUCCESS' => '<p>Uživatel byl úspěšně upraven.</p>',
	'L_ALERT_USERS_USER_EDIT_ERROR' => '<p>Uživatel nebyl upraven.</p>',
	'L_ALERT_USERS_USER_DELETE_SUCCESS' => '<p>Uživatel byl úspěšně smazán.</p>',
	'L_ALERT_USERS_USER_DELETE_ERROR' => '<p>Uživatel nebyl smazán.</p>',
	'L_ALERT_USERS_USER_EMAIL_REQUIRED' => '<p>Email je vyžadován.</p>',
	
	'L_ALERT_USERS_PERMISSIONS_SET' => '<p>Oprávnění nebyla přidělena.</p>',
	'L_ALERT_USERS_GROUP_ADD_SUCCESS' => '<p>Uživatelská skupina byla úspěšně přidána.</p>',
	'L_ALERT_USERS_GROUP_ADD_ERROR' => '<p>Uživatelská skupina nebyla přidána.</p>',
	'L_ALERT_USERS_GROUP_EDIT_SUCCESS' => '<p>Uživatelská skupina byla úspěšně upravena.</p>',
	'L_ALERT_USERS_GROUP_EDIT_ERROR' => '<p>Uživatelská skupina nebyla upravena.</p>',
	'L_ALERT_USERS_GROUP_DELETE_SUCCESS' => '<p>Uživatelská skupina byla úspěšně smazána.</p>',
	'L_ALERT_USERS_GROUP_DELETE_ERROR' => '<p>Uživatelská skupina nebyla smazána.</p>',

	'L_PERMISSIONS_USERS_USER_ADD' => '<p>Nemáte oprávnění přidávat uživatele.</p>',
	'L_PERMISSIONS_USERS_USER_EDIT' => '<p>Nemáte oprávnění upravovat uživatele.</p>',
	'L_PERMISSIONS_USERS_USER_DELETE' => '<p>Nemáte oprávnění mazat uživatele.</p>',
	'L_PERMISSIONS_USERS_GROUP_ADD' => '<p>Nemáte oprávnění přidávat uživatelské skupiny.</p>',
	'L_PERMISSIONS_USERS_GROUP_EDIT' => '<p>Nemáte oprávnění upravovat uživatelské skupiny.</p>',
	'L_PERMISSIONS_USERS_GROUP_DELETE' => '<p>Nemáte oprávnění mazat uživatelské skupiny.</p>',

	'L_ALERT_USERS_PASSWORD_WRONG' => '<p>Zadali jste chybné heslo nebo neexistující uživatelské jméno.</p>',
	'L_ALERT_USERS_LOGIN_SUCCESS' => '<p>Přihlášení proběhlo úspěšně.</p>',

	// EMAILS
	'L_EMAIL_SUBJECT_USERS_USER_REGISTRATION' => 'Registrace — ' . $tpl->assign['SITE_HEADER'],
	'L_EMAIL_USERS_USER_REGISTRATION' => 'Byl jste úspěšně zaregistrován na webu <a href="' . $tpl->assign['SITE_ROOT_PATH'] . '">' . $tpl->assign['SITE_HEADER'] . '</a>. Tento email uschovejte v bezpečí, protože heslo bylo v systému zašifrováno a nebylo by možné Vám ho znovu zaslat, Vaše přihlašovací údaje jsou následující:',

	// INFOBAR
	'L_INFOBAR_COOKIES_DISABLED' => 'Cookies jsou vypnuté. Aby nedocházelo k automatickému odhlašování, je potřeba povolit cookies!',

	// USERS
	'L_USERS_USER' => 'Uživatel',
	'L_USERS_USERS' => 'Uživatelé',
	'L_USERS_SEARCH' => 'Hledat uživatele',
	'L_USERS_USERLIST' => 'Seznam uživatelů',
	'L_USERS_USER_USERNAME' => 'Uživatelské jméno',
	'L_USERS_USER_PASSWORD' => 'Heslo',
	'L_USERS_USER_PASSWORD_CONFIRM' => 'Kontrola hesla',
	'L_USERS_USER_PASSWORD_CHANGE' => 'Změnit heslo',
	'L_USERS_USER_PASSWORD_GENERATE' => 'Vygenerovat heslo',
	'L_USERS_USER_PASSWORD_INPUT' => 'Zadat vlastní heslo',
	'L_USERS_USER_PASSWORD_MATCH' => 'Hesla se neshodují!',
	'L_USERS_USER_EMAIL' => 'E-mail',

	// USER GROUPS
	'L_USERS_GROUPS' => 'Uživatelské skupiny',
	'L_USERS_GROUP_HEADER' => 'Název skupiny',
	'L_USERS_GROUP_DESCRIPTION' => 'Popis skupiny',
	'L_USERS_GROUP_PERMISSIONS' => 'Oprávnění skupiny',
	'L_USERS_NO_GROUPS' => 'Žádné uživatelské skupiny',

	// PERMISSIONS
	'L_PERMISSIONS_HEADER' => 'Název',
	'L_PERMISSIONS_VALUES' => 'Hodnoty',

	'L_PERMISSIONS_VALUE.change_groups' => 'Měnit skupiny',

	'L_PERMISSIONS_HEADER.user' => 'Uživatel',
	'L_PERMISSIONS_HEADER.group' => 'Uživatelská skupina',

	// ACTIONS
	'L_USERS_USER_ADD' => 'Přidat uživatele',
	'L_USERS_USER_EDIT' => 'Upravit uživatele',
	'L_USERS_GROUP_ADD' => 'Přidat uživatelskou skupinu',
	'L_USERS_GROUP_EDIT' => 'Upravit uživatelskou skupinu',

	// OVERVIEW
	'L_USERS_COUNT' => 'Počet uživatelů',
	'L_GROUPS_COUNT' => 'Počet uživatelských skupin'
));
?>