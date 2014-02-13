function ajax(address, data, statechange) {
    window.XMLHttpRequest ? xmlhttp=new XMLHttpRequest() : xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      
    xmlhttp.onreadystatechange=statechange;
      
    xmlhttp.open("POST",address,true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data);
}

function showCats(mD, parent) {
	address     = 'scripts/categories.php';
	data        = '';
	statechange = function()
    {
        if (xmlhttp.readyState===4 && xmlhttp.status===200)
          {                
              document.getElementById("workArea").innerHTML=xmlhttp.responseText;
          }
    }
	
	ajax(address, data, statechange);
}

function deleteMe() {
	address     = 'scripts/categories.php';
	data        = 'action=delete&item=' + currentCats;
	statechange = function()
    {
        if (xmlhttp.readyState===4 && xmlhttp.status===200)
          {                
              document.getElementById("message").innerHTML=xmlhttp.responseText;
          }
    }
	
	ajax(address, data, statechange);
}