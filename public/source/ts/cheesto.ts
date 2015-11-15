/// <reference path="../dts/jquery.d.ts" />
/// <reference path="../dts/jqueryui.d.ts" />
/// <reference path="../dts/datetimepicker.d.ts" />
/* global $, setTimeout */

"use strict"; // jshint ignore:line

interface cheestoReadResponse {
    // 0 to many individual status arrays
    statusOptions: string[];
}

var Cheesto = {
    firstgen: true,

    dashboardInit: function(): void {
        if ($("#messages-cheesto").length) {
            Cheesto.getStatuses();
        }
    },

    getStatuses: function(): void {
        $.getJSON("api/i/cheesto/read",
            function (data) {
                if (!$.apiSuccess(data)) {
                    return;
                }

                Cheesto.generateView(data.data);
                setTimeout(Cheesto.getStatuses, 30000);

                if (Cheesto.firstgen) {
                    Cheesto.firstgen = false;
                }
            });
    },

    generateView: function(dataObj: cheestoReadResponse): void {
        Cheesto.generateTable(dataObj);
        if (Cheesto.firstgen) {
            Cheesto.makeStatusSelect(dataObj);
        }
        return;
    },

    makeStatusSelect: function(dataObj: cheestoReadResponse): void {
        var statusSelect = $("<select/>").attr("id", "status-select");
        statusSelect.change(Cheesto.setStatus);

        statusSelect.append(`<option value="-1">Set Status:</option>`);

        for (var key2 in dataObj.statusOptions) {
            var html = `<option value="${dataObj.statusOptions[key2]}">${dataObj.statusOptions[key2]}</option>`;
            statusSelect.append(html);
        }

        $("#status-select").replaceWith(statusSelect);
        return;
    },

    generateTable: function(dataObj: cheestoReadResponse): void {
        var div = $("<div/>").attr("id", "messages-cheesto-content");
        var table = $("<table/>");
        table.append(`<thead><tr><th>Name</th><th>Status</th></tr></thead><tbody>`);

        for (var key in dataObj) {
            if (dataObj.hasOwnProperty(key)) {
                if (key !== "statusOptions") {
                    var user = dataObj[key];
                    var html = "";
                    // The modified date is in the format %Y-%m-%d %H:%m:%s
                    // To match the return date, format to %m/%d/%Y %H:%m
                    var modDate = new Date(user.modified);
                    var formatMin = (modDate.getMinutes() < 10) ? "0"+modDate.getMinutes() : modDate.getMinutes();
                    var formatHour = (modDate.getHours() < 10) ? "0"+modDate.getHours() : modDate.getHours();
                    var formatModDate = (modDate.getMonth()+1)+"/"+modDate.getDate()+"/"+modDate.getFullYear();
                    formatModDate += " "+formatHour+":"+formatMin;

                    if (user.status === "Available") {
                        html = `<tr><td>${user.fullname}</td><td class="status-cell" title="Last Changed: ${formatModDate}">${user.status}</td></tr>`;
                    } else {
                        // If the status is not Available show the return time and message
                        var message = (user.message === "") ? "" : `Message: ${user.message}\n\n`;
                        html = `<tr><td>${user.fullname}</td><td class="status-cell" title="${message}Return: ${user.returntime}\n
                            Last Changed: ${formatModDate}">${user.status}</td></tr>`;
                    }

                    table.append(html);
                }
            }
        }

        table.append("</tbody>");
        div.append(table);
        $("#messages-cheesto-content").replaceWith(div);

        $("td.status-cell").tooltip({
            track: true,
            show: {
                effect: "fade",
                delay: 50
            }
        });
        return;
    },

    setStatus: function(): void {
        var newStatus: string = $("#status-select").val();

        if (newStatus !== "Available") {
            // Status requires a return time and optional message
            $("#cheesto-status-form").dialog({
                height: 440,
                width: 640,
                title: "Ĉeesto Status",
                modal: true,
                open: function(evt, ui) {
                    $("#cheesto-date-pick").datetimepicker({
                        timeFormat: "HH:mm",
                        controlType: "select",
                        stepMinute: 10,
                    });
                },
                show: {
                    effect: "fade",
                    duration: 500
                },
                hide: {
                    effect: "fade",
                    duration: 500
                },
                buttons: {
                    "Save": function() {
                        $(this).dialog("close");
                        Cheesto.processStatus(newStatus);
                    },
                    Cancel: function() {
                        $(this).dialog("close");
                        $("#status-select").prop("selectedIndex", 0);
                    }
                }
            });
        } else {
            // Status is Available
            Cheesto.sendNewStatus(newStatus, "00:00:00", "");
        }
    },

    processStatus: function(status: string): void {
        var message = $("#cheesto-message-text");
        var returnTime = $("#cheesto-date-pick");

        Cheesto.sendNewStatus(status, returnTime.val(), message.val());

        $("#status-select").prop("selectedIndex", 0);
        message.val("");
        returnTime.val("Today");
        $("input[name=quicktime]").prop("checked", false);
    },

    sendNewStatus: function (stat: string, rt: string, message: string): void {
        $.post("api/i/cheesto/update", {status: stat, returntime: rt, message: message})
            .done(function() { Cheesto.getStatuses(); });
    },

    setDateTime: function(delta: number): void {
        var currentdate = new Date();

        var minutes = currentdate.getMinutes()+(delta % 60);
        var hours = currentdate.getHours()+((delta-(delta % 60))/60);

        if (minutes > 59) {
            minutes = minutes - 60;
            hours++;
        }

        var datetime = `${(`0${(currentdate.getMonth()+1)}`).slice(-2)}/${(`0${currentdate.getDate()}`).slice(-2)}/${currentdate.getFullYear()} ${(`0${hours}`).slice(-2)}:${(`0${minutes}`).slice(-2)}`;

        $("#cheesto-date-pick").val(datetime);
    }
};
