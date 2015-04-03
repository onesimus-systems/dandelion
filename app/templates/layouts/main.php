<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="assets/favicon.ico">

        <?= $this->getCssSheets($requiredCssFiles) ?>
        <title><?= $this->e($appTitle) ?> - <?= $this->e($pageTitle) ?></title>
    </head>
    <body>
        <header>
            <div class="title-lockup">
                <span class="app-title"><?= $this->e($appTitle) ?></span>
                <span class="app-tagline"><?= $this->e($tagline) ?></span>
            </div>

            <nav>
                <ul>
                    <li><a href="./">Dashboard</a></li>
                    <li><a href="settings">Settings</a></li>
                    <li><a href="admin">Administration</a></li>
                    <li><a href="logout">Logout</a></li>
                </ul>
            </nav>
        </header>

        <div class="main-content">
            <?= $this->section('content') ?>
        </div>

        <footer>
            <span class="footer-info">
                <span class="dash">Copyright &copy;2015 Onesimus Systems</span>
                <span class="dash">License: <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GPLv3</a></span>
                <span class="dash">Version: <?= $this->e($appVersion) ?></span>
                <span class="dash"><a href="about">About</a></span>
                <span><a href="help">Help</a></span>
            </span>
        </footer>
    </body>
</html>
