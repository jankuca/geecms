	<div id="content">
		<h2>{L_MODULE_BLOG} &mdash; {L_BLOG_TAGS}</h2>
		
		<h3>{L_BLOG_TAG_FIND}</h3>
		<input type="text" value="{L_SEARCHED_TERM}" onclick="if(this.value == '{L_SEARCHED_TERM}') this.value = '';" onkeyup="ajax_blog_tags_find(this.value,'tags','tags_found');" />
		
		<div id="tags_found"></div>
		
		<div id="tags">
			<h3>{L_BLOG_TAGS}</h3>
			<table>
				<thead>
					<tr>
						<th class="action">{L_USED_COUNT}</td>
						<th>{L_HEADER}</th>
						<th class="action">{L_EDIT}</th>
						<th class="action">{L_DELETE}</th>
					</tr>
				</thead>
				<tbody>
					<foreach(BLOG_TAGS)><tr>
						<td><var(TAG_COUNT)>&times;</td>
						<td title="<var(TAG_TAG)>"><var(TAG_HEADER)></td>
						<td><a href="<var(TAG_LINK_EDIT)>">{L_EDIT}</a></td>
						<td><a href="<var(TAG_LINK_DELETE)>">{L_DELETE}</a></td>
					</tr></foreach(BLOG_TAGS)>
				</tbody>
			</table>
			{BLOG_TAGS_BROWSER}
		</div>
	</div>
	<script type="text/javascript"><!--
function ajax_blog_tags_find(term,id_tags,id_tags_found)
{
	var check = document.getElementById('ajax_loader');
	if(check)
		document.body.removeChild(check);
	
	var loader = document.createElement('script');
	loader.id = 'ajax_loader';
	loader.type = 'text/javascript';
	loader.src = '{SITE_ROOT_PATH}ajaxrequest.php?c=blog&function=tag_find&q='+term+'&id_tags='+id_tags+'&id_tags_found='+id_tags_found;
	document.body.appendChild(loader);//document.getElementById('column').innerHTML = loader.src;
}
	--></script>
