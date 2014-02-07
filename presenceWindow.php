<html>
    <head>
        <meta charset="utf-8" />
        <title>Dandelion Presence</title>
        <link rel="stylesheet" href="styles/presencewin.css" />
        <script src="scripts/presence.js"></script>
    </head>
    
    <body onLoad="presence.startR();">
        <?php $windowed = 1; ?>
        
        <h3>&#264;eesto:</h3>
        <form method="post">
            <select id="cstatus">
                <option>Set Status:</option>
                <option>Available</option>
                <option>Away From Desk</option>
                <option>At Lunch</option>
                <option>Out for Day</option>
                <option>Out</option>
                <option>Appointment</option>
                <option>Do Not Disturb</option>
                <option>Meeting</option>
                <option>Out Sick</option>
                <option>Vacation</option>
            </select>
            <input type="button" value="Set" onClick="presence.setStatus(1);" />
        </form>
        <div id="pt">
            <?php include 'scripts/presence.php'; ?>
        </div>
    </body>
</html>