	<div id="content">
		<h2>{L_MODULE_BLOG} &mdash; {L_BLOG_CATEGORY_ADD}</h2>
		
		<form action="{CATEGORY_ACTION}" method="post"><div>
		
		<label for="category_header">{L_HEADER}:</label>
		<input type="text" name="category[header]" /><br />
		
		<fieldset>
			<legend>{L_SLUG}</legend>
			<input type="radio" name="category_slug_generate" value="1" id="category_slug_generate_true" checked="checked" /> <label class="none" for="category_slug_generate_true">{L_SLUG_GENERATE}</label><br />
			<input type="radio" name="category_slug_generate" value="0" id="category_slug_generate_false" /> <label class="none" for="category_slug_generate_false">{L_SLUG_INPUT}</label><br />
			<div style="display:none;" id="category_slug_input_div">
				<label for="category_slug">{L_SLUG}:</label>
				<input type="text" name="category[slug]" />
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
