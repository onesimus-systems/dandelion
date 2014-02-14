function ajax(address, data, success, failure, async) {
	async = typeof async !== 'undefinded' ? async : true;
	requestID = "1";

    window.XMLHttpRequest ? requestID=new XMLHttpRequest() : requestID=new ActiveXObject("Microsoft.XMLHTTP");
      
    requestID.onreadystatechange=function()
	    {
    		responseText = '';
    		responseText = requestID.responseText;
    		ready 		 = requestID.readyState;
    		status 		 = requestID.status;
	        if (ready===4 && status===200)
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
      
    requestID.open("POST",address,async);
    requestID.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    requestID.send(data);
}

function getRandomInt(min, max) {
	return Math.floor(Math.random() * (max - min + 1) + min);
}