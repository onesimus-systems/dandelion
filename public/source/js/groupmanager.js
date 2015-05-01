/* global document, $, console, location */

var GroupManage = {};

GroupManage.init = function() {
    "use strict";
    $('#save-btn').click(GroupManage.save);
    $('#delete-btn').click(GroupManage.confirmDeleteGroup);
};

GroupManage.save = function() {
    "use strict";
    var permissions = {};
    $('form input[type="checkbox"]').each(function() {
        permissions[$(this).val()] = $(this).prop('checked');
    });

    var permissionsStr = JSON.stringify(permissions);
    var gid = $('#groupid').val();

    $.post('../../api/i/rights/edit', {groupid: gid, rights: permissionsStr}, null, 'json')
        .done(function(response) {
            if (response.errorcode === 0) {
                $.flashMessage('Group saved');
            } else {
                $.flashMessage('Error saving group');
            }
        });
};

GroupManage.confirmDeleteGroup = function() {
    "use strict";
    $.confirmBox("Are you sure you want to delete this group?",
        "Delete Group",
        GroupManage.deleteGroup
    );
};

GroupManage.deleteGroup = function() {
    "use strict";
    var gid = $('#groupid').val();

    $.post('../../api/i/rights/delete', {groupid: gid}, null, 'json')
        .done(function(data) {
            if (data.errorcode === 0) {
                $.alert('Group deleted successfully', 'Group Management', function() {
                    location.assign('../../admin');
                });
            } else {
                $.alert('Error deleting group', 'Group Management');
            }
        });
    return;
};

(function() {
    GroupManage.init();
})();
