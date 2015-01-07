/* global $, alert, userManager */

"use strict"; // jshint ignore:line

userManager.uiforms = {
    add: function() { // Create user form
        var rightsList = null;

        $.ajax('api/i/rights/getlist', {
            async: false,
            dataType: 'json'
        })
            .done(function(json) {
                if (json.errorcode === 0) {
                    rightsList = json.data;
                } else {
                    alert(json.status);
                }
                return;
            });

        var table = $('<table/>');
        table.append('<tr><td>Username:</td><td><input type="text" id="add_user" autocomplete="off"></td></tr>');
        table.append('<tr><td>Password:</td><td><input type="password" id="add_pass"></td></tr>');
        table.append('<tr><td>Real Name:</td><td><input type="text" id="add_fullname" autocomplete="off"></td></tr>');

        var roleRow = $('<tr/>');
        roleRow.append('<td>Role:</td>');

        var roleCell = $('<td/>');
        var select = $('<select/>').attr('id', 'add_group');
        select.append('<option value="0">Select:</option>');
        for (var key in rightsList) {
            if (!rightsList.hasOwnProperty(key))
                continue;

            var group = rightsList[key];
            select.append('<option value="' + group.role + '">' + group.role + '</option>');
        }
        roleCell.append(select);
        roleRow.append(roleCell);
        table.append(roleRow);

        $('#dialog').append(table);
        this.showDialogBox('add', {title: 'Add new user', buttonText1: 'Add'});
        return;
    },

    edit: function() { // Edit user form
        var userinfo = null;
        var rightsList = null;
        var themeList = null;

        // Load user
        $.ajax('api/i/users/getuserinfo', {
            async: false,
            dataType: 'json',
            data: {uid: userManager.currentuid}
        })
            .done(function(json) {
                if (json.errorcode === 0) {
                    userinfo = json.data;
                } else {
                    alert(json.status);
                }
                return;
            });

        // Load rights groups
        $.ajax('api/i/rights/getlist', {
            async: false,
            dataType: 'json'
        })
            .done(function(json) {
                if (json.errorcode === 0) {
                    rightsList = json.data;
                } else {
                    alert(json.status);
                }
                return;
            });

        // Load theme list
        $.ajax('api/i/settings/getthemelist', {
            async: false,
            dataType: 'json'
        })
            .done(function(json) {
                if (json.errorcode === 0) {
                    themeList = json.data;
                } else {
                    alert(json.status);
                }
                return;
            });

        var table = $('<table/>');
        table.append('<tr><td>User ID:</td><td><input type="text" id="edit_uid" value="'+userinfo.userid+'" readonly></td></tr>');
        table.append('<tr><td>Real Name:</td><td><input type="text" id="edit_fullname" value="'+userinfo.realname+'" autocomplete="off"></td></tr>');

        // Build and append row for rights groups select box
        var roleRow = $('<tr/>');
        roleRow.append('<td>Role:</td>');
        var roleCell = $('<td/>');
        var select = $('<select/>').attr('id', 'edit_group');
        for (var key in rightsList) {
            if (!rightsList.hasOwnProperty(key))
                continue;

            var group = rightsList[key];
            var selected = '';
            if (group.role == userinfo.role) { selected = 'selected'; }
            select.append('<option value="' + group.role + '" '+selected+'>' + group.role + '</option>');
        }
        roleCell.append(select);
        roleRow.append(roleCell);
        table.append(roleRow);

        // Build and append row for theme select box
        var themeRow = $('<tr/>');
        themeRow.append('<td>Theme:</td>');
        var themeCell = $('<td/>');
        var select1 = $('<select/>').attr('id', 'edit_theme');
        if (userinfo.theme === '') { select1.append('<option value="" selected>Default</option>'); }
        else { select1.append('<option value="">Default</option>'); }

        for (var key1 in themeList) {
            if (!themeList.hasOwnProperty(key1))
                continue;

            var theme = themeList[key1];
            var selected1 = '';
            if (theme == userinfo.theme) { selected1 = 'selected'; }
            select1.append('<option value="' + theme + '" '+selected1+'>' + theme + '</option>');
        }
        themeCell.append(select1);
        themeRow.append(themeCell);
        table.append(themeRow);

        table.append('<tr><td>Date Created:</td><td><input type="text" value="'+userinfo.datecreated+'" readonly></td></tr>');
        table.append('<tr><td>Prompt:</td><td><input type="text" id="edit_prompt" value="'+userinfo.firsttime+'"></td></tr>');

        $('#dialog').append(table);
        this.showDialogBox('edit', {title: 'Edit user', buttonText1: 'Save', height: 330});
        return;
    },

    delete: function() { // Delete user form
        $('#dialog').append('Are you sure you want to delete user X?');
        this.showDialogBox('delete', {buttonText1: 'Yes', buttonText2: 'No'});
        return;
    },

    reset: function() { // Reset user password form
        // jshint multistr:true
        $('#dialog').append('<table>\
                                <tr><td>New Password:</td><td><input type="password" id="pass1"></td></tr>\
                                <tr><td>Repeat Password:</td><td><input type="password" id="pass2"></td></tr>\
                            </table>');
        this.showDialogBox('reset', {buttonText1: 'Reset'});
        return;
    },

    cxeesto: function() { // Change user status form
        var status = null;

        // Load user
        $.ajax('api/i/cheesto/read', {
            async: false,
            dataType: 'json',
            data: {uid: userManager.currentuid}
        })
            .done(function(json) {
                if (json.errorcode === 0) {
                    status = json;
                } else {
                    alert(json.status);
                }
                return;
            });

        var table = $('<table/>');
        table.append('<tr><td>User ID:</td><td><input type="text" id="status_uid" value="'+status.data.uid+'" readonly></td></tr>');
        table.append('<tr><td>Name:</td><td><input type="text" id="status_name" value="'+status.data.realname+'" readonly></td></tr>');

        var statusRow = $('<tr/>');
        statusRow.append('<td>Status:</td>');
        var statusCell = $('<td/>');
        var statusSelect = $('<select/>').attr('id', 'status_text');
        statusSelect.append('<option value="-1">Set Status:</option>');
        for (var key in status.data.statusOptions) {
            statusSelect.append('<option>'+status.data.statusOptions[key]+'</option>');
        }
        statusCell.append(statusSelect);
        statusRow.append(statusCell);
        table.append(statusRow);

        table.append('<tr><td>Message:</td><td><textarea cols="30" rows="5" id="status_message">'+status.data.message+'</textarea></td></tr>');
        table.append('<tr><td>Return:</td><td><input type="text" id="status_return" value="'+status.data.returntime+'"></td></tr>');

        $('#dialog').append(table);

        $('#status_return').datetimepicker({
                        timeFormat: "HH:mm",
                        controlType: 'select',
                        stepMinute: 10
                    });

        this.showDialogBox('cxeesto', {title: 'Set User Status', height: 380, width: 515, buttonText1: 'Save'});
        return;
    },

    revokeKey: function() { // Confirm key revoke form
        $('#dialog').append('Are you sure you want to revoke the API key for user X?');
        this.showDialogBox('revokeKey', {buttonText1: 'Yes', buttonText2: 'No'});
        return;
    },

    showDialogBox: function(func, customize) {
        // Check customization settings
        if (typeof customize == 'undefined') {
            customize = {};
        }
        if (typeof customize.height == 'undefined') {
            customize.height = 300;
        }
        if (typeof customize.width == 'undefined') {
            customize.width = 450;
        }
        if (typeof customize.title == 'undefined') {
            customize.title = '';
        }
        if (typeof customize.buttonText1 == 'undefined') {
            customize.buttonText1 = 'Okay';
        }
        if (typeof customize.buttonText2 == 'undefined') {
            customize.buttonText2 = 'Cancel';
        }

        // Build dialog buttons
        var dialog_buttons = {};

        dialog_buttons[customize.buttonText1] = function() {
            $(this).dialog("close");
            userManager[func]();
            return;
        };
        dialog_buttons[customize.buttonText2] = function() {
            $(this).dialog("close");
            return;
        };

        // Build and show dialog
        $("#dialog").attr('title', customize.title);
        $("#dialog").dialog({
            height: customize.height,
            width: customize.width,
            modal: true,
            show: {
                effect: "fade",
                duration: 500
            },
            hide: {
                effect: "fade",
                duration: 250
            },
            buttons: dialog_buttons
        });

        return;
    },
};
