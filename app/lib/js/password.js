function getPasswordStrength()
{
	var ret = 0;
	var pass  = document.getElementById('user_password').value;
	//first check the length of the password
	//and check that there is no invalid characters like spaces
	var tmp = pass.search(/[^\d|a-z|A-Z|!@\$#%\^\*&()]/g);
	if(tmp == -1 && pass.length >= 8)
	{
		//check if the user not typed only one kind of characters
		reg = /^(\d+)$|^([a-z]+)$|^([A-Z]+)$|^([\W^\s]+)$/g;
		var srch = pass.search(reg);
		if(srch == -1)
		{
			//check if the user types 2 kinds of characters not in the beginning or the end
			reg = /^([\d]{1}[a-z]+)$|^([a-z]{1}[\d]+)$|^([A-Z]{1}[\W^\s]+)$|^([\W^\s]{1}[A-Z]+)$|^([\d]{1}[A-Z]+)$|^([A-Z]{1}[\d]+)$|^([a-z]{1}[\W^\s]+)$|^([\W^\s]{1}[a-z]+)$|^([\d]{1}[\W^\s]+)$|^([\W^\s]{1}[\d]+)$|^([a-z]{1}[A-Z]+)$|^([A-Z]{1}[a-z]+)$/;
			var srch = pass.search(reg);
			
			if(srch == -1)
			{
				ret = 1;
				//if more than 6 characters and the that 2 characters without the restriction above
				if(pass.length == 8)
				{
					//count the characters //it is the easy way for now
					var count_tmp = 0;
					reg =/[\d]+/g;
					var srch = pass.search(reg);
					if(srch != -1)count_tmp++;
					reg =/[a-z]+/g;
					var srch = pass.search(reg);
					if(srch != -1)count_tmp++;
					reg =/[A-Z]+/g;
					var srch = pass.search(reg);
					if(srch != -1)count_tmp++;
					reg =/[\W^\s]+/g;
					var srch = pass.search(reg);
					if(srch != -1)count_tmp++;
					if(count_tmp > 2)
					{
						ret = 2;
					}
				}
				else
				{
					ret = 2;
				}
			}	
		}
	}
	
	setPasswordStrength(ret);
	return ret;
}

function setPasswordStrength(strength)
{
		var txt = '';
		var color = '';
		var image= '';
		switch(strength)
		{
				case 0:
					color = "#FF0000";
					txt  = "Weak password";
					image = "password_strength_weak";
					document.getElementById('user_submit').disabled = true;
					break;
				case 1:
					color = "#EFA800";
					txt  = "Normal password";
					image = "password_strength_normal";
					document.getElementById('user_submit').disabled = false;
					break;
				case 2:
					color = "#41AF50";
					txt  = "Strong password";
					image = "password_strength_strong";
					document.getElementById('user_submit').disabled = false;
					break;
		}
		
		var elm = document.getElementById('password_strength');
		elm.innerHTML = txt;
		elm.style.color = color;
		elm.style.backgroundImage = "url('./images.php?image="+image+"')";
		
}