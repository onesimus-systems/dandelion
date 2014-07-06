var presence =
{
    timeoutId: "",
    version: 0,
    
    checkstat: function(ver) {
		$.getJSON("iapi/cheesto/readall",
		        function(data) {
		            presence.generateView(ver, data);
            		clearTimeout(presence.timeoutId);
            		delete presence.timeoutId;
            		
            		presence.version = ver;
            		presence.timeoutId = setTimeout(function() { presence.checkstat(ver); }, 30000);
		 });
    },
    
    generateView: function(ver, dataObj) {
        dataObj = dataObj['data'];
        $('#pt').html('');
        var table = $('<table/>');
        
        if (ver == 0) {
            var tableHead = '<thead><tr>\
                    <td width="50%">Name</td>\
                    <td width="50%">Status</td>\
            </tr></thead>';
            
            table.append(tableHead);
    
            for(i=0; i<dataObj.length; i++) {
                var user = dataObj[i];
    
                var html = '<tr>\
                    <td><span title="'+user['message']+'">'+user['realname']+'</span></td>\
                    <td><span title="'+user['statusInfo']['status']+'" class="'+user['statusInfo']['color']+'">'+user['statusInfo']['symbol']+'</td>\
                    </tr>';
    
                table.append(html);
            }
            
            var popOutButton = '<tr><td colspan="3" width="100%" class="cen">\
                                    <form><input type="button" onClick="presence.popOut();" class="linklike" value="Popout &#264;eesto"></form>\
                                </td></tr>';
            table.append(popOutButton);
        }
        else if (ver == 1) {
            var tableHead = '<thead><tr><td>Name</td>\
                            <td>Message</td>\
                            <td colspan="2">Status</td>\
                            <td>Last Changed</td>\
                            </tr></thead><tbody>';
            table.append(tableHead);

            for (i=0; i<dataObj.length; i++) {
                var user = dataObj[i];
                console.log(user);
                var html = '<tr>\
                    <td>'+user['realname']+'</td>\
                    <td>'+user['message']+'</td>\
                    <td class="statusi"><span class="'+user['statusInfo']['color']+'">'+user['statusInfo']['symbol']+'</span></td>\
                    <td>'+user['statusInfo']['status']+'</td>\
                    <td>'+user['dmodified']+'</td>\
                    </tr>';
                
                table.append(html);
            }
        }

        $('#pt').append(table);
    },
    
    setStatus: function(ver) {
        newStatus = $("select#cstatus").prop("selectedIndex");
        
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
            presence.sendNewStatus(newStatus, rtime, ver, "");
        }
    },
    
    popOut: function() {
        window.open("presenceWindow.php","presencewin","location=no,menubar=no,scrollbars=no,status=no,height=500,width=950");
    },
    
    sendNewStatus: function(stat, rt, ver, message) {
		$.post("iapi/cheesto/update", { status: stat, returntime: rt, message: message },
                function(data) {
                    $("select#cstatus").prop("selectedIndex", 0);
                    presence.checkstat(ver);
        });
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