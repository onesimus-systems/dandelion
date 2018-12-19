import Cheesto from 'cheesto';
import Categories from 'categories';

var search = false,
    refreshc: number;

$(document).ready(function() {
    Refresh.init();
    View.init();
    Search.init();
    Cheesto.dashboardInit();

    $("#show-cheesto-button").click(function() {
        Section.show(this, "messages-panel");
    });

    $("#show-logs-button").click(function() {
        Section.show(this, "logs-panel");
    });
});

const Refresh = {
    init: function(): void {
        if ($("#log-list").length) {
            Refresh.refreshLog();
            Refresh.startrefresh();
        }
    },

    startrefresh: function(): void {
        refreshc = setInterval(function() { Refresh.refreshLog(); }, 60000);
    },

    stoprefresh: function(): void {
        clearInterval(refreshc);
    },

    refreshLog: function(clearSearch?: boolean): void {
        if (clearSearch) {
            search = false;
            Refresh.startrefresh();
        }

        if (!search) {
            $.getJSON("api/i/logs/read", {}, function(json: apiResponse) {
                if ($.apiSuccess(json)) {
                    View.makeLogView(json.data);
                } else {
                    Refresh.stoprefresh();
                }
            });
        }
    }
}; // Refresh

const Section = {
    show: function(elem: any, panel: string): void {
        if (elem.innerHTML.match(/^Show\s/)) {
            elem.innerHTML = elem.innerHTML.replace(/^Show\s/, "Hide ");
        } else {
            elem.innerHTML = elem.innerHTML.replace(/^Hide\s/, "Show ");
        }

        $(`#${panel}`).toggleClass("enabled");
    }
};

const View = {
    prevPage: -1,
    nextPage: -1,
    currentOffset: -1,

    init: function(): void {
        $("#prev-page-button").click(View.loadPrevPage);
        $("#next-page-button").click(View.loadNextPage);
        $("#create-log-button").click(function() {
            location.assign("log/new");
        });
        $("#clear-search-button").click(function() {
            $("#search-query").val("");
            Refresh.refreshLog(true);
        });
    },

    makeLogView: function(data: any): void {
        var logView = $("#log-list");
        var newLogs = $(View.displayLogs(data));
        logView.replaceWith(newLogs);
        View.pageControls(data.metadata);
        View.currentOffset = data.metadata.offset;
        View.checkOverflow(newLogs);
    },

    checkOverflow: function(logView: JQuery): void {
        var logs = $(logView[0].childNodes);
        logs.each(function(index, elem) {
            var b = $(elem.childNodes[1]);
            if (b.overflown()) {
                var id = b.data("log-id");
                $(`<div class="log-overflow"><a href="log/${id}" target="_blank">Read more...</a></div>`).insertAfter(b);
            }
        });
    },

    displayLogs: function(data: any): string {
        var logs = `<div id="log-list">`;

        for (var key in data) {
            if (!data.hasOwnProperty(key) || !$.isNumeric(key)) {
                continue;
            }

            var log = data[key];

            var creator = log.fullname;
            if (creator === "" || creator === null) {
                creator = "Deleted User";
            }

            // Display each log entry
            var html = `<div class="log-entry"><span class="log-title"><a href="log/${log.id}">${log.title}</a></span>`;

            html += `<div class="log-body" data-log-id="${log.id}">${log.body}</div><div class="log-metadata"><span class="log-meta-author">Created by ${creator} on ${log.date_created} @ ${log.time_created} `;

            if (log.is_edited == "1") { html += "(Amended)"; }

            html += `</span><span class="log-meta-cat">Categorized as <a href="#" onClick="Search.searchLogLink('${log.category}');">${log.category}</a></span>`;
            html += `<span class="log-meta-comments">Comments: <a href="log/${log.id}#comments">${log.num_of_comments}</a></span></div></div>`;

            logs += html;
        }
        logs += "</div>";
        return logs;
    },

    pageControls: function(data: any): void {
        if (data.offset > 0) {
            View.prevPage = data.offset - data.limit;
            $("#prev-page-button").show();
        } else {
            View.prevPage = -1;
            $("#prev-page-button").hide();
        }

        if (data.offset + data.limit < data.logSize && data.resultCount == data.limit) {
            View.nextPage = data.offset + data.limit;
            $("#next-page-button").show();
        } else {
            View.nextPage = -1;
            $("#next-page-button").hide();
        }

        if (search) {
            $("#clear-search-button").show();
        } else {
            $("#clear-search-button").hide();
        }
    },

    loadPrevPage: function(): void {
        if (View.prevPage >= 0) {
            if (search) {
                Search.searchLog(View.prevPage);
            } else {
                View.pagentation(View.prevPage);
            }
        }
    },

    loadNextPage: function(): void {
        if (View.nextPage >= 0) {
            if (search) {
                Search.searchLog(View.nextPage);
            } else {
                View.pagentation(View.nextPage);
            }
        }
    },

    pagentation: function(pageOffset: number): void {
        $.getJSON("api/i/logs/read", { offset: pageOffset }, function(json) {
            View.makeLogView(json.data);

            if (pageOffset <= 0) {
                Refresh.refreshLog();
                Refresh.startrefresh();
            } else {
                Refresh.stoprefresh();
            }
        });
    }
}; // View

