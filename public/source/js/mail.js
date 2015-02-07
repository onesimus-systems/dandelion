/* global $, document, setTimeout, alert, confirm */
/* jshint multistr: true */

"use strict"; // jshint ignore:line

$(document).ready(function() {
    $("#writeMail").css("display", "none");
    mail.showFolder();
});

$(document).on("focusin", function(e) {
    if ($(e.target).closest(".mce-window").length) {
        e.stopImmediatePropagation();
    }
});

var mail = {
        areUnread: function() {
            $.getJSON("api/i/mail/mailCount", function(data) {
                if (data.data.count > 0) {
                    $("#mailicon").attr("src", "static/images/mail.png");
                    $("#mailicon").attr("alt", "You have mail");
                    $("#mailicon").attr("title", data.data.count);
                }
            });

            setTimeout(function(){ mail.areUnread(); }, 30000);
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
            $.getJSON("api/i/mail/getAllMail",
                    function(data){ mail.showMailList(data.data); });
        },

        getTrashCan: function() {
            $.getJSON("api/i/mail/getAllMail", {"trash": "true"},
                    function(data){ mail.showMailList(data.data); });
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

            for(var i=0; i<mailItems; i++){
                var mailItem = parsed[i];

                var html = '<tr>\
                    <td><input type="checkbox" id="check'+i+'" value='+mailItem.id+'></td>\
                    <td>'+mailItem.realname+'</td>\
                    <td><a href="#" onClick="mail.viewMail('+mailItem.id+');">'+mailItem.subject+'</a></td>\
                    <td>'+mailItem.dateSent+'</td>\
                    </tr>';

                if (mailItem.isItRead == "0") {
                    table.append($(html).addClass('unread'));
                }
                else {
                    table.append(html);
                }
            }

            $('#mailList').append(table);
        },

        viewMail: function(id) {
            $.getJSON("api/i/mail/read", {mid: id}, function(data) {
                data = data.data;
                mail.showFolder();

                var html = '<h2>'+data[0].subject+'</h2>\
                To: You<br>\
                From: '+data[0].realname+'<br>\
                Sent: '+data[0].dateSent+' '+data[0].timeSent+'<br>\
                <br>'+data[0].body;

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
                $.getJSON("api/i/mail/read", {mid: selection[0]}, function(data) {
                    data = data.data;
                    $("#mailForm")[0].reset();
                    $("textarea#mailBody").html(data[0].body);
                    $("#mailSubject").val("RE: " + data[0].subject);
                    $("#toUsersId").val(data[0].fromUser);
                    $("#toUsers").val(data[0].realname);
                    $("#toUsers").prop("readonly", true);

                    mail.showWriteDialog();
                });
            }
        },

        writeMailDialog: function() {
            $.getJSON("api/i/mail/getUserList", function(data) {
                data = data.data;
                $("#mailForm")[0].reset();
                $("#toUsers").prop("readonly", false);
                $("textarea#mailBody").html("");
                $("#toUsersId").val("");

                mail.showWriteDialog();

                var users = [];

                $(data).each(function(key, value) {
                    var user = [];
                    user.label = value.realname;
                    user.value = value.userid;
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
                browser_spellcheck: true,
                forced_root_block: false,
                resize: false,
                menubar: "edit format view insert tools",
                toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link | forecolor",
                plugins: [
                          "autolink link lists hr anchor pagebreak",
                          "searchreplace wordcount code insertdatetime",
                          "template paste textcolor"
                          ]
            });
        },

        sendMail: function() {
            var mailPiece = {};

            mailPiece.to = $("#toUsersId").val();
            mailPiece.subject = $("#mailSubject").val();
            mailPiece.body = $("#mailBody").val();

            if (mailPiece.to !== "" && mailPiece.subject !== "" && mailPiece.body !== "") {
                mailPiece = JSON.stringify(mailPiece);

                $.post("api/i/mail/send", {mail: mailPiece},
                        function(data) {
                    data = JSON.parse(data);
                    alert(data.data);
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

            if (selected.length === 1 && confirm("Delete Selected Mail?")) {
                var permenant = false;

                if ($("#folder").val() == "trash") {
                    permenant = true;
                }

                $.post("api/i/mail/delete", {"mid": selected[0], "permenant": permenant},
                    function(data) {
                        data = JSON.parse(data);
                        mail.showFolder();
                        alert(data.data);
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
