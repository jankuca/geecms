	<div id="content">	
		<h2>{L_MODULE_USERS} &mdash; {L_USERS_GROUPS}</h2>
		
		<if(USER_GROUPS)><table>
			<thead>
				<tr>
					<th>{L_USERS_GROUP_HEADER}</th>
					<th class="action"></th>
					<th class="action"></th>
				</tr>
			</thead>
			<tbody>
<foreach(USER_GROUPS)>				<tr>
					<td><a href="<var(GROUP_LINK_READ)>"><var(GROUP_HEADER)></a><br /><small><var(GROUP_DESCRIPTION)></small></td>
					<td><a href="<var(GROUP_LINK_EDIT)>">{L_EDIT}</a></td>
					<td><a href="<var(GROUP_LINK_DELETE)>">{L_DELETE}</a></td>
				</tr>
</foreach(USER_GROUPS)>			</tbody>
		</table><else(USER_GROUPS)>
		<p>{L_USERS_NO_GROUPS}</p></if(USER_GROUPS)>
	</div>
