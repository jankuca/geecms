	<div id="column">
<if(ACP_MENU)>		<h3>{L_MENU}</h3>
		<ul>
<foreach(ACP_MENU)>			<li<var(ACTIVE)>><a href="<var(LINK)>"><var(HEADER)></a></li>
</foreach(ACP_MENU)>		</ul></if(ACP_MENU)>

<if(ACP_MODULES_MENU)>		<h3>{L_MODULES_MENU}</h3>
		<ul>
<foreach(ACP_MODULES_MENU)>			<li<var(ACTIVE)>><a href="<var(LINK)>"><var(HEADER)></a></li>
</foreach(ACP_MODULES_MENU)>		</ul></if(ACP_MODULES_MENU)>

<if(ACP_SUBMENU)><h3>{L_SUBMENU}</h3>
		<ul>
<foreach(ACP_SUBMENU)>			<li<var(ACTIVE)>><a href="<var(LINK)>"><var(HEADER)></a></li>
</foreach(ACP_SUBMENU)>		</ul></if(ACP_SUBMENU)>
	</div>
