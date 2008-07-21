	<div id="content">
		<h2>{L_MODULE_MENU} &mdash; {L_MENU_ITEM_EDIT}: <strong>{ITEM.HEADER}</strong></h2>
		
		<form action="{ITEM_ACTION}" method="post"><div>
		<label for="item_header">{L_TEXT}</label>
		<input type="text" name="item[header]" value="{ITEM.HEADER}" id="item_header" /><br />
		
		<label for="item_link">{L_LINK}</label>
		{SITE_ROOT_PATH} <input type="text" name="item[link]" value="{ITEM.LINK}" id="item_link" class="terc" /><br />
		
		<label>{L_SHOW}</label>
		<input type="radio" name="item[show]" id="item_show_true" value="1"{ITEM.SHOW_TRUE} /> <label for="item_show_true" class="none">{L_YES}</label>
		<input type="radio" name="item[show]" id="item_show_false" value="0"{ITEM.SHOW_FALSE} /> <label for="item_show_false" class="none">{L_NO}</label><br />
		
		<input type="submit" name="action" value="{L_MENU_ITEM_EDIT}" />
		</div></form>
	</div>
