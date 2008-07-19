	<div id="content">
		<h2>{L_ACP_INDEX}</h2>
		<h3>Nainstalované moduly (funkcionality)</h3>
<if(INSTALLED_MODULES)><foreach(INSTALLED_MODULES)>		<h4><var(MODULE_HEADER)></h4>
		<img class="img" src="<var(MODULE_IMAGE)>" alt="<var(MODULE_HEADER)>" />
		<var(MODULE_DESCRIPTION)>
		<ul><li><a href="<var(MODULE_LINK)>">{L_MODULE_LINK}</a></li></ul></foreach(INSTALLED_MODULES)>
		<else(INSTALLED_MODULES)>
		<p>{L_NO_INSTALLED_MODULES}</p>
		</if(INSTALLED_MODULES)>
	</div>
