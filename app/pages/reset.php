<?php
/**
 * Reset user password on first login page
 */
namespace Dandelion;

use \Dandelion\Utils\View;

$requiredCssFiles = array();
include $paths['app'].'/pages/includes/head.php';
?>
<!-- Begin Page Body -->
<p>This is your first time logging into Dandelion. Please reset your password:</p>

<br>
<?= (isset($_SESSION['errors']) ? $_SESSION['errors'] : ''); $_SESSION['errors']=''; ?>
<br>

<div id="passwordResetDialog">
    <h2>Reset Password for <?= $_SESSION['userInfo']['realname']; ?>:</h2>
    <form>
        <table>
            <tr><td>New Password:</td><td><input type="password" id="pass1"></td></tr>
            <tr><td>Repeat Password:</td><td><input type="password" id="pass2"></td></tr>
            <tr><td></td><td><button type="button" onClick="api.resetPassword();">Reset Password</button></tr>
        </table>
    </form>
</div>

<?= View::loadJS('jquery','reset'); ?>
<!-- End Page Body -->
<?php include $paths['app'].'/pages/includes/footer.php'; ?>
