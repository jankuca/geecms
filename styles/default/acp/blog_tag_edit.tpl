	<div id="content">
		<h2>{L_MODULE_BLOG} &mdash; {L_BLOG_TAG_EDIT}: <strong>{TAG.HEADER}</strong></h2>
		
		<form action="{TAG_ACTION}" method="post"><div>
		
		<label for="tag_header">{L_HEADER}:</label>
		<input type="text" name="tag[header]" value="{TAG.HEADER}" id="tag_header" /><br />
		
		<fieldset>
			<legend>{L_SLUG}</legend>
			<input type="hidden" name="tag_tag_original" value="{TAG.TAG}" />
			
			<input type="radio" name="tag_tag_generate" value="2" id="tag_tag_keep_original" checked="checked" /> <label class="none" for="tag_tag_keep_original">{L_SLUG_KEEP_ORIGINAL}</label><br />
			<input type="radio" name="tag_tag_generate" value="1" id="tag_tag_generate_true" /> <label class="none" for="tag_tag_generate_true">{L_SLUG_GENERATE_NOREC}</label><br />
			<input type="radio" name="tag_tag_generate" value="0" id="tag_tag_generate_false" /> <label class="none" for="tag_tag_generate_false">{L_SLUG_INPUT}</label><br />
			<div style="display:none;" id="tag_tag_input_div">
				<label for="tag_tag">{L_SLUG}:</label>
				<input type="text" name="tag[tag]" value="{TAG.TAG}" />
			</div>
		</fieldset><br />
		
		<input type="submit" name="action" value="{L_BLOG_TAG_EDIT}" />
		
		</div></form>
	</div>
	
	<script type="text/javascript">
$(document).ready(function(){
	$("#tag_tag_generate_false").change(function(){
	  $("#tag_tag_input_div").slideToggle("slow");
	  document.getElementById('tag_tag_input_div').id = '';
	});
});
	</script>
