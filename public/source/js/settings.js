/* global $, document*/

"use strict"; // jshint ignore:line

$(function(){
    api.getApiKey();
});

var api = {
    getApiKey: function() {
        $.getJSON('api/i/key/get',
            function(data) {
                $('#apiKey').html('API Key: '+data.data.key);
        });
    },

    generateKey: function() {
        $.getJSON('api/i/key/generate',
            function(data) {
                $('#apiKey').html('API Key: '+data.data.key);
        });
    },

    showResetPasswordForm: function() {
        $('#dialogBox').dialog({
            height: 300,
            width: 450,
            modal: true,
            show: {
                effect: 'fade',
                duration: 500
            },
            hide: {
                effect: 'fade',
                duration: 500
            },
            buttons: {
                'Reset': function() {
                    api.resetPassword();
                },
                Cancel: function() {
                    $(this).dialog('close');
                }
            }
        });
    },

    resetPassword: function() {
        var pw1 = $('#pass1').val();
        var pw2 = $('#pass2').val();
        $('#pass1').val('');
        $('#pass2').val('');

        if (pw1 === pw2 && pw1 !== '') {
            $.post('api/i/users/resetPassword', {'pw': pw1}, null, 'json')
                .done(function(msg) {
                    $('#dialogBox').dialog('close');
                    $.alert(msg.data, 'Password Reset');
                });
        } else {
            $.alert('Passwords do not match or are empty', 'Password Reset');
        }
    },

    saveLogLimit: function() {
        var limit = $('#show_limit').val();
        $.post('api/i/usersettings/saveLogLimit', {'limit': limit}, null, 'json')
            .done(function(msg) {
                $.alert(msg.data, 'Settings');
            });
    },

    saveTheme: function() {
        var theme = $('#userTheme').val();
        $.post('api/i/usersettings/saveTheme', {'theme': theme}, null, 'json')
            .done(function(msg) {
                $.alert(msg.data, 'Settings', function() {document.location.reload(true);});
            });
    }
};