const Search = {
    init: function(): void {
        $("#search-btn").click(Search.searchLog);
        $("#query-builder-btn").click(Search.showBuilder);
        $("#search-query").on("keypress", Search.check);
        $("#qb-date1").change(function() {
            if (!$("#qb-date2").val()) {
                $("#qb-date2").val($("#qb-date1").val());
            }
        });
    },

    showBuilder: function(): void {
        $("#query-builder-form").dialog({
            height: 380,
            width: 540,
            title: "Search Query Builder",
            modal: true,
            open: function(evt, ui) {
                $("#qb-date1").datepicker();
                $("#qb-date2").datepicker();
                Categories.grabFirstLevel("#categories2");
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
                "Search": function() {
                    Search.buildQuery();
                    $(this).dialog("close");
                    Search.clearBuilderForm();
                },
                Cancel: function() {
                    $(this).dialog("close");
                    Search.clearBuilderForm();
                }
            }
        });
    },

    buildQuery: function(): void {
        var title = $("#qb-title");
        var titleNot = $("#qb-title-not");
        var body = $("#qb-body");
        var bodyNot = $("#qb-body-not");
        var dateNot = $("#qb-date-not");
        var date1 = $("#qb-date1");
        var date2 = $("#qb-date2");
        var cat = Categories.getCatString();
        var catNot = $("#qb-cat-not");
        var query = "";

        if (title.val()) {
            if (titleNot.prop("checked")) {
                query += ` title:"!${title.val().replace(`"`, "\\\"")}"`;
            } else {
                query += ` title:"${title.val().replace(`"`, "\\\"")}"`;
            }
        }

        if (body.val()) {
            if (bodyNot.prop("checked")) {
                query += ` body:"!${body.val().replace(`"`, "\\\"")}"`;
            } else {
                query += ` body:"${body.val().replace(`"`, "\\\"")}"`;
            }
        }

        if (date1.val()) {
            var negate = "";
            if (dateNot.prop("checked")) {
                negate = "!";
            }
            if (date2.val() && date1.val() != date2.val()) {
                query += ` date:"${negate}${date1.val()} to ${date2.val()}"`;
            } else {
                query += ` date:"${negate}${date1.val()}"`;
            }
        }

        if (cat) {
            if (catNot.prop("checked")) {
                query += ` category:"!${cat}" `;
            } else {
                query += ` category:"${cat}" `;
            }
        }

        $("#search-query").val(query);
        Search.exec(query, 0);
    },

    clearBuilderForm: function(): void {
        $("#qb-title").val("");
        $("#qb-body").val("");
        $("#qb-date1").val("");
        $("#qb-date2").val("");
        $("#qb-title-not").prop("checked", false);
        $("#qb-body-not").prop("checked", false);
        $("#qb-date-not").prop("checked", false);
        $("#qb-cat-not").prop("checked", false);
        $("#categories2").empty();
    },

    // Checks if enter key was pressed, if so search
    check: function(e: any): void {
        if (e.keyCode == 13) {
            e.preventDefault();
            Search.searchLog();
        }
    },

    // Execute search from button or enter key
    searchLog: function(offset?: number): void {
        if (typeof offset !== "number") { offset = 0; }
        var query = $("#search-query").val();
        Search.exec(query, offset);
    },

    // Execute category search from link
    searchLogLink: function(query: string): void {
        query = ` category:"${query}"`;
        if (search) {
            var queryBar = $("#search-query").val();
            $("#search-query").val(queryBar + query);
        } else {
            $("#search-query").val(query);
        }
        Search.exec(query, 0);
    },

    // Send search query to server
    exec: function(query: string, offset?: number): boolean {
        if (typeof query === "undefined") { return false; }
        if (typeof offset === "undefined") { offset = 0; }

        $.get("api/i/logs/search", { query: query, offset: offset }, function(json) {
            search = true;
            Refresh.stoprefresh();
            View.makeLogView(json.data);
        }, "json")
            .fail(function(json) {
                $.alert(json.responseJSON.status, "Server Error");
            });
    }
}; // Search
