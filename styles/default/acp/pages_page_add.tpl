	<div id="content">
		<h2>{L_MODULE_PAGES} &mdash; {L_PAGES_PAGE_ADD}</h2>
		<form action="{PAGE_ACTION}" method="post">
		<div>
			<label for="page_header">{L_PAGES_PAGE_HEADER}:</label>
			<input type="text" name="page[header]" /><br /><br />
			
			<fieldset>
				<legend>{L_PAGES_PAGE_SLUG}</legend>
				<input type="radio" name="page_slug_generate" value="1" id="page_slug_generate_true" checked="checked" /> <label class="none" for="page_slug_generate_true">{L_PAGES_PAGE_SLUG_GENERATE}</label><br />
				<input type="radio" name="page_slug_generate" value="0" id="page_slug_generate_false" /> <label class="none" for="page_slug_generate_false">{L_PAGES_PAGE_SLUG_INPUT}</label>
				<div id="page_slug_insert_div" style="display:none;"><label for="page_slug_insert">{L_PAGES_PAGE_SLUG}:</label><input type="text" name="page[slug]" id="page_slug_insert" /></div>
			</fieldset>
			
			<label for="page_parent">{L_PAGES_PAGE_PARENT}:</label>
			{PAGES_TREE}<br /><br />
			
			<label for="page_options_addtomainmenu">{L_PAGES_PAGE_ADDTOMAINMENU}:</label>
			<input type="checkbox" name="options[addtomainmenu]" value="1" /> <label for="page_options_addtomainmenu" class="none">{L_YES}</label><br /><br />
			
			{PAGE.CONTENT}
			
			<input type="submit" name="action" value="{L_PAGES_PAGE_ADD}" />
		</div>
		<!--script type="text/javascript" src="./app/lib/js/codepress/codepress.js"></script-->
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
