<?php
session_start();
$_SESSION['error'] = '';

// Check for existing configuration and redirect if exists and installed
if (file_exists(__DIR__.'/../app/config/config.php')) {
    $currConfig = include __DIR__.'/../app/config/config.php';
    if ($currConfig['installed']) {
        header("Location: {$currConfig['hostname']}");
        echo 'Redirecting to: '.$currConfig['hostname'];
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // This script will redirect if successful
    include __DIR__.'/../app/install/install.php';
}
?>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=9">
    <title>Dandelion Web Log - Install Script</title>
    <link rel="stylesheet" href="build/css/main.min.css">
    <link rel="stylesheet" type="text/css" href="assets/themes/Halloween/main.css">
    <link rel="icon" type="image/ico" href="assets/favicon.ico">
    <style>
        table td.field {
            width: 400px;
        }
        table td.labels {
            width: 200px;
        }
    </style>
</head>

<body>
<br><br>
<h1>Install Dandelion Web Log</h1>

<h2 class="le">Please fill in the information below to setup Dandelion:</h2>
    <form method="post" class="le" action="install.php">
        <?= $_SESSION['error'] ? '<h3>'.$_SESSION['error'].'</h3>' : '' ?>

        <h2>Database Connection Information:</h2>
        <table>
            <tr>
                <td class="labels">Database Type:</td>
                <td class="field"><select name="db_type" onChange="showHide(this.value);"><option value="mysql">MySQL</option><!-- <option value="sqlite">SQLite</option>--></select></td>
            </tr>
        </table>
        <table id="mysql_only" style="display:inline;">
            <tr>
                <td class="labels">Username:</td>
                <td class="field"><input type="text" name="db_user"></td>
            </tr>
            <tr>
                <td class="labels">Password:</td>
                <td class="field"><input type="password" name="db_pass"></td>
            </tr>
            <tr>
                <td class="labels">Host/IP Address:</td>
                <td class="field"><input type="text" name="db_host"></td>
            </tr>
            <tr>
                <td class="labels">Database Name:</td>
                <td class="field"><input type="text" name="db_name"></td>
            </tr>
            <tr>
                <td class="labels">Table Prefix:</td>
                <td class="field"><input type="text" name="db_prefix"> Leave blank for default</td>
            </tr>
        </table>

        <br><br><h2>Dandelion Settings:</h2>
        <table>
            <tr>
                <td class="labels">Application Hostname:</td>
                <td class="field"><input type="text" name="hostname"></td>
            </tr>
            <tr>
                <td class="labels">Application Header:</td>
                <td class="field"><input type="text" name="apptitle" value="Dandelion Web Log"></td>
            </tr>
            <tr>
                <td class="labels">Application Subheader:</td>
                <td class="field"><input type="text" name="tagline"></td>
            </tr>
        </table>
        <table>
            <tr>
                <td><input type="submit" value="Finish Install"></td>
            </tr>
        </table>
    </form>
</body>

<script type="text/javascript">
    function showHide(db_type)
    {
        if (db_type == 'sqlite') {
            document.getElementById('mysql_only').style.display = 'none';
        } else {
            document.getElementById('mysql_only').style.display = 'inline';
        }
    }
</script>
</html>
