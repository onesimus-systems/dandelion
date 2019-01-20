import '../modules/common';

function enableUser(): void {
    const userId = $('#user-id').val();
    $.post('/api/i/users/enable', { uid: userId }, null, 'json')
        .done(function(data) {
            if ($.apiSuccess(data)) {
                $.alert('User enabled', 'User Management', function() {
                    location.reload(true);
                });
            } else {
                $.alert('Error enabling user', 'User Management');
            }
        });
}

function disableUser(): void {
    const userId = $('#user-id').val();
    $.post('/api/i/users/disable', { uid: userId }, null, 'json')
        .done(function(data) {
            if ($.apiSuccess(data)) {
                $.alert('User disabled', 'User Management', function() {
                    location.reload(true);
                });
            } else {
                $.alert('Error disabling user', 'User Management');
            }
        });
}

function deleteUser(): void {
    $.post('/api/i/users/delete', { uid: $('#user-id').val() }, null, 'json')
        .done(function(data) {
            if ($.apiSuccess(data)) {
                $.alert('User deleted successfully', 'User Management', function() {
                    location.assign('../../admin');
                });
            } else {
                $.alert('Error deleting user', 'User Management');
            }
        });
}

function confirmDeleteUser(): void {
    $.confirmBox('Disabling a user is prefered over deletion.<br><br>Are you sure you want to delete this user?',
        'Delete User',
        deleteUser
    );
}

function resetPassword(): void {
    const uid: number = $('#user-id').val();
    const pass1: string = $('#pass1').val();
    const pass2: string = $('#pass2').val();
    const forceReset: boolean = $('#force-reset-chk').prop('checked');
    $('#pass1').val('');
    $('#pass2').val('');

    if (pass1 === pass2 && pass1 !== '') {
        $.post('/api/i/users/resetpassword', { pw: pass1, uid: uid, 'force_reset': forceReset }, null, 'json')
            .done(function(data) {
                $.alert(data.data, 'User Management');
            });
    } else {
        $.alert('Passwords do not match or are empty', 'User Management');
    }
}

function showPasswordDialog(): void {
    $('#pwd-reset-dialog').dialog({
        modal: true,
        width: 400,
        height: 275,
        show: {
            effect: 'fade',
            duration: 500
        },
        hide: {
            effect: 'fade',
            duration: 250
        },
        buttons: [
            {
                text: 'Reset',
                click: function() {
                    $(this).dialog('close');
                    resetPassword();
                }
            },
            {
                text: 'Cancel',
                click: function() {
                    $(this).dialog('close');
                }
            }
        ]
    });
}

function revokeKey(): void {
    $.post('/api/i/key/revoke', { uid: $('#user-id').val() }, null, 'json')
        .done(function(data) {
            if ($.apiSuccess(data)) {
                $.alert('API key revoked', 'User Management');
            } else {
                $.alert('Error revoking API key', 'User Management');
            }
        });
}

function confirmRevokeKey(): void {
    $.confirmBox('Are you sure you want to revoke the API key?',
        'API Key Revoke',
        revokeKey
    );
}

function saveUser(): void {
    const userid: string = $('#user-id').val();
    const fullname: string = $('#fullname').val();
    const group: string = $('#user-group').val();
    const status: string = $('#user-status').val();
    const message: string = $('#user-status-message').val();
    const returntime: string = $('#user-status-return').val();
    const apiEnable: number = $('#user-api-override').val();

    $.post('/api/i/users/edit', { uid: userid, fullname: fullname, role: group, 'api_override': apiEnable }, null, 'json')
        .done(function(response) {
            if ($.apiSuccess(response)) {
                $.post('/api/i/cheesto/update', { uid: userid, message: message, status: status, returntime: returntime }, null, 'json')
                    .done(function(response) {
                        if ($.apiSuccess(response)) {
                            $.flashMessage('User saved');
                        } else {
                            $.flashMessage('Error saving user');
                        }
                    });
            } else {
                $.flashMessage('Error saving user');
            }
        });
}

function checkStatus(): void {
    const status: string = $('#user-status').val();

    if (status === 'Available') {
        $('#user-status-message').val('');
        $('#user-status-return').val('00:00:00');
    }
}

function init(): void {
    const disableBtn = $('#disable-user-btn');
    if (disableBtn.length) {
        disableBtn.click(disableUser);
    } else {
        $('#enable-user-btn').click(enableUser);
    }
    $('#delete-user-btn').click(confirmDeleteUser);
    $('#reset-pwd-btn').click(showPasswordDialog);
    $('#revoke-api-btn').click(confirmRevokeKey);
    $('#save-btn').click(saveUser);
    $('#user-status').change(checkStatus);
    $('#user-status-return').datetimepicker({
        timeFormat: 'HH:mm',
        controlType: 'select',
        stepMinute: 10
    });
}
init();
