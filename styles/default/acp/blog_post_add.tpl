	<div id="content">
		<h2>{L_MODULE_BLOG} &mdash; {L_BLOG_POST_ADD}</h2>
		<form action="{POST_ACTION}" method="post" onsubmit="if(document.getElementById('add_new_tag').value != ''){select_tag(document.getElementById('add_new_tag').value); document.getElementById('add_new_tag').value = ''; return(false);}">
		<div>
			<label for="post_header">{L_HEADER}:</label>
			<input type="text" name="post[header]" id="post_header" /><br />

			<fieldset>
				<legend>{L_SLUG}</legend>
				<input type="radio" name="post_slug_generate" value="1" id="post_slug_generate_true" checked="checked" /> <label class="none" for="post_slug_generate_true">{L_SLUG_GENERATE}</label><br />
				<input type="radio" name="post_slug_generate" value="0" id="post_slug_generate_false" /> <label class="none" for="post_slug_generate_false">{L_SLUG_INPUT}</label>
				<div id="post_slug_insert_div" style="display:none;"><label for="post_slug_insert">{L_SLUG}:</label><input type="text" name="post[slug]" id="post_slug_insert" /></div>
			</fieldset>

			<label for="post_category">{L_BLOG_CATEGORY}:</label>
			<select name="post[category]" id="post_category">
<foreach(BLOG_CATEGORIES)>				<option value="<var(CATEGORY_ID)>"<var(CATEGORY_ACTIVE)> /><var(CATEGORY_HEADER)></option>
</foreach(BLOG_CATEGORIES)>			</select>

			<h3>{L_BLOG_POST_PROLOGUE}</h3>
			{POST.PROLOGUE}

			<h3>{L_BLOG_POST_CONTENT}</h3>
			{POST.CONTENT}

			<h3>{L_BLOG_TAGS}</h3>
			<div>
				<div style="float:left;">
					<input type="text" style="display:block;width: 294px;" value="{L_SEARCHED_TERM}" onclick="if(this.value == '{L_SEARCHED_TERM}') this.value = '';" onkeyup="ajax_blog_tags_find(this.value,'tags','tags_found');" />
					<select id="tags_found" multiple="multiple" style="display:none;width:300px;height:200px;"></select>
					<select id="tags" multiple="multiple" style="display:block;width:300px;height:200px;">
<foreach(BLOG_TAGS)>						<option value="<var(TAG_HEADER)>" onclick="select_tag(this.value);"><var(TAG_HEADER)></option>
</foreach(BLOG_TAGS)>					</select>
				</div>
				<div id="post_tags_div" style="float:right;width:200px;padding-right:120px">
					<h4>{L_BLOG_TAGS_SELECTED}</h4>
				</div>
				<div style="clear:right;float:right;width:200px;padding-right:120px;padding-top:20px;">
					<h4>{L_BLOG_TAG_NEW}</h4>
					<input type="text" id="add_new_tag" style="width:150px;" /><input type="button" value="OK" class="none" style="width: 30px;" onclick="select_tag(document.getElementById('add_new_tag').value); return(false);" />
				</div>
				<br class="clear" />
			</div>

			<textarea name="post[tags]" id="post_tags_select" style="display:none;">, </textarea>
			<input type="submit" name="action" value="{L_BLOG_POST_EDIT}" />
		</div>
		<script type="text/javascript" src="./app/lib/js/codepress/codepress.js"></script>
		<script type="text/javascript">
$(document).ready(function(){
	$("#post_slug_generate_false").change(function(){
	  $("#post_slug_insert_div").slideToggle("slow");
	  document.getElementById('post_slug_insert_div').id = '';
	});
});

var site_root_path = '{SITE_ROOT_PATH}';
		</script>
		<script type="text/javascript" src="./styles/default/acp/js/weblog_tags.js"></script>
	</div>
