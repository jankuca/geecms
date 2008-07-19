	<div id="content">
		<h2>{L_USERS_USERS}</h2>
		<h3>{L_USERS_SEARCH}</h3>
		<h3>{L_USERS_USERLIST}</h3>
		<table>
			<thead>
				<tr>
					<th class="col2">{L_USERS_USER_USERNAME}</th>
					<th class="action"></th>
					<th class="action"></th>
				</tr>
			</thead>
			<tbody>
<foreach(USERS_USERLIST)>				<tr>
					<td><a href="<var(USER_LINK_READ)>"><var(USER_USERNAME)></a></td>
					<td><a href="<var(USER_LINK_EDIT)>">{L_EDIT}</a></td>
					<td><a href="<var(USER_LINK_DELETE)>">{L_DELETE}</a></td>
				</tr>
</foreach(USERS_USERLIST)>			</tbody>
		</table>
	</div>
