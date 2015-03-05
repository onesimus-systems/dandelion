/* global $, document*/

"use strict"; // jshint ignore:line

$(document).ready(function(){
    $('#save-per-page-btn').click(Settings.savePerPage);
    $('#save-theme-btn').click(Settings.saveTheme);
    $('#reset-password-btn').click(Settings.resetPassword);
    $('#generate-apikey-btn').click(Settings.generateKey);
});

var Settings =
{
    generateKey: function() {
        $.getJSON('api/i/key/generate', function(data) {
                $('#apikey').html('<strong>Key:</strong> '+data.data.key);
        });
    },

    resetPassword: function() {
        var pw1 = $('#new-password-1').val();
        var pw2 = $('#new-password-2').val();
        $('#new-password-1').val('');
        $('#new-password-2').val('');

        if (pw1 === pw2 && pw1 !== '') {
            $.post('api/i/users/resetPassword', {'pw': pw1}, null, 'json')
                .done(function(msg) {
                    $.alert(msg.data, 'Password Reset');
                });
        } else {
            $.alert('Passwords do not match or are empty', 'Password Reset');
        }
    },

    savePerPage: function() {
        var limit = $('#page-limit').val();
        $.post('api/i/usersettings/saveLogLimit', {'limit': limit}, null, 'json')
            .done(function(msg) {
                $.alert(msg.data, 'Settings');
            });
    },

    saveTheme: function() {
        var theme = $('#theme').val();
        $.post('api/i/usersettings/saveTheme', {'theme': theme}, null, 'json')
            .done(function(msg) {
                $.alert(msg.data, 'Settings', function() { document.location.reload(true); });
            });
    }
};
