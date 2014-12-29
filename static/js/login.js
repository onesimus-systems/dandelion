/* global $, document, alert, location, unescape */

"use strict"; // jshint ignore:line

function attemptLogin() {
    var user = $('#username').val();
    var pass = $('#password').val();
    var remember = $('#rememberMe').prop('checked');

    if (user === '' || pass === '') {
        return false;
    }

    $.post("api/i/auth/login", { user: user, pass: pass, remember: remember }, null, 'json')
        .done(function( response ) {
            if (response.data != '0' && response.data != '1') {
                alert(response.data);
                return;
            }

            if (response.data == '1') {
                location.assign('/reset');
            } else if (response.data == '0') {
                location.assign('/');
            }
        });
}

function check(e) {
    if (e.keyCode == 13) {
        attemptLogin();
    }
}

function getCookie(name) {
    var re = new RegExp(name + "=([^;]+)");
    var value = re.exec(document.cookie);
    return (value !== null) ? unescape(value[1]) : null;
}

(function() {
    var username = getCookie('dan_username');

    if (username !== null) {
        $('#remember').css('display', 'none');
        $('#username').val(username);
        $('#password').focus();
    } else {
        $('#username').focus();
    }
})();
