function ajax(address, data, success, failure) {
	requestID = getRandomInt();
	console.log(requestID);
    window.XMLHttpRequest ? requestID=new XMLHttpRequest() : requestID=new ActiveXObject("Microsoft.XMLHTTP");
      
    requestID.onreadystatechange=function()
	    {
    		responseText = '';
    		responseText = requestID.responseText;
	        if (requestID.readyState===4 && requestID.status===200)
	          {
	        	if(success != null) {
	        		success();
	        	}
	          }
	        else
	          {
	        	if(failure != null) {
	        		failure();
	        	}
	          }
	    }
      
    requestID.open("POST",address,true);
    requestID.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    requestID.send(data);
}

function ajax2(address, data, success, failure) {
	requestID = getRandomInt();
	console.log(requestID);
    window.XMLHttpRequest ? requestID=new XMLHttpRequest() : requestID=new ActiveXObject("Microsoft.XMLHTTP");
      
    requestID.onreadystatechange=function()
	    {
    		responseText = '';
    		responseText = requestID.responseText;
	        if (requestID.readyState===4 && requestID.status===200)
	          {
	        	if(success != null) {
	        		success();
	        	}
	          }
	        else
	          {
	        	if(failure != null) {
	        		failure();
	        	}
	          }
	    }
      
    requestID.open("POST",address,true);
    requestID.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    requestID.send(data);
}

function getRandomInt() {
	return Math.floor(Math.random() * (100 - 0 + 1) + 0);
}