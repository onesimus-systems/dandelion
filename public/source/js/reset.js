/* global $, location, alert*/

"use strict"; // jshint ignore:line

var api = {
    resetPassword: function() {
        var pw1 = $("#pass1").val();
        var pw2 = $("#pass2").val();
        $("#pass1").val('');
        $("#pass2").val('');

        if (pw1 === pw2 && pw1 !== "") {
            $.post('api/i/users/resetPassword', {'pw': pw1}, null, "json")
                .done(function(msg) {
                    alert('Password changed');
                    location.assign('/logout');
                });
        } else {
            alert('Passwords do not match or are empty');
        }
    }
};
