	<div id="content">
		<h2>{L_MODULE_USERS} &mdash; {L_USERS_GROUP_ADD}</h2>
		<form action="{GROUP_ACTION}" method="post">
		<div>
			<label for="group_header">{L_USERS_GROUP_HEADER}:</label>
			<input type="text" name="group_header" id="group_header" /><br />
			
			<label for="group_description">{L_USERS_GROUP_DESCRIPTION}:</label>
			<input type="text" name="group_description" id="group_description" class="half" /><br />

			<h3>{L_USERS_GROUP_PERMISSIONS}</h3>
				<table>
					<thead>
						<tr>
							<th class="col1">{L_PERMISSIONS_HEADER}</th>
							<th>{L_PERMISSIONS_VALUES}</th>
						</tr>
					</thead>
					<tbody>
<foreach(CP_MODULES)>
	<tr class="heading">
		<td colspan="2"><h4><var(CP_MODULE_HEADER)></h4></td>
	</tr>
<foreach(CP_NAMES.<var(CP_MODULE_NAME)>)>
	<tr>
		<td class="col1"><var(CP_NAME_HEADER)></td>
		<td>
<foreach(CP_VALUES.<var(CP_NAME_NAME)>)>
			<input type="checkbox" name="group_permissions[<var(CP_MODULE_NAME)>][<var(CP_NAME_NAME)>][]" value="<var(CP_VALUE_NAME)>" id="<var(CP_NAME_NAME)>-<var(CP_VALUE_NAME)>" />
			<label for="<var(CP_NAME_NAME)>-<var(CP_VALUE_NAME)>" class="none"><var(CP_VALUE_HEADER)></label>
</foreach(CP_VALUES.<var(CP_NAME_NAME)>)>
		</td>
	</tr>
</foreach(CP_NAMES.<var(CP_MODULE_NAME)>)>
</foreach(CP_MODULES)>

				</tbody>
			</table>

			<input type="submit" name="action" value="{L_USERS_GROUP_ADD}" id="user_submit" />
		</div>
		</form>
	</div>
