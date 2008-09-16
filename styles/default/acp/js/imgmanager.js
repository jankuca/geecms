document.getElementsByClassName = function(cl)
{
	var retnode = [];
	var myclass = new RegExp('\\b'+cl+'\\b');
	var elem = this.getElementsByTagName('*');
	for(var i = 0; i < elem.length; i++)
	{
		var classes = elem[i].className;
		if(myclass.test(classes)) retnode.push(elem[i]);
	}
	return(retnode);
};

// -- tree --
function expandDir(targetId,actualId)
{
	var actual = document.getElementById(actualId);
	actual.className = 'pack';
	actual.style.backgroundPosition = '-20px 2px';
	actual.onclick = function()
	{
		packDir(this.title,actualId);
	}
	
	var targetDir = document.getElementById(targetId);
	targetDir.style.display = 'block';
}
function packDir(targetId,actualId)
{
	var actual = document.getElementById(actualId);
	actual.className = 'expand';
	actual.style.backgroundPosition = '0 2px';
	actual.onclick = function()
	{
		expandDir(this.title,actualId);
	}
	
	var targetDir = document.getElementById(targetId);
	targetDir.style.display = 'none';
}
var iDoc;
// -- upload --
function showUploadDialog()
{
	// -- hider --
	showHider();
	
	// -- dialog --
	upload_dir = selectedDir.replace(/--/g,'/');
	upload_dir = upload_dir.replace(/^dir/,'');
	
	var dialog = document.getElementById('dialog');
	dialog.style.display = 'block';
	dialog.style.top = (document.body.clientHeight - dialog.clientHeight) / 2 +'px';
	dialog.style.left = (document.body.clientWidth - dialog.clientWidth) / 2 +'px';
	dialog.style.display = 'none';
	$(dialog).slideToggle('slow');
	
	var inputDir = document.getElementById('upload_dir');
	inputDir.value = upload_dir;
}
function uploadStart()
{
	if(document.getElementById('upload_file').value != '')
	{
		document.getElementById('dialogForm').style.display = 'none';
		document.getElementById('dialogStatus').style.display = 'block';
		document.getElementById('dialogStatus').innerHTML = '<img src="./styles/default/acp/images/throbber.gif" style="margin: 16px 0;" alt="" />';
		return(true);
	}
	else return(false);
}
function uploadEnd()
{
	document.getElementById('dialogStatus').innerHTML = '<img src="./styles/default/acp/images/success.png" alt="" />';
	setTimeout('document.getElementById(\'dialogForm\').style.display = \'block\';' +
	'document.getElementById(\'dialogStatus\').style.display = \'none\';' +
	'hideDialog();' +
	'hideHider();',
	1000);
}


var request;
function createXmlHttpRequestObject()
{
	try { request = new XMLHttpRequest(); }
	catch(e)
	{
		var MSXmlVerze = new Array('MSXML2.XMLHttp.6.0','MSXML2.XMLHttp.5.0','MSXML2.XMLHttp.4.0','MSXML2.XMLHttp.3.0','MSXML2.XMLHttp.2.0','Microsoft.XMLHttp');
		for(var i = 0; i <= MSXmlVerze.length; i ++)
		{
			try { request = new ActiveXObject(MSXmlVerze[i]); break; }
			catch(e){}
		}
	}
	if(!request)
		alert("Došlo k chybě při vytváření objektu XMLHttpRequest!");
}

function hideDialog()
{
	document.getElementById('dialogStatus').style.display = 'none';
	document.getElementById('dialogForm').style.display = 'block';
	document.getElementById('dialog').style.display = 'none';
}

function showHider()
{
	var hider = document.createElement('div');
	hider.id = 'hider';
	hider.style.position = 'absolute';
	hider.style.left = '0';
	hider.style.top = '0';
	hider.style.width = '100%';
	hider.style.height = '100%';
	hider.style.background = '#000';
	hider.style.opacity = '0.8';
	hider.style.filter = 'alpha(opacity=80)';
	hider.style.MozOpacity = '0.8';
	hider.style.KhtmlOpacity = '0.8';
	hider.style.zIndex = '2';
	hider.onclick = function(){ hideDialog(); hideHider(); }
   document.body.appendChild(hider);
}
function hideHider()
{
	document.body.removeChild(document.getElementById('hider'));
}

function selectDir(id)
{
	var oldNode = document.getElementById('selectedDir');
	if(oldNode)
		oldNode.id = '';
	else
	{
		oldNode = document.getElementsByClassName('selectedDir');
		oldNode = oldNode[0];
		oldNode.className = '';
	}
	
	var theNode = document.getElementById(id).parentNode;
	theNode.id = 'selectedDir';
	selectedDir = document.getElementById(id).title;
}


function addImageItem(targetId,filename,filesize,ext)
{
	var li = document.createElement('li');
	li.className = 'image-' + ext;
	li.innerHTML = '<strong class="new">' + filename + '</strong> (' + filesize + ' kB)';
	var dir = document.getElementById(targetId);
	dir.appendChild(li);
}



// -- init --
selectedDir = 'dir--images';
$(document).ready(function()
{
	var expands = document.getElementsByClassName('expand');
	for(var i in expands)
	{
		var actualDir = document.getElementById(expands[i].title);
		actualDir.style.display = 'none';
		actualDir.style.backgroundPosition = 'left';
		
		expands[i].id = 'expand-pack--' + expands[i].title;
		expands[i].onclick = function()
		{
			expandDir(this.title,this.id);
		}
	}
	
	var folders = document.getElementsByClassName('item');
	for(var i in folders)
	{
		folders[i].onclick = function()
		{
			selectDir(this.firstChild.id);
		}
	}
	
	var imagesOther = document.getElementsByClassName('image-other');
	for(var i in imagesOther)
	{
		if(imagesOther[i].title.search('.ico') != -1)
		{
			imagesOther[i].style.backgroundImage = "url('"+imagesOther[i].title+"')";
		}
	}
});