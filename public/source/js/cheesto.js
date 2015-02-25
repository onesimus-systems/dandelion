/* global $, clearTimeout, setTimeout, window */
/* jshint multistr: true */

"use strict"; // jshint ignore:line
var newStatus = 1;

var presence =
{
    timeoutId: 0,
    version: 0,
    firstgen: true,

    checkstat: function (ver) {
        $.getJSON("api/i/cheesto/read",
            function (data) {
                if (data.errorcode == 5) {
                    return;
                }

                presence.generateView(ver, data);
                clearTimeout(presence.timeoutId);
                delete presence.timeoutId;

                presence.version = ver;
                presence.timeoutId = setTimeout(function () {
                    presence.checkstat(ver);
                }, 30000);
            });
    },

    generateView: function (ver, dataObj) {
        dataObj = dataObj.data;

        if (!this.firstgen) {
            this.updateTableOnly(ver, dataObj);
            return;
        }

        var appendable = $('<div/>').attr('id', 'pt');

        // Generate select box of status options
        appendable.append(presence.makeStatusSelect(ver, dataObj));

        var userStatusesDiv = $('<div/>').attr('id', 'userStatuses');
        var tableView;
        // Generate status table depending on view version
        if (ver === 0) {
            tableView = this.makeTableMini(dataObj);
        }
        else if (ver === 1) {
            tableView = this.makeTableFull(dataObj);
        }
        userStatusesDiv.append(tableView);
        appendable.append(userStatusesDiv);

        $('#mainPresence').html(appendable);
    },

    updateTableOnly: function (ver, dataObj) {
        var tableView;
        // Generate status table depending on view version
        if (ver === 0) {
            tableView = this.makeTableMini(dataObj);
        }
        else if (ver === 1) {
            tableView = this.makeTableFull(dataObj);
        }
        $('#userStatuses').html(tableView);
    },

    /**
     * @param {{statusOptions:string}} dataObj
     */
    makeStatusSelect: function (ver, dataObj) {
        var statusSelectDiv = $('<div/>').attr('id', 'statusSelect');
        var statusSelect = $('<select/>').attr('id', 'cstatus');
        statusSelect.change(function () {
            presence.setStatus(ver);
        });
        statusSelect.append('<option value="-1">Set Status:</option>');

        for (var key2 in dataObj.statusOptions) {
            var html = '<option>' + dataObj.statusOptions[key2] + '</option>';
            statusSelect.append(html);
        }

        statusSelectDiv.append(statusSelect);
        this.firstgen = false;
        return statusSelectDiv;
    },

    /**
     * @param {{statusInfo:object}} dataObj
     */
    makeTableMini: function (dataObj) {
        // Mini view on main page
        var table = $('<table/>').addClass('userStatusTable');
        var tableHead = '<thead><tr>\
            <td width="50%">Name</td>\
            <td width="50%">Status</td>\
            </tr></thead>';

        table.append(tableHead);

        for (var key in dataObj) {
            if (!dataObj.hasOwnProperty(key))
                continue;

            if (key !== "statusOptions") {
                var user = dataObj[key];
                var classm = '';

                if (user.message !== '') {
                    classm = ' class="message"';
                }

                var html = '<tr>\
                    <td class="textLeft"><span title="' + user.message + '"' + classm + '>' + user.realname + '</span></td>\
                    <td><span title="' + dataObj.statusOptions[user.status] + '&#013;Return: ' + user.returntime + '" class="' + user.statusInfo.color + '">' + user.statusInfo.symbol + '</td>\
                    </tr>';

                table.append(html);
            }
        }

        var popOutButton = '<tr><td colspan="3" width="100%" class="cen">\
            <form><input type="button" onClick="presence.popOut();" class="linklike" value="Popout &#264;eesto"></form>\
            </td></tr>';
        table.append(popOutButton);

        return table;
    },

    /**
     * @param {{realname:string}} dataObj
     */
    makeTableFull: function (dataObj) {
        // Windowed view
        var table = $('<table/>').addClass('userStatusTable');
        var tableHead = '<thead><tr><td>Name</td>\
            <td>Message</td>\
            <td colspan="2">Status</td>\
            <td>Last Changed</td>\
            </tr></thead><tbody>';
        table.append(tableHead);

        for (var key in dataObj) { // jshint ignore:line
            if (!dataObj.hasOwnProperty(key))
                continue;

            if (key !== "statusOptions") {
                var user = dataObj[key];

                var html = '<tr>\
                    <td>' + user.realname + '</td>\
                    <td>' + user.message + '</td>\
                    <td class="statusi"><span class="' + user.statusInfo.color + '">' + user.statusInfo.symbol + '</span></td>';

                if (user.status == 0) { // jshint ignore:line
                    html += '<td>' + dataObj.statusOptions[user.status] + '</td>';
                } else {
                    html += '<td>' + dataObj.statusOptions[user.status] + '<br>Return: ' + user.returntime + '</td>';
                }

                html += '<td>' + user.dmodified + '</td></tr>';

                table.append(html);
            }
        }

        return table;
    },

    setStatus: function (ver) {
        newStatus = $("select#cstatus").prop("selectedIndex") - 1;
        $("select#cstatus").prop("selectedIndex", 0);
        var rtime;

        if (newStatus > 0) {
            // Status requires a return time and optional status
            rtime = ""; // jshint ignore:line
            window.open("getdate", "getdate", "location=no,menubar=no,scrollbars=no,status=no,height=550,width=350");
        }
        else if (newStatus === 0) {
            // Status is Available
            rtime = "00:00:00"; // jshint ignore:line
            presence.sendNewStatus(0, rtime, ver, "");
        }
    },

    popOut: function () {
        window.open("presenceWindow", "presencewin", "location=no,menubar=no,scrollbars=no,status=no,height=500,width=950");
    },

    sendNewStatus: function (stat, rt, ver, message) {
        $.post("api/i/cheesto/update", {status: stat, returntime: rt, message: message},
            function () {
                presence.checkstat(ver);
            });
    },

    showHideP: function () {
        if ($("#showHide").html() == "[ - ]") {
            $("#presence").css("minWidth", $("#mainPresence").prop("offsetWidth") + "px");
            $("#mainPresence").css("display", "none");
            $("#showHide").html("[ + ]");
        }
        else {
            $("#presence").css("minWidth", "0px");
            $("#mainPresence").css("display", "");
            $("#showHide").html("[ - ]");
        }
    }
};
