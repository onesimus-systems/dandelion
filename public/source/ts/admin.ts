/// <reference path="../dts/jquery.d.ts" />
/// <reference path="categories.ts" />
/* global document, $, location, Categories */

var Admin = {
    init: function() {
        "use strict";

        $("#add-user-btn").click(Admin.showAddUserDialog);
        $("#add-role-button").click(Admin.showAddGroupDialog);

        Categories.grabFirstLevel("#categories");
        $("#add-category-button").click(Categories.createNew);
        $("#edit-category-button").click(Categories.editCat);
        $("#delete-category-button").click(Categories.deleteCat);
    },

    editUser: function(userid) {
        location.assign(`admin/edituser/${userid}`);
    },

    editGroup: function(group) {
        location.assign(`admin/editgroup/${group}`);
    },

    showAddUserDialog: function() {
        "use strict";

        $.get("api/i/groups/getlist", {}, null, "json")
            .done(function(json) {
                if ($.apiSuccess(json)) {
                    var rightsList = json.data;

                    var table = $("<table/>").attr("id", "add-user-table");
                    table.append(`<tr><td>Username:</td><td><input type="text" id="add_user" autocomplete="off"></td></tr>`);
                    table.append(`<tr><td>Password:</td><td><input type="password" id="add_pass"></td></tr>`);
                    table.append(`<tr><td>Real Name:</td><td><input type="text" id="add_fullname" autocomplete="off"></td></tr>`);

                    var roleRow = $("<tr/>");
                    roleRow.append("<td>Role:</td>");

                    var roleCell = $("<td/>");
                    var select = $("<select/>").attr("id", "add_group");
                    select.append(`<option value="0">Select:</option>`);
                    for (var key in rightsList) {
                        if (!rightsList.hasOwnProperty(key)) {
                            continue;
                        }

                        var group = rightsList[key];
                        select.append(`<option value="${group.id}">${group.name}</option>`);
                    }
                    roleCell.append(select);
                    roleRow.append(roleCell);
                    table.append(roleRow);
                    table = $("<form/>").append(table);

                    $.dialogBox(table.html(), Admin.addUser, null, {title: "Add new user", buttonText1: "Add"});
                } else {
                    $.alert(json.status, "User Management");
                }
                return;
            });
        return;
    },

    addUser: function() {
        "use strict";

        var username = $("#add_user").val();
        var password = $("#add_pass").val();
        var fullname = $("#add_fullname").val();
        var group = $("#add_group").val();

        $.post("api/i/users/create", {username: username, password: password, fullname: fullname, role: group}, null, "json")
            .done(function(data) {
                location.reload();
            });
        return;
    },

    showAddGroupDialog: function() {
        "use strict";

        var dialog = `Group Name:<br><br><input type="text" id="new_group_name">`;
        $.dialogBox(
            dialog,
            Admin.addGroup,
            null,
            {title: "Create new group", buttonText1: "Create", height: 200, width: 300}
        );
    },

    addGroup: function() {
        "use strict";

        var groupName = $("#new_group_name").val();

        $.post("api/i/groups/create", {name: groupName}, null, "json")
            .done(function(data) {
                location.reload();
            });
    }
};

(function() {
    Admin.init();
})();
