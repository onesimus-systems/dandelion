const GroupManage = {
    init: function(): void {
        "use strict";
        $("#save-btn").click(GroupManage.save);
        $("#delete-btn").click(GroupManage.confirmDeleteGroup);
    },

    save: function(): void {
        "use strict";
        var permissions = {};
        $(`form input[type="checkbox"]`).each(function() {
            permissions[$(this).val()] = $(this).prop("checked");
        });

        var permissionsStr: string = JSON.stringify(permissions);
        var gid: string = $("#groupid").val();

        $.post("../../api/i/groups/edit", {groupid: gid, rights: permissionsStr}, null, "json")
            .done(function(response) {
                if ($.apiSuccess(response)) {
                    $.flashMessage("Group saved");
                } else {
                    $.flashMessage("Error saving group");
                }
            });
    },

    confirmDeleteGroup: function(): void {
        "use strict";
        $.confirmBox("Are you sure you want to delete this group?",
            "Delete Group",
            GroupManage.deleteGroup
        );
    },

    deleteGroup: function(): void {
        "use strict";
        var gid: string = $("#groupid").val();

        $.post("../../api/i/groups/delete", {groupid: gid}, null, "json")
            .done(function(data) {
                if ($.apiSuccess(data)) {
                    $.alert("Group deleted successfully", "Group Management", function() {
                        location.assign("../../admin");
                    });
                } else {
                    $.alert(`Error deleting group: ${data.status}`, "Group Management");
                }
            });
        return;
    }
};

(function() {
    GroupManage.init();
})();
