<?php
/**
  * @brief Shows forms for user management
  *
  * @author Lee Keitel
  * @date March, 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;

class UserForms
{
    /** Confirm user delete form
     *
     * @param name (string) - User's real name
     * @param uid (int) - User's ID number
     *
     * @return nothing
     */
    public function confirmDelete($name, $uid)
    {
        if ($uid != $_SESSION['userInfo']['userid']) {
            echo <<<HTML
            <br><hr width="500">
            Are you sure you want to delete "{$name}"?<br><br>
            <form method="post">
                <input type="hidden" name="the_choosen_one" value="{$uid}">
                <input type="submit" name="sub_type" value="Yes">
                <input type="submit" value="No">
            </form>
            <hr width="500"><br>
HTML;
        } else {
            echo '<br>You can\'t delete yourself.<br><br>';
        }
    }

    /** Edit user status form
     *
     * @param row (associative array) - All user information from database for Cxeesto
     *
     * @return nothing
     */
    public function editCxeesto($row)
    {
        $scripts = loadJS("jquery","jqueryui","timepicker.js","slider.js");
        
        echo <<<HTML
        <div id="editform">
            <h2>Edit User Status:</h2>
            <form name="edit_form" method="post">
                <table>
                    <tr><td>User ID:</td><td><input type="text" name="status_id" value="{$row['uid']}" autocomplete="off" readonly></td></tr>
                    <tr><td>Name:</td><td><input type="text" name="status_name" value="{$row['realname']}" autocomplete="off" readonly></td></tr>
                    <tr><td>Status:</td><td>
                        <select name="status_s">
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
                        </select></td></tr>
                    <tr><td>Message:</td><td><textarea cols="30" rows="5" name="status_message">{$row['message']}</textarea></td></tr>
                    <tr><td>Return:</td><td><input type="text" name="status_return" id="datepick" value="{$row['return']}"></td></tr>
                </table>
                <input type="submit" name="sub_type" value="Set Status">
                <input type="submit" name="sub_type" value="Cancel">
            </form>
            {$scripts}
            <script type="text/javascript">
            $(document).ready(function () {
                $('#datepick').datetimepicker({
                        timeFormat: "HH:mm",
                        controlType: 'select',
                        stepMinute: 10,
                    });
                });
            </script>
        </div><br>
HTML;
    }

    /** Edit user form
     *
     * @param userInfo (keyed array) - All user information from database
     *
     * @return nothing
     */
    public function editUser($userInfo)
    {
        $permissions = new Permissions();
        $list = $permissions->getGroupList();
        $themeList = getThemeList($userInfo['theme']);

        echo <<<HTML
        <div id="editform">
            <h2>Edit User Information:</h2>
            <form name="edit_form" method="post">
                <table>
                    <tr><td>User ID:</td><td><input type="text" name="edit_uid" value="{$userInfo['userid']}" readonly></td></tr>
                    <tr><td>Real Name:</td><td><input type="text" name="edit_real" value="{$userInfo['realname']}" autocomplete="off"></td></tr>
                    <tr><td>Role:</td><td>
                    <select name="edit_role">
HTML;
                        foreach ($list as $group) {
                            if ($group['role'] == $userInfo['role'])
                                $selected = 'selected';
                            else
                                $selected = '';

                            echo '<option value="'.$group['role'].'" '.$selected.'>'.ucfirst($group['role']).'</option>';
                        }
        echo <<<HTML
                    </select>
                    </td></tr>
                    <tr><td>Theme:</td><td>
                        {$themeList}
                    </td></tr>
                    <tr><td>Date Created:</td><td><input type="text" name="edit_date" value="{$userInfo['datecreated']}" readonly></td></tr>
                    <tr><td>First Login:</td><td><input type="text" name="edit_first" value="{$userInfo['firsttime']}" autocomplete="off"></td></tr>
                </table>
                <input type="submit" name="sub_type" value="Save Edit">
                <input type="submit" name="sub_type" value="Cancel">
            </form>
        </div><br>
HTML;
    }

    /** Add new user form
     *
     * @return nothing
     */
    public function addUser()
    {
        $permissions = new Permissions();
        $list = $permissions->getGroupList();
        echo <<<HTML
        <div id="editform">
            <h2>Add a User:</h2>
                <form name="edit_form" method="post">
                    <table>
                        <tr><td>Username:</td><td><input type="text" name="add_user" autocomplete="off"></td></tr>
                        <tr><td>Password:</td><td><input type="password" name="add_pass"></td></tr>
                        <tr><td>Real Name:</td><td><input type="text" name="add_real" autocomplete="off"></td></tr>
                        <tr><td>Role:</td><td>
                        <select name="add_role">
HTML;
                        foreach ($list as $group) {
                            echo '<option value="'.$group['role'].'">'.ucfirst($group['role']).'</option>';
                        }
        echo <<<HTML
                        </select>
                        </td></tr>
                    </table>
                    <input type="submit" name="sub_type" value="Add">
                    <input type="submit" name="sub_type" value="Cancel">
                </form>
        </div><br>
HTML;
    }

    /** Confirm user delete form
     *
     * @param uid (int) - User's ID number
     * @param uname (string) - User's username
     * @param realname (string) - User's name
     *
     * @return nothing
     */
    public function resetPassword($uid, $uname, $realname)
    {
        echo <<<HTML
        <div id="editform">
            <h2>Reset Password for {$realname}:</h2>
            <form name="edit_form" method="post">
                <table>
                    <tr><td>User ID:</td><td><input type="text" name="reset_uid" value="{$uid}" readonly></td></tr>
                    <tr><td>Username:</td><td><input type="text" value="{$uname}" readonly></td></tr>
                    <tr><td>New Password:</td><td><input type="password" name="reset_1"></td></tr>
                    <tr><td>Repeat Password:</td><td><input type="password" name="reset_2"></td></tr>
                </table>
                <input type="submit" name="sub_type" value="Reset">
                <input type="submit" name="sub_type" value="Cancel">
            </form>
        </div><br>
HTML;
    }
}
