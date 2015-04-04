/* global document, $, location, console */

"use strict"; // jshint ignore:line

var Admin =
{
    init: function() {
        $('#add-user-btn').click(Admin.showAddUserDialog);
        //Admin.loadGroupList();
        //Admin.loadCategoryList()
    },

    editUser: function(userid) {
        location.assign('edituser/'+userid);
    },

    showAddUserDialog: function() {
        $.get('api/i/rights/getlist', {}, null, 'json')
            .done(function(json) {
                if (json.errorcode === 0) {
                    var rightsList = json.data;

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

                    $.dialogBox(table, Admin.addUser, null, {title: 'Add new user', buttonText1: 'Add'});
                } else {
                    $.alert(json.status, 'User Management');
                }
                return;
            });
        return;
    },

    addUser: function() {
        var username = $('#add_user').val();
        var password = $('#add_pass').val();
        var fullname = $('#add_fullname').val();
        var group = $('#add_group').val();

        $.post('api/i/users/create', {username: username, password: password, fullname: fullname, role: group}, null, 'json')
            .done(function(data) {
                location.reload();
            });
        return;
    }
};
