	<div id="content">
		<h2>{L_MODULE_MENU}</h2>
		<img class="img" src="./images.php?image=module_menu" alt="{L_MODULE_MENU}" />
		{L_MODULE_MENU_DESCRIPTION}
		
		<h3>{L_MENU_MAINMENU}</h3>
		<table>
			<thead>
				<tr>
					<th>{L_TEXT}</th>
					<th class="action" colspan="2">{L_MOVE}</th>
					<th class="action">{L_EDIT}</th>
					<th class="action">{L_DELETE}</th>
					<th class="action">{L_SHOWING}</th>
				</tr>
			</thead>
			<tbody>
				<foreach(MENU_MAINMENU)><tr>
					<td title="<var(ITEM_LINK)>"><var(ITEM_HEADER)></td>
					<td class="actionhalf"><var(ITEM_LINK_MOVEUP)></td>
					<td class="actionhalf"><var(ITEM_LINK_MOVEDOWN)></td>
					<td><a href="<var(ITEM_LINK_EDIT)>">{L_EDIT}</a></td>
					<td><a href="<var(ITEM_LINK_DELETE)>">{L_DELETE}</a></td>
					<td><a href="<var(ITEM_LINK_SHOW_HIDE)>"><var(ITEM_TEXT_SHOW_HIDE)></td>
				</tr></foreach(MENU_MAINMENU)>
			</tbody>
		</table>
	</div>
