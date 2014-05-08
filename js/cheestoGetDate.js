$(document).ready(function() {
    $('#datepick').datetimepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            stepMinute: 10,
        });
});

function giveTime() {
    newStatus = window.opener.newStatus;
    rtime = document.getElementById('datepick').value;
    isWin = window.opener.isWind;
    message = encodeURIComponent(document.getElementById('messagetext').value);
    
    window.opener.presence.sendNewStatus(newStatus, rtime, isWin, message);
    window.close();
}

function setDateTime(timeAdd) {
    var currentdate = new Date();
    
    minutes = currentdate.getMinutes()+(timeAdd % 60);
    hours = currentdate.getHours()+((timeAdd-(timeAdd % 60))/60);
    
    if (minutes > 59) {
        minutes = minutes - 60;
        hours++;
    }
    
    
    var datetime = ('0'  + (currentdate.getMonth()+1)).slice(-2) + "/"
                   + ('0'  + currentdate.getDate()).slice(-2) + "/" 
                   + currentdate.getFullYear() + " "  
                   + ('0'  + hours).slice(-2) + ":"  
                   + ('0'  + minutes).slice(-2);
                   
    document.getElementById("datepick").value = datetime;
}