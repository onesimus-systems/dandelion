//jQuery UI extention for an alert box
// Code from: http://coding.abel.nu/2012/01/jquery-ui-replacement-for-alert/
$.extend({alert: function (message, title, callback) {
    if (typeof callback === 'undefined') {
        callback = function(){};
    }
    $("<div></div>").dialog({
        buttons: { "Ok": function () { $(this).dialog("close"); } },
        close: function (event, ui) { $(this).remove(); callback(); },
        resizable: false,
        title: title,
        modal: true
    }).text(message);
}
});

$.extend({confirmBox: function (message, title, yescallback, nocallback) {
    if (typeof yescallback === 'undefined') {
        yescallback = function(){};
    }
    if (typeof nocallback === 'undefined') {
        nocallback = function(){};
    }

    $("<div></div>").dialog({
        buttons: { "Yes": function () { $(this).dialog("close"); yescallback(); $(this).remove(); },
            "Cancel": function() { $(this).dialog("close"); nocallback(); $(this).remove(); }
        },
        resizable: false,
        title: title,
        modal: true
    }).text(message);
}
});

$.extend({dialogBox: function(html, yescallback, nocallback, customize) {
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
    if (typeof yescallback === 'undefined' || yescallback === null) {
        yescallback = function(){};
    }
    if (typeof nocallback === 'undefined' || nocallback === null) {
        nocallback = function(){};
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
    var dialogBox = $('<div/>');
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
