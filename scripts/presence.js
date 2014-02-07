var presence = {
    startR: function() {
        wherearewe = setInterval(function() {presence.checkstat(1)}, 30000);
    },
    
    checkstat: function(isWin) {
      window.XMLHttpRequest ? xmlhttp=new XMLHttpRequest() : xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          
        xmlhttp.onreadystatechange=function()
          {
              if (xmlhttp.readyState===4 && xmlhttp.status===200)
                {
                    document.getElementById("pt").innerHTML=xmlhttp.responseText;
                }
          }
        
        xmlhttp.open("POST",'scripts/presence.php',true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send("windowedt=" + isWin);
    },
    
    setStatus: function(isWin) {
        newStatus = document.getElementById('cstatus').selectedIndex;
        isWind = isWin;
        
        if (newStatus != 1 && newStatus != 0) {
            rtime = "";
            window.open("scripts/getdate.html","getdate","location=no,menubar=no,scrollbars=no,status=no,height=550,width=350");
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
        window.XMLHttpRequest ? xmlhttp=new XMLHttpRequest() : xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          
        xmlhttp.onreadystatechange=function()
          {
              if (xmlhttp.readyState===4 && xmlhttp.status===200)
                {
                    document.getElementById("pt").innerHTML=xmlhttp.responseText;
                }
          }
        
        xmlhttp.open("POST",'scripts/presence.php',true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send("setorno=" + stat + "&returntime=" + rt + "&windowedt=" + isWin + "&message=" + message);
        
        document.getElementById('cstatus').selectedIndex = 0;
    },
}