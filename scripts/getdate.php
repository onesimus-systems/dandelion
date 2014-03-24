<?php
require_once 'grabber.php';
$theme = getTheme();
?>
<html>
    <head>
        <meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <title>Dandelion cxeesto</title>
		<link rel="stylesheet" type="text/css" href="../jquery/css/smoothness/jquery-ui.min.css" />
        <link rel="stylesheet" href="../styles/datetimepicker.css">
        <link rel="stylesheet" href="../styles/presencewin.css" />
		<link rel="stylesheet" type="text/css" href="../themes/<?php echo $theme;?>/presenceWin.css" />
    </head>
    
    <body>
        <form>
            <table>
                <thead>
                    <tr><td colspan="2">Quick Set:</td></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>10 Minutes<input type="radio" name="quicktime" onClick="setDateTime(10);" /></td>
                        <td>20 Minutes<input type="radio" name="quicktime" onClick="setDateTime(20);" /></td>
                    </tr>
                    <tr>
                        <td>30 Minutes<input type="radio" name="quicktime" onClick="setDateTime(30);" /></td>
                        <td>40 Minutes<input type="radio" name="quicktime" onClick="setDateTime(40);" /></td>
                    </tr>
                    <tr>
                        <td>1 Hour<input type="radio" name="quicktime" onClick="setDateTime(60);" />
                        <td>1 Hour 15 Min.<input type="radio" name="quicktime" onClick="setDateTime(75);" /></td>
                    </tr>
                    <tr>
                        <td>1 Hour 30 Min.<input type="radio" name="quicktime" onClick="setDateTime(90);" /></td>
                        <td>1 Hour 45 Min.<input type="radio" name="quicktime" onClick="setDateTime(105);" /></td>
                    </tr>
                    <tr>
                        <td>2 Hours<input type="radio" name="quicktime" onClick="setDateTime(120);" /></td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
            <br />
            
            Return Time:<br />
            <input type="text" id="datepick" value="Today" /><br /><br />
            Message:<br />
            <textarea id="messagetext" cols="25" rows="10"></textarea><br />
            <input type="button" onclick="giveTime();" value="Done" />
        </form>
        <script src="../jquery/js/jquery-2.1.0.min.js"></script>
        <script src="../jquery/js/jquery-ui-1.10.4.custom.min.js"></script>
        <script src="../js/timepicker.js"></script>
        <script src="../js/slider.js"></script>
        <script type="text/javascript">
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
        </script>
    </body>
</html>
