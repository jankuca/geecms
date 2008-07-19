	<div id="content">
		<h2>{L_MODULE_PAGES} &mdash; {L_PAGES_PAGE_EDIT}: {PAGE.HEADER}</h2>
		<form action="{PAGE_ACTION}" method="post">
		<div>
			<label for="page_header">{L_PAGES_PAGE_HEADER}:</label>
			<input type="text" name="page[header]" value="{PAGE.HEADER}" /><br /><br />
			
			<fieldset>
				<legend>{L_PAGES_PAGE_SLUG}</legend>
				<input type="hidden" name="page_slug_original" value={PAGE.SLUG}" />
				<input type="radio" name="page_slug_generate" value="1" id="page_slug_generate_true" checked="checked" /> <label class="none" for="page_slug_generate_true">{L_PAGES_PAGE_SLUG_GENERATE}</label><br />
				<input type="radio" name="page_slug_generate" value="0" id="page_slug_generate_false" /> <label class="none" for="page_slug_generate_false">{L_PAGES_PAGE_SLUG_INPUT}</label>
				<div id="page_slug_insert_div" style="display:none;"><label for="page_slug_insert">{L_PAGES_PAGE_SLUG}:</label><input type="text" name="page[slug]" value="{PAGE.SLUG}" id="page_slug_insert" /></div>
			</fieldset>
			
			<label for="page_parent">{L_PAGES_PAGE_PARENT}:</label>
			{PAGES_TREE}
			
			{PAGE.CONTENT}
			<!--textarea name="page[content]" rows="10" cols="78">{PAGE.CONTENT}</textarea-->
			
			
			<input type="submit" name="action" value="{L_PAGES_PAGE_EDIT}" />
		</div>
		<script type="text/javascript" src="./app/lib/js/codepress/codepress.js"></script>
		<script type="text/javascript">
$(document).ready(function(){
	$("#page_slug_generate_true").change(function(){
	  $("#page_slug_insert_div").slideToggle("slow");
	});
	$("#page_slug_generate_false").change(function(){
	  $("#page_slug_insert_div").slideToggle("slow");
	});
});
		</script>
	</div>
