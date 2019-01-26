import '../modules/common';
import { bindMouseMove, centerDialog } from '../modules/dialogUtils';
import { Elm } from '../elm/Administration.elm';
import '../modules/elm';

function editUser(userid: string): void {
    location.assign(`admin/edituser/${userid}`);
}

function editGroup(group: string): void {
    location.assign(`admin/editgroup/${group}`);
}

function showAddUserDialog(): void {
    $.get('api/i/groups/getlist', {}, null, 'json')
        .done(function(json) {
            if (!$.apiSuccess(json)) {
                $.alert(json.status, 'User Management');
                return;
            }

            const rightsList = json.data;

            const table = $('<table/>').attr('id', 'add-user-table');
            table.append(`<tr><td>Username:</td><td><input type="text" id="add_user" autocomplete="off"></td></tr>`);
            table.append(`<tr><td>Password:</td><td><input type="password" id="add_pass"></td></tr>`);
            table.append(`<tr><td>Real Name:</td><td><input type="text" id="add_fullname" autocomplete="off"></td></tr>`);
            table.append(`<tr><td>Force Password Reset:</td><td><input type="checkbox" id="add_force_reset" checked="true"></td></tr>`);

            const roleRow = $('<tr/>');
            roleRow.append('<td>Role:</td>');

            const roleCell = $('<td/>');
            const select = $('<select/>').attr('id', 'add_group');
            select.append(`<option value="0">Select:</option>`);
            for (const key in rightsList) {
                if (!rightsList.hasOwnProperty(key)) {
                    continue;
                }

                const group = rightsList[key];
                select.append(`<option value="${group.id}">${group.name}</option>`);
            }
            roleCell.append(select);
            roleRow.append(roleCell);
            table.append(roleRow);

            // eslint-disable-next-line
            $.dialogBox($('<form/>').append(table).html(), addUser, null, {
                title: 'Add new user',
                buttonText1: 'Add',
                height: 350,
            });
        });
}

function addUser(): void {
    const username = $('#add_user').val();
    const password = $('#add_pass').val();
    const fullname = $('#add_fullname').val();
    const group = $('#add_group').val();
    const forceReset = $('#add_force_reset').prop('checked');

    if (!username || !password || !fullname || group === 0) {
        $.alert(
            'Username, password, full name, and group are required',
            'Add User',
            showAddUserDialog
        );
        return;
    }

    $.post('api/i/users/create', { username: username, password: password, fullname: fullname, role: group, 'force_reset': forceReset }, null, 'json')
        .done(function(data) {
            if (data.errorcode === 0) {
                location.reload();
                return;
            }

            $.alert(data.data, 'Add User', showAddUserDialog);
        }).fail(function(req) {
            const json = JSON.parse(req.responseText);
            $.alert(json.status, 'Add User');
        });
}

function addGroup(): void {
    const groupName = $('#new_group_name').val();

    $.post('api/i/groups/create', { name: groupName }, null, 'json')
        .done(function() {
            location.reload();
        });
}

function showAddGroupDialog(): void {
    $.dialogBox(
        'Group Name:<br><br><input type="text" id="new_group_name">',
        addGroup,
        null,
        { title: 'Create new group', buttonText1: 'Create', height: 250, width: 300 }
    );
}

function init(): void {
    $('#add-user-btn').click(showAddUserDialog);
    $('#add-role-button').click(showAddGroupDialog);

    $('[data-group-id]').click(function() {
        editGroup($(this).data('group-id'));
    });

    $('[data-user-id]').click(function() {
        editUser($(this).data('user-id'));
    });

    const elmMount = document.getElementById('category-mgt');
    if (elmMount) {
        const app = Elm.Main.init<AdminElmApp, {}>({
            node: elmMount,
            flags: {},
        });

        app.ports.bindDialogDragAndCenter.subscribe((info: DialogInfo) =>
            requestAnimationFrame(() => {
                bindMouseMove(info.trigger, info.target);
                centerDialog(info.target);
            }));
    }
}
init();
