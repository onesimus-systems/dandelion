<?php
/**
  * Global header file
  *
  * This file is a part of Dandelion
  *
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;

?>
<h1 class="t_cen"><?php echo $_SESSION['app_settings']['app_title']; ?></h1>
<h4 class="t_cen"><?php echo $_SESSION['app_settings']['slogan']; ?></h4>
<p class="t_cen" id="nav_link">
<a href="index.php">View Log</a><a href="settings.phtml">Settings</a><a href="admin.phtml">Administration</a><a href="tutorial.phtml">Tutorial</a><a href="scripts/logout.php">Logout</a>
</p>
