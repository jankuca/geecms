	<div id="content">
		<h2>{L_MODULE_BLOG} &mdash; {L_BLOG_POSTS}</h2>
		
		<h3>{L_BLOG_POST_FIND}</h3>
		<input type="text" value="{L_SEARCHED_TERM}" onclick="if(this.value == '{L_SEARCHED_TERM}') this.value = '';" onkeyup="ajax_blog_posts_find(this.value,'posts','posts_found');" />
		
		<div id="posts_found"></div>
		
		<div id="posts">
			<h3>{L_BLOG_POSTS}</h3>
			<table>
				<thead>
					<tr>
						<th class="col3">{L_HEADER}</th>
						<th>{L_BLOG_CATEGORY}</th>
						<th class="action">{L_EDIT}</th>
						<th class="action">{L_DELETE}</th>
					</tr>
				</thead>
				<tbody>
					<foreach(BLOG_POSTS)><tr>
						<td title="<var(POST_SLUG)>"><var(POST_HEADER)></td>
						<td><var(POST_CATEGORY)></td>
						<td><a href="<var(POST_LINK_EDIT)>">{L_EDIT}</a></td>
						<td><a href="<var(POST_LINK_DELETE)>">{L_DELETE}</a></td>
					</tr></foreach(BLOG_POSTS)>
				</tbody>
			</table>
			{BLOG_POSTS_BROWSER}
		</div>
	</div>
	<script type="text/javascript"><!--
function ajax_blog_posts_find(term,id_posts,id_posts_found)
{
	var check = document.getElementById('ajax_loader');
	if(check)
		document.body.removeChild(check);
	
	var loader = document.createElement('script');
	loader.id = 'ajax_loader';
	loader.type = 'text/javascript';
	loader.src = '{SITE_ROOT_PATH}ajaxrequest.php?c=blog&function=post_find&q='+term+'&id_posts='+id_posts+'&id_posts_found='+id_posts_found;
	document.body.appendChild(loader);
}
	--></script>
