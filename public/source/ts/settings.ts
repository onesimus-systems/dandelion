declare var page: string;

$(document).ready(function(){
    if (typeof page === "undefined") {
        page = "";
    }

    if (page === "initialReset") {
        $("#new-password-1").on("keypress", Settings.check);
        $("#new-password-2").on("keypress", Settings.check);
        $("#new-password-1").focus();
    } else {
        $("#save-per-page-btn").click(Settings.savePerPage);
        $("#save-theme-btn").click(Settings.saveTheme);
        $("#generate-apikey-btn").click(Settings.generateKey);
    }
    $("#reset-password-btn").click(Settings.resetPassword);
});

const Settings = {
    generateKey: function(): void {
        $.post("api/i/key/generate", {}, function(data) {
                $("#apikey").html(`<strong>Key:</strong> ${data.data}`);
        }, "json");
    },

    resetPassword: function(): void {
        var pw1: string = $("#new-password-1").val();
        var pw2: string = $("#new-password-2").val();
        $("#new-password-1").val("");
        $("#new-password-2").val("");

        if (pw1 === pw2 && pw1 !== "") {
            $.post("api/i/users/resetpassword", {"pw": pw1}, null, "json")
                .done(function(msg) {
                    if (!$.apiSuccess(msg)) {
                        // Display error if password didn't save correctly
                        $.alert("Error saving password: "+msg.data, "Password Reset", function() {
                            $("#new-password-1").focus();
                        });
                        return;
                    }

                    // If the page is an initial reset, redirect to dashboard
                    // otherwise show a confirmation box
                    if (page === "initialReset") {
                        location.assign(".");
                    } else {
                        $.alert(msg.data, "Password Reset");
                    }
                });
        } else {
            $.alert("Passwords do not match or are empty", "Password Reset", function() {
                $("#new-password-1").focus();
            });
        }
    },

    savePerPage: function(): void {
        var limit: string = $("#page-limit").val();
        $.post("api/i/usersettings/saveloglimit", {"limit": limit}, null, "json")
            .done(function(msg) {
                $.alert(msg.data, "Settings");
            });
    },

    saveTheme: function(): void {
        var theme: string = $("#theme").val();
        $.post("api/i/usersettings/savetheme", {"theme": theme}, null, "json")
            .done(function(msg) {
                $.alert(msg.data, "Settings", function() { document.location.reload(true); });
            });
    },

    check: function(e: any): void {
        if (e.keyCode === 13) {
            Settings.resetPassword();
        }
    },
};
