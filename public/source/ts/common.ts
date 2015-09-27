/// <reference path="../dts/jquery.d.ts" />
/// <reference path="../dts/jqueryui.d.ts" />
//jQuery UI extention for an alert box
// Code from: http://coding.abel.nu/2012/01/jquery-ui-replacement-for-alert/
$.extend({
    alert: function(message, title, callback) {
        if (typeof callback === "undefined") {
            callback = function() { return; };
        }
        $("<div></div>").dialog({
            buttons: { "Ok": function() { $(this).dialog("close"); } },
            close: function(event, ui) { $(this).remove(); callback(); },
            resizable: false,
            title: title,
            modal: true
        }).text(message);
    }
});

$.extend({
    decodeHTMLEntities: function(str) {
        if(str && typeof str === "string") {
            var element = document.createElement("div");
            // strip script/html tags
            str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, "");
            str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, "");
            element.innerHTML = str;
            str = element.textContent;
            element.textContent = "";
        }

        return str;
      }
});

$.extend({
    apiSuccess: function(response) {
        if (response.errorcode === 0) {
            return true;
        } else if (response.errorcode === 3) {
            // Code 3 means the user is not logged in
            location.assign("login");
        }
        return false;
    }
});

$.extend({
    flashMessage: function(msg, domid) {
        if (typeof domid === "undefined") {
            domid = "#message";
        }
        var message = $(domid);
        message.hide();
        message.text(msg);
        message.fadeIn();
        setTimeout(function() { message.fadeOut(); }, 5000);
    }
});

$.extend({
    confirmBox: function(message, title, yescallback, nocallback) {
        if (typeof yescallback === "undefined") {
            yescallback = function() { return; };
        }
        if (typeof nocallback === "undefined") {
            nocallback = function() { return; };
        }

        $("<div></div>").dialog({
            buttons: {
                "Ok": function() { $(this).dialog("close"); yescallback(); $(this).remove(); },
                "Cancel": function() { $(this).dialog("close"); nocallback(); $(this).remove(); }
            },
            resizable: false,
            title: title,
            modal: true
        }).text(message);
    }
});

$.extend({
    dialogBox: function(html, yescallback, nocallback, customize) {
        // Check customization settings
        if (typeof customize == "undefined") {
            customize = {};
        }
        if (typeof customize.height == "undefined") {
            customize.height = 300;
        }
        if (typeof customize.width == "undefined") {
            customize.width = 450;
        }
        if (typeof customize.title == "undefined") {
            customize.title = "";
        }
        if (typeof customize.buttonText1 == "undefined") {
            customize.buttonText1 = "Okay";
        }
        if (typeof customize.buttonText2 == "undefined") {
            customize.buttonText2 = "Cancel";
        }
        if (typeof yescallback === "undefined" || yescallback === null) {
            yescallback = function() { return; };
        }
        if (typeof nocallback === "undefined" || nocallback === null) {
            nocallback = function() { return; };
        }

        // Build dialog buttons
        var dialog_buttons = {};
        dialog_buttons[customize.buttonText1] = function() {
            $(this).dialog("close");
            yescallback();
            $(this).remove();
            return;
        };
        dialog_buttons[customize.buttonText2] = function() {
            $(this).dialog("close");
            nocallback();
            $(this).remove();
            return;
        };

        // Build and show dialog
        var dialogBox = $("<div/>");
        dialogBox.append(html);
        dialogBox.dialog({
            height: customize.height,
            width: customize.width,
            modal: true,
            title: customize.title,
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
    }
});
