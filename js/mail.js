$(document).ready(function() {
    $("#writeMail").css("display", "none");
    mail.showFolder();
});

$(document).on("focusin", function(e) {
    if ($(event.target).closest(".mce-window").length) {
        e.stopImmediatePropagation();
    }
});

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
        
        showFolder: function() {
            var folderName = $("#folder").val();
            switch (folderName) {
                case "inbox":
                    this.getAllMail();
                    break;
                    
                case "trash":
                    this.getTrashCan();
                    break;
            }
        },

        getAllMail: function() {
            $.getJSON("scripts/mail/getAllMail.php",
                    function(data){ mail.showMailList(data); });
        },
        
        getTrashCan: function() {
            $.getJSON("scripts/mail/getAllMail.php", {"trash": "true"},
                    function(data){ mail.showMailList(data); });
        },

        showMailList: function(parsed) {
            var mailItems = parsed.length;

            $('#mailList').html('');
            var table = $('<table/>').addClass("mailboxCSS");

            table.append('<tr>\
                    <th width="2%">&nbsp;</th>\
                    <th width="15%">From</th>\
                    <th width="63%">Subject</th>\
                    <th width="20%">Date</th>\
            </tr>');

            for(i=0; i<mailItems; i++){
                mailItem = parsed[i];

                html = '<tr>\
                    <td><input type="checkbox" id="check'+i+'" value='+mailItem['id']+'></td>\
                    <td>'+mailItem['realname']+'</td>\
                    <td><a href="#" onClick="mail.viewMail('+mailItem['id']+');">'+mailItem['subject']+'</a></td>\
                    <td>'+mailItem['dateSent']+'</td>\
                    </tr>';

                if (mailItem['isItRead'] == "0") {
                    table.append($(html).addClass('unread'));
                }
                else {
                    table.append(html);
                }
            }

            $('#mailList').append(table);
        },

        viewMail: function(id) {
            $.getJSON("scripts/mail/viewMail.php", {mid: id}, function(data) {
                mail.showFolder();

                var html = '<h2>'+data[0]['subject']+'</h2>\
                To: You<br>\
                From: '+data[0]['realname']+'<br>\
                Sent: '+data[0]['dateSent']+' '+data[0]['timeSent']+'<br>\
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
                            $( "#mailDialog" ).html( '' );
                        }
                    }
                });
            });
        },

        replyToMail: function() {
            var selection = this.getSelectedMailIds();
            if (selection.length == 1) {
                $.getJSON("scripts/mail/viewMail.php", {mid: selection[0]}, function(data) {
                    console.log(data);
                    $("#mailForm")[0].reset();
                    $("textarea#mailBody").html(data[0]['body']);
                    $("#mailSubject").val("RE: " + data[0]['subject']);
                    $("#toUsersId").val(data[0]['fromUser']);
                    $("#toUsers").val(data[0]['realname']);
                    $("#toUsers").prop("readonly", true);
                    
                    mail.showWriteDialog();
                });
            }
        },

        writeMailDialog: function() {
            $.getJSON("scripts/mail/getUserList.php", function(data) {
                $("#mailForm")[0].reset();
                $("#toUsers").prop("readonly", false);
                $("textarea#mailBody").html("");
                $("#toUsersId").val("");

                mail.showWriteDialog();

                var users = [];

                $(data).each(function(key, value) {
                    var user = [];
                    user.label = value["realname"];
                    user.value = value["userid"];
                    users.push(user);
                });

                $("#toUsers").autocomplete({
                    minLength: 0,
                    source: users,
                    focus: function( event, ui ) {
                        $( "#toUsers" ).val( ui.item.label );
                        return false;
                    },
                    select: function( event, ui ) {
                        $( "#toUsers" ).val( ui.item.label );
                        $( "#toUsersId" ).val( ui.item.value );
                        return false;
                    }
                })
                .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                    return $( "<li>" )
                    .append( "<a>" + item.label + "</a>" )
                    .appendTo( ul );
                };
            });
        },
        
        showWriteDialog: function() {
            $( "#writeMail" ).dialog({
                height: 575,
                width: 800,
                modal: true,
                show: {
                    effect: "fade",
                    duration: 500
                },
                hide: {
                    effect: "fade",
                    duration: 500
                },
                buttons: {
                    "Send Mail": function() {
                        if (mail.sendMail()) {
                            $( this ).dialog( "close" );
                            mail.showFolder();
                        }
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });

            $("textarea#mailBody").tinymce({
                forced_root_block: false,
                resize: false,
                menubar: "edit format view insert tools",
                toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link | forecolor",
                plugins: [
                          "autolink link lists hr anchor pagebreak spellchecker",
                          "searchreplace wordcount code insertdatetime",
                          "contextmenu template paste textcolor"
                          ]
            });
        },

        sendMail: function() {
            var mailPiece = {};

            mailPiece["to"] = $("#toUsersId").val();
            mailPiece["subject"] = $("#mailSubject").val();
            mailPiece["body"] = $("#mailBody").val();

            if (mailPiece.to != "" && mailPiece.subject != "" && mailPiece.body != "") {
                mailPiece = JSON.stringify(mailPiece);

                $.post("scripts/mail/sendMail.php", {mail: mailPiece},
                        function(data) {
                    alert(data);
                });

                return true;
            }
            else {
                alert("Error: You need a subject, body, and recipient.");
                return false;
            }
        },
        
        deleteMail: function() {
            var selected = this.getSelectedMailIds();

            if (selected.length == 1 && confirm("Delete Selected Mail?")) {
                var permenant = false;
                
                if ($("#folder").val() == "trash") {
                    permenant = true;
                }
                
                $.post("scripts/mail/deleteMail.php", {"mid": selected[0], "permenant": permenant},
                    function(data) { 
                        mail.showFolder();
                        alert(data);
                });
            }
        },

        getSelectedMailIds: function() {
            var selectedMail = [];

            $("#mailList :checked").each(function() {
                selectedMail.push($(this).val());
            });

            return selectedMail;
        }
};