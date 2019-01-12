/// <reference path="../dts/jquery.d.ts" />
/// <reference path="../dts/jqueryui.d.ts" />
/// <reference path="../dts/datetimepicker.d.ts" />

interface cheestoReadResponse {
    // 0 to many individual status arrays
    statuses: cheestoStatus[];
    statusOptions: string[];
}

interface cheestoStatus {
    id: number;
    user_id: number;
    status: string;
    message: string;
    returntime: string;
    modified: string;
    disabled: boolean;
    fullname: string;
}

const statusSelectClass = "__cheesto_status_select";
const statusTableClass = "__cheesto_status_table";

namespace Cheesto {
    let mountPoint = "";

    export function mount(elem: string): void {
        mountPoint = elem;
        getStatuses();
        setupDialog();
    }

    function setupDialog(): void {
        const quickTimes = $('[name="quicktime"]');
        quickTimes.each((index, elem) => {
            const el = $(elem);
            const time = parseInt(el.data('time-offset'));

            el.click(() => setDateTime(time));
        });
    }

    function getStatuses(): void {
        $.getJSON("api/i/cheesto/read",
            data => {
                if (!$.apiSuccess(data)) {
                    setTimeout(getStatuses, 60000);
                    return;
                }

                generateView(data.data);
                setTimeout(getStatuses, 30000);
            });
    }

    function generateView(dataObj: cheestoReadResponse): void {
        const view = $("<div/>").attr("id", mountPoint);
        view.append(makeStatusSelect(dataObj.statusOptions));
        view.append(generateTable(dataObj.statuses));
        $(`#${mountPoint}`).replaceWith(view);
    }

    function makeStatusSelect(statusOptions: string[]): JQuery {
        const statusSelect = $("<select/>");
        statusSelect.addClass(statusSelectClass);
        statusSelect.change(setStatus);

        statusSelect.append(`<option value="-1">Set Status:</option>`);

        for (const key2 in statusOptions) {
            const html = `<option value="${statusOptions[key2]}">${statusOptions[key2]}</option>`;
            statusSelect.append(html);
        }

        return statusSelect;
    }

    function generateTable(statuses: cheestoStatus[]): JQuery {
        const div = $("<div/>");
        div.addClass(statusTableClass);
        const table = $("<table/>");
        table.append(`<thead><tr><th>Name</th><th>Status</th></tr></thead><tbody>`);

        for (const key in statuses) {
            if (statuses.hasOwnProperty(key)) {
                const user = statuses[key];
                let html = "";
                // The modified date is in the format %Y-%m-%d %H:%m:%s
                // To match the return date, format to %m/%d/%Y %H:%m
                const modDate = new Date(user.modified);
                const formatMin = (modDate.getMinutes() < 10) ? "0"+modDate.getMinutes() : modDate.getMinutes();
                const formatHour = (modDate.getHours() < 10) ? "0"+modDate.getHours() : modDate.getHours();
                let formatModDate = (modDate.getMonth()+1)+"/"+modDate.getDate()+"/"+modDate.getFullYear();
                formatModDate += " "+formatHour+":"+formatMin;

                if (user.status === "Available") {
                    html = `<tr><td>${user.fullname}</td><td class="status-cell" title="Last Changed: ${formatModDate}">${user.status}</td></tr>`;
                } else {
                    // If the status is not Available show the return time and message
                    const message = (user.message === "") ? "" : `${user.message}\n\n`;
                    html = `<tr><td>${user.fullname}</td><td class="status-cell" title="${message}Return: ${user.returntime}\nLast Changed: ${formatModDate}">${user.status}</td></tr>`;
                }

                table.append(html);
            }
        }

        table.append("</tbody>");
        div.append(table);

        table.children("td.status-cell").tooltip({
            track: true,
            show: {
                effect: "fade",
                delay: 50
            }
        });

        return div;
    }

    function setStatus(event: JQueryEventObject): void {
        const newStatus: string = $(event.target).val();

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
                        processStatus(newStatus);
                    },
                    Cancel: function() {
                        $(this).dialog("close");
                        resetStatusSelect();
                    }
                }
            });
        } else {
            // Status is Available
            sendNewStatus(newStatus, "00:00:00", "");
        }
    }

    function resetStatusSelect(): void {
        $(`.${statusSelectClass}`).prop('selectedIndex', 0);
    }

    function processStatus(status: string): void {
        const message = $("#cheesto-message-text");
        const returnTime = $("#cheesto-date-pick");

        sendNewStatus(status, returnTime.val(), message.val());

        resetStatusSelect();
        message.val("");
        returnTime.val("Today");
        $("input[name=quicktime]").prop("checked", false);
    }

    function sendNewStatus(stat: string, rt: string, message: string): void {
        $.post("api/i/cheesto/update", {status: stat, returntime: rt, message: message})
            .done(getStatuses);
    }

    function setDateTime(delta: number): void {
        const currentdate = new Date();

        let minutes = currentdate.getMinutes()+(delta % 60);
        let hours = currentdate.getHours()+((delta-(delta % 60))/60);

        if (minutes > 59) {
            minutes = minutes - 60;
            hours++;
        }

        const datetime = `${(`0${(currentdate.getMonth()+1)}`).slice(-2)}/${(`0${currentdate.getDate()}`).slice(-2)}/${currentdate.getFullYear()} ${(`0${hours}`).slice(-2)}:${(`0${minutes}`).slice(-2)}`;

        $("#cheesto-date-pick").val(datetime);
    }
};

export default Cheesto;
