var presence = {
    startR: function() {
        wherearewe = setInterval(function() {presence.checkstat(1)}, 30000);
    },
    
    checkstat: function(isWin) {
    	var params = new Object;
    	params.success = function()
	      	{
	    	  document.getElementById("pt").innerHTML=requestID.responseText;
	        }
        
    	params.address= 'scripts/presence.php';
    	params.data = "windowedt=" + isWin;
        ajax(params);
    },
    
    setStatus: function(isWin) {
        newStatus = document.getElementById('cstatus').selectedIndex;
        isWind = isWin;
        
        if (newStatus != 1 && newStatus != 0) {
            rtime = "";
            window.open("scripts/getdate.php","getdate","location=no,menubar=no,scrollbars=no,status=no,height=550,width=350");
            }
        else if (newStatus == 0)
            return false;
        else {
            rtime = "00:00:00";
            presence.sendNewStatus(newStatus, rtime, isWin, "");
            }
    },
    
    popOut: function() {
        window.open("presenceWindow.phtml","presencewin","location=no,menubar=no,scrollbars=no,status=no,height=500,width=950");
    },
    
    sendNewStatus: function(stat, rt, isWin, message) {
        var params = new Object();
          
        params.success = function()
	        {
	            document.getElementById("pt").innerHTML=responseText;
	        }
        
        params.address = 'scripts/presence.php';
        params.data = "setorno=" + stat + "&returntime=" + rt + "&windowedt=" + isWin + "&message=" + message;
        ajax(params);
        
        document.getElementById('cstatus').selectedIndex = 0;
    },
    
    showHideP: function() {
    	if (document.getElementById('showHide').innerHTML == "[ - ]") {
    		document.getElementById('presence').style.minWidth = document.getElementById('mainPresence').offsetWidth+"px";
    		document.getElementById('mainPresence').style.display = 'none';
    		document.getElementById('showHide').innerHTML = "[ + ]";
    	}
    	else {
    		document.getElementById('presence').style.minWidth = "0px";
    		document.getElementById('mainPresence').style.display = '';
    		document.getElementById('showHide').innerHTML = "[ - ]";
    	}
    }
}