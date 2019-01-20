<!--
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
-->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="<?= $this->e($hostname) ?>/assets/favicon.ico">

        <?= $this->getCssSheets($requiredCssFiles) ?>
        <?= $this->getCssSheetsSimple($simpleCss) ?>
        <title><?= $this->e($appTitle) ?> - <?= $this->e($pageTitle) ?></title>
    </head>
    <body>
        <header>
            <div class="title-bar">
                <span class="app-title"><?= $this->e($appTitle) ?></span>
                <span class="app-tagline"><?= $this->e($tagline) ?></span>
            </div>

            <nav>
                <ul>
                    <?php if (!isset($logoutOnly)): ?>
                    <li><a href="<?= $this->e($hostname) ?>/">Dashboard</a></li>
                    <li><a href="<?= $this->e($hostname) ?>/settings">Settings</a></li>
                    <li><a href="<?= $this->e($hostname) ?>/admin">Administration</a></li>
                    <?php endif; ?>
                    <li><a href="<?= $this->e($hostname) ?>/logout">Logout</a></li>
                </ul>
            </nav>
        </header>

        <div class="main-content">
            <?= $this->section('content') ?>
        </div>

        <footer>
            <span class="footer-info">
                <span class="dash">Copyright &copy;2019 Onesimus Systems</span>
                <span class="dash">License: <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GPLv3</a></span>
                <span class="dash">Version: <?= $this->e($appVersion) ?></span>
                <span class="dash"><a href="<?= $this->e($hostname) ?>/about">About</a></span>
                <span><a href="http://blog.onesimussystems.com/dandelion" target="_blank">Help</a></span>
            </span>
        </footer>
    </body>
</html>
