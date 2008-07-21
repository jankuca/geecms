	<div id="content">
		<h2>{L_MODULE_MENU} &mdash; {L_MENU_ITEM_ADD}</h2>
		
<foreach(ADD)>		<h3 onclick="document.getElementById('add-list-<var(MODULE_NAME)>').style.display='block';"><var(MODULE_HEADER)></h3>
		<div style="display:none;" class="add-list" id="add-list-<var(MODULE_NAME)>">
<var(MODULE_CONTENT)>		</div>
</foreach(ADD)>
	</div>
