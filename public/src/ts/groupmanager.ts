import '../modules/common';

function save(): void {
    const permissions = {};
    $('form input[type="checkbox"]').each(function() {
        permissions[$(this).val()] = $(this).prop('checked');
    });

    const permissionsStr = JSON.stringify(permissions);
    const gid: string = $('#groupid').val();

    $.post('/api/i/groups/edit', { groupid: gid, rights: permissionsStr }, null, 'json')
        .done(function(response) {
            if ($.apiSuccess(response)) {
                $.flashMessage('Group saved');
            } else {
                $.flashMessage('Error saving group');
            }
        });
}

function deleteGroup(): void {
    const gid: string = $('#groupid').val();

    $.post('/api/i/groups/delete', { groupid: gid }, null, 'json')
        .done(function(data) {
            if ($.apiSuccess(data)) {
                $.alert('Group deleted successfully', 'Group Management', function() {
                    location.assign('../../admin');
                });
            } else {
                $.alert(`Error deleting group: ${data.status}`, 'Group Management');
            }
        });
}

function confirmDeleteGroup(): void {
    $.confirmBox('Are you sure you want to delete this group?',
        'Delete Group',
        deleteGroup
    );
}

function init(): void {
    $('#save-btn').click(save);
    $('#delete-btn').click(confirmDeleteGroup);
}
init();
