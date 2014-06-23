var mail = {
        areUnread: function() {
            $.getJSON("scripts/mail/getMailCount.php", function(data) {
                if (data['count'] > 0) {
                    $("#mailicon").attr("src", "images/mail.png");
                    $("#mailicon").attr("alt", "You have mail");
                    $("#mailicon").attr("title", data['count']);
                }
            });

            setTimeout(function(){ mail.areUnread(); }, 10000);
        },

        getAllMail: function() {
            $.getJSON("scripts/mail/getAllMail.php", function(data) {
                console.log(data);
                var table = $('<table/>').addClass("mailboxCSS");

                var header = table.append('<tr>\
                        <th width="2%">&nbsp;</th>\
                        <th width="15%">From</th>\
                        <th width="63%">Subject</th>\
                        <th width="20%">Date</th>\
                </tr>');

                for(i=0; i<data.length; i++){
                    html = '<tr>\
                            <td><input type="checkbox" id="check'+i+'" value='+data[i]['id']+'></td>\
                            <td>'+data[i]['realname']+'</td>\
                            <td><a href="#" onClick="mail.viewMail('+data[i]['id']+');">'+data[i]['subject']+'</a></td>\
                            <td>'+data[i]['dateSent']+'</td>\
                            </tr>';
                    
                    if (data[i]['isRead'] = "0") {
                        table.append(html).addClass('unread');
                    }
                    else {
                        table.append(html);
                    }
                }

                $('#mailList').append(table);
            });
        },
        
        viewMail: function(id) {
            $.getJSON("scripts/mail/viewMail.php", {mid: id}, function(data) {
                console.log(data);
                
                var html = '<h2>'+data[0]['subject']+'</h2>\
                    To: You<br>\
                    From: '+data[0]['realname']+'<br>\
                    <br>'+data[0]['body'];
                    
                $( "#mailDialog" ).html( html );
                $( "#mailDialog" ).dialog({
                    modal: true,
                    width: 400,
                    show: {
                        effect: "fade",
                        duration: 500
                    },
                    hide: {
                        effect: "fade",
                        duration: 500
                    },
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
            });
        }
};