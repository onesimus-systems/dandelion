/* global $, document, alert*/

"use strict"; // jshint ignore:line

$(function(){
    api.getApiKey();
});

var api = {
    getApiKey: function() {
        $.getJSON("api/i/keyManager/getKey",
            function(data) {
                $("#apiKey").html('API Key: '+data.data.key);
        });
    },

    generateKey: function() {
        $.getJSON("api/i/keyManager/newKey",
            function(data) {
                $("#apiKey").html('API Key: '+data.data.key);
        });
    },

    showResetPasswordForm: function() {
        $("#passwordResetDialog").dialog({
            height: 300,
            width: 450,
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
                "Reset": function() {
                    api.resetPassword();
                },
                Cancel: function() {
                    $( this ).dialog("close");
                }
            }
        });
    },

    resetPassword: function() {
        var pw1 = $("#pass1").val();
        var pw2 = $("#pass2").val();
        $("#pass1").val('');
        $("#pass2").val('');

        if (pw1 === pw2 && pw1 !== "") {
            $.post('api/i/users/resetPassword', {'pw': pw1}, null, "json")
                .done(function(msg) {
                    $("#passwordResetDialog").dialog("close");
                    alert(msg.data);
                });
        } else {
            alert('Passwords do not match or are empty');
        }
    },

    saveLogLimit: function() {
        var limit = $('#show_limit').val();
        $.post('api/i/settings/saveLogLimit', {'limit': limit}, null, "json")
            .done(function(msg) {
                alert(msg.data);
            });
    },

    saveTheme: function() {
        var theme = $('#userTheme').val();
        $.post('api/i/settings/saveTheme', {'theme': theme}, null, "json")
            .done(function(msg) {
                alert(msg.data);
                document.location.reload(true);
            });
    }
};
