//jQuery UI extention for an alert box
// Code from: http://coding.abel.nu/2012/01/jquery-ui-replacement-for-alert/
$.extend({ alert: function (message, title, callback) {
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
