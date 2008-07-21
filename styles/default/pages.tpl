	<div id="content">
<if(ERROR)>		<div class="error">
			<h2>{L_ERROR}</h2>
			<p>{L_PAGE_NOT_EXIST}.</p>
		</div></if(ERROR)>
<if(PAGE)>		<h2>{PAGE.HEADER}</h2>
{PAGE.CONTENT}</if(PAGE)>
	</div>

<if(PAGE_CHILDS)>	<div class="page-childs">
		<h2>{L_PAGES_CHILDS}</h2>
		<ul>
<foreach(PAGE_CHILDS)>			<li><a href="<var(CHILD_LINK)>"><var(CHILD_HEADER)></a></li>
</foreach(PAGE_CHILDS)>		</ul>
	</div>
</if(PAGE_CHILDS)>