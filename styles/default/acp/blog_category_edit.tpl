	<div id="content">
		<h2>{L_MODULE_BLOG} &mdash; {L_BLOG_CATEGORY_EDIT}: <strong>{CATEGORY.HEADER}</strong></h2>
		
		<form action="{CATEGORY_ACTION}" method="post"><div>
		
		<label for="category_header">{L_HEADER}:</label>
		<input type="text" name="category[header]" value="{CATEGORY.HEADER}" /><br />
		
		<fieldset>
			<legend>{L_SLUG}</legend>
			<input type="hidden" name="category_slug_original" value="{CATEGORY.SLUG}" />
			
			<input type="radio" name="category_slug_generate" value="2" id="category_slug_keep_original" checked="checked" /> <label class="none" for="category_slug_keep_original">{L_SLUG_KEEP_ORIGINAL}</label><br />
			<input type="radio" name="category_slug_generate" value="1" id="category_slug_generate_true" /> <label class="none" for="category_slug_generate_true">{L_SLUG_GENERATE_NOREC}</label><br />
			<input type="radio" name="category_slug_generate" value="0" id="category_slug_generate_false" /> <label class="none" for="category_slug_generate_false">{L_SLUG_INPUT}</label><br />
			<div style="display:none;" id="category_slug_input_div">
				<label for="category_slug">{L_SLUG}:</label>
				<input type="text" name="category[slug]" value="{CATEGORY.SLUG}" />
			</div>
		</fieldset><br />
		
		<input type="submit" name="action" value="{L_BLOG_CATEGORY_ADD}" />
		
		</div></form>
	</div>
	
	<script type="text/javascript">
$(document).ready(function(){
	$("#category_slug_generate_false").change(function(){
	  $("#category_slug_input_div").slideToggle("slow");
	  document.getElementById('category_slug_input_div').id = '';
	});
});
	</script>
