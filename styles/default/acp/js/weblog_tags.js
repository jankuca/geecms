function select_tag(tagHeader)
{
	var postTagsDiv = document.getElementById('post_tags_div');
	var postTagsSelect = document.getElementById('post_tags_select');
	var tag = document.createElement('span');
	
	if(postTagsSelect.value.search(', '+tagHeader+', ') == -1)
	{
		tag.className = 'tag';
		tag.id = 'tag_new';
		tag.innerHTML = tagHeader;
		tag.style.display = 'none';
		tag.onclick = 'delete_tag(this);'
		
		postTagsDiv.appendChild(tag);
		$("#tag_new").slideToggle("slow");
		document.getElementById('tag_new').id = '';
		
		postTagsSelect.value += tagHeader+', ';
	}
}
function delete_tag(tag)
{
	var postTagsSelect = document.getElementById('post_tags_select');
	postTagsSelect.value = postTagsSelect.value.replace(', '+tag.innerHTML+', ',', ');
	
	tag.parentNode.removeChild(tag);
}

function ajax_blog_tags_find(term,id_tags,id_tags_found)
{
	var check = document.getElementById('ajax_loader');
	if(check)
		document.body.removeChild(check);
	
	var loader = document.createElement('script');
	loader.id = 'ajax_loader';
	loader.type = 'text/javascript';
	loader.src = site_root_path+'ajaxrequest.php?c=blog&function=tag_find&mode=selectbox&q='+term+'&id_tags='+id_tags+'&id_tags_found='+id_tags_found;
	document.body.appendChild(loader);
}