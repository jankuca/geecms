	<div id="content">
		<h2>{L_MODULE_BLOG} &mdash; {L_BLOG_CATEGORIES}</h2>
		<h3>{L_BLOG_CATEGORIES}</h3>
		<table>
			<thead>
				<tr>
					<th>{L_HEADER}</th>
					<th class="action">{L_EDIT}</th>
					<th class="action">{L_DELETE}</th>
				</tr>
			</thead>
			<tbody>
				<foreach(BLOG_CATEGORIES)><tr>
					<td title="<var(CATEGORY_SLUG)>"><var(CATEGORY_HEADER)></td>
					<td><a href="<var(CATEGORY_LINK_EDIT)>">{L_EDIT}</a></td>
					<td><a href="<var(CATEGORY_LINK_DELETE)>">{L_DELETE}</a></td>
				</tr></foreach(BLOG_CATEGORIES)>
			</tbody>
		</table>
	</div>
