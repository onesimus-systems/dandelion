/* global $, document, location, unescape */

"use strict"; // jshint ignore:line

$(document).ready(function() {
    var username = Login.getCookie('dan_username');

    if (username !== null) {
        $('#remember-username').prop('checked', true);
        $('#username').val(username);
        $('#password').focus();
    } else {
        $('#username').focus();
    }

    $('#login-btn').click(Login.attemptLogin);
    $('#username').on('keypress', Login.check);
    $('#password').on('keypress', Login.check);
});

var Login =
{
    attemptLogin: function() {
        var user = $('#username').val();
        var pass = $('#password').val();
        var remember = $('#remember-username').prop('checked');

        if (user === '' || pass === '') {
            return false;
        }

        $.post("login", { user: user, pass: pass, remember: remember }, null, 'json')
            .done(function( response ) {
                if (response != '1' && response != '2') {
                    $.alert(response, 'Dandelion Login');
                    return;
                }

                if (response == '2') {
                    location.assign('reset');
                } else if (response == '1') {
                    location.assign('.');
                }
            });
    },

    check: function(e) {
        if (e.keyCode == 13) {
            Login.attemptLogin();
        }
    },

    getCookie: function(name) {
        var re = new RegExp(name + "=([^;]+)");
        var value = re.exec(document.cookie);
        return (value !== null) ? unescape(value[1]) : null;
    }
};
