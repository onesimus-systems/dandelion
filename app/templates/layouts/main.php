<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="/assets/favicon.ico">
        <?= $this->getCssSheets($requiredCssFiles) ?>
        <title><?= $this->e($appTitle) ?> - <?= $this->e($pageTitle) ?></title>
    </head>
    <body>
        <header>
            <h1 class="t_cen"><?= $this->e($appTitle) ?> </h1>
            <h4 class="t_cen"><?= $this->e($tagline) ?> </h4>

            <nav class="t_cen" id="nav_link">
                <a href=".">Dashboard</a><a href="settings">Settings</a><a href="admin">Administration</a><a href="tutorial">Tutorial</a><a href="logout">Logout</a>
            </nav>
        </header>

        <?= $this->section('content') ?>

        <footer id="credits" class="t_cen">
            &copy; 2014 Onesimus Computer Systems | <a href="about" class="aboutlink">Dandelion v<?= $this->e($appVersion) ?></a>
        </footer>
    </body>
</html>
