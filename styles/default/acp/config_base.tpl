	<div id="content">
		<h2>{L_CONFIG} &mdash; {L_MODULE_BASE_CONFIG}</h2>
		<img class="img" src="./images.php?image=config" alt="{L_CONFIG} &mdash; {CONFIG.HEADER}" />
		
		<form action="{CONFIG_ACTION}" method="post">
		<table class="config">
			<thead>
				<tr>
					<th class="col1">{L_HEADER}</th>
					<th>{L_VALUE}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{L_CONFIG_SITE_HEADER}</td>
					<td><input type="text" name="config[site_header]" value="{CONFIG.SITE_HEADER}" /></td>
				</tr>
				<tr>
					<td>{L_CONFIG_SITE_SLOGAN}</td>
					<td><input type="text" name="config[site_slogan]" value="{CONFIG.SITE_SLOGAN}" /></td>
				</tr>
				<tr>
					<td>{L_CONFIG_SITE_ROOT_PATH}</td>
					<td><input type="text" name="config[site_root_path]" value="{CONFIG.SITE_ROOT_PATH}" /></td>
				</tr>
				<tr>
					<td>{L_CONFIG_SHOW_ERRORS}</td>
					<td>
						<input type="radio" name="config[show_errors]" value="0" id="config_show_errors_false"{CONFIG.SHOW_ERRORS.FALSE.CHECKED} /> <label class="none" for="config_show_errors_false">{L_NO}</label>
						<input type="radio" name="config[show_errors]" value="1" id="config_show_errors_true"{CONFIG.SHOW_ERRORS.TRUE.CHECKED} /> <label class="none" for="config_show_errors_true">{L_YES}</label>
					</td>
			</tbody>
		</table>
		<div><input type="submit" name="action" value="{L_CONFIG_SUBMIT}" /></div>
		</form>
	</div>
