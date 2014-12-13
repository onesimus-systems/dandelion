/* global $ */

"use strict"; // jshint ignore:line

$(function(){
    api.getApiKey(); 
});

var api = {
    getApiKey: function() {
        $.getJSON("api/i/keyManager/getKey",
            function(data) {
                $("#apiKey").html(data.data.key);
        });
    },
    
    generateKey: function() {
        $.getJSON("api/i/keyManager/newKey",
            function(data) {
                $("#apiKey").html(data.data.key);
        });
    }
};