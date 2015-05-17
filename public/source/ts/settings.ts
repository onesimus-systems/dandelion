/// <reference path="../dts/jquery.d.ts" />
/// <reference path="../dts/common.d.ts" />
/* global $, document*/

"use strict"; // jshint ignore:line

$(document).ready(function(){
    if (typeof page === 'undefined' || page !== 'initialReset') {
        $('#save-per-page-btn').click(Settings.savePerPage);
        $('#save-theme-btn').click(Settings.saveTheme);
        $('#generate-apikey-btn').click(Settings.generateKey);
    }
    $('#reset-password-btn').click(Settings.resetPassword);
});

var Settings = {
    generateKey: function(): void {
        $.getJSON('api/i/key/generate', function(data) {
                $('#apikey').html('<strong>Key:</strong> '+data.data.key);
        });
    },

    resetPassword: function(): void {
        var pw1: string = $('#new-password-1').val();
        var pw2: string = $('#new-password-2').val();
        $('#new-password-1').val('');
        $('#new-password-2').val('');

        if (pw1 === pw2 && pw1 !== '') {
            $.post('api/i/users/resetPassword', {'pw': pw1}, null, 'json')
                .done(function(msg) {
                    $.alert(msg.data, 'Password Reset', function() {
                        if (page === 'initialReset') {
                            location.assign('dashboard');
                        }
                    });
                });
        } else {
            $.alert('Passwords do not match or are empty', 'Password Reset');
        }
    },

    savePerPage: function(): void {
        var limit: string = $('#page-limit').val();
        $.post('api/i/usersettings/saveLogLimit', {'limit': limit}, null, 'json')
            .done(function(msg) {
                $.alert(msg.data, 'Settings');
            });
    },

    saveTheme: function(): void {
        var theme: string = $('#theme').val();
        $.post('api/i/usersettings/saveTheme', {'theme': theme}, null, 'json')
            .done(function(msg) {
                $.alert(msg.data, 'Settings', function() { document.location.reload(true); });
            });
    }
};
