var presence =
{
    startR: function() {
        wherearewe = setInterval(function() {presence.checkstat(1)}, 30000);
    },
    
    checkstat: function(isWin) {
        $.ajax({
			url: "scripts/presence.php",
			data: { windowedt: isWin }
        })
			.done(function( html ) {
				$("#pt").html( html );
			});
    },
    
    setStatus: function(isWin) {
        newStatus = $("select#cstatus").prop("selectedIndex");
        isWind = isWin;
        
        if (newStatus > 1)
        {
            rtime = "";
            window.open("getdate.php","getdate","location=no,menubar=no,scrollbars=no,status=no,height=550,width=350");
        }
		else if (newStatus === 0)
		{
            return false;
		}
        else
        {
            rtime = "00:00:00";
            presence.sendNewStatus(newStatus, rtime, isWin, "");
        }
    },
    
    popOut: function() {
        window.open("presenceWindow.phtml","presencewin","location=no,menubar=no,scrollbars=no,status=no,height=500,width=950");
    },
    
    sendNewStatus: function(stat, rt, isWin, message) {
        $.ajax({
			type: "POST",
			url: "scripts/presence.php",
			data: { setorno: stat, returntime: rt, windowedt: isWin, message: message }
        })
			.done(function( html ){
				$("#pt").html( html );
			});
        
        $("select#cstatus").prop("selectedIndex", 0);
    },
    
    showHideP: function() {
		if ($("#showHide").html() == "[ - ]") {
			$("#presence").css("minWidth", $("#mainPresence").prop("offsetWidth")+"px");
			$("#mainPresence").css("display", "none");
			$("#showHide").html( "[ + ]" );
		}
		else {
			$("#presence").css("minWidth", "0px");
			$("#mainPresence").css("display", "");
			$("#showHide").html( "[ - ]" );
		}
    }
};