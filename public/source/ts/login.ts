/// <reference path="../dts/jquery.d.ts" />
/// <reference path="../dts/common.d.ts" />
/* global $, document, location */

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

var Login = {
    attemptLogin: function(): boolean {
        var user: string = $('#username').val();
        var pass: string = $('#password').val();
        var remember: boolean = $('#remember-username').prop('checked');

        if (!user || !pass) {
            return false;
        }

        $.post("login", { user: user, pass: pass, remember: remember }, null, 'json')
            .done(function(response: any) {
                if (response != '1' && response != '2') {
                    $.alert('Login failed, please check your username and password', 'Dandelion Login');
                    return;
                }

                if (response == '2') {
                    location.assign('reset');
                } else if (response == '1') {
                    location.assign('.');
                }
            });
    },

    check: function(e: any): void {
        if (e.keyCode === 13) {
            Login.attemptLogin();
        }
    },

    getCookie: function(name: string): string {
        var re = new RegExp(name + "=([^;]+)");
        var value = re.exec(document.cookie);
        return (value !== null) ? decodeURI(value[1]) : null;
    }
};
