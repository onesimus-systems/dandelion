/* global $, document, window */
/* exported giveTime, setDateTime */

"use strict"; // jshint ignore:line

$(document).ready(function() {
    $('#datepick').datetimepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            stepMinute: 10,
        });
});

function giveTime() {
    var newStatus = window.opener.newStatus,
		rtime = $("#datepick").val(),
		ver = window.opener.presence.version,
		message = $("#messagetext").val();
		
    message = encodeURIComponent(message);
    
    window.opener.presence.sendNewStatus(newStatus, rtime, ver, message);
    window.close();
}

function setDateTime(timeAdd) {
    var currentdate = new Date();
    
    var minutes = currentdate.getMinutes()+(timeAdd % 60);
    var hours = currentdate.getHours()+((timeAdd-(timeAdd % 60))/60);
    
    if (minutes > 59) {
        minutes = minutes - 60;
        hours++;
    }
    
    
    var datetime = ('0'  + (currentdate.getMonth()+1)).slice(-2) + "/" +
                   ('0'  + currentdate.getDate()).slice(-2) + "/" +
                   currentdate.getFullYear() + " " +
                   ('0'  + hours).slice(-2) + ":" +
                   ('0'  + minutes).slice(-2);
                   
    $("#datepick").val( datetime );
}