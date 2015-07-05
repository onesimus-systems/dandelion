/// <reference path="cheesto.ts" />
/// <reference path="categories.ts" />
/// <reference path="../dts/jhtmlarea.d.ts" />
/**
 * Scripts for Dashboard
 */
/* global document, window, searchFun, $, setInterval,
    setTimeout, clearInterval, Categories, Cheesto */

"use strict"; // jshint ignore:line

var search = false,
    refreshc: number;

var Refresh,
    Section,
    View,
    Search,
    AddEdit;

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

Refresh = {
    init: function(): void {
        if ($("#log-list").length) {
            Refresh.refreshLog();
            Refresh.startrefresh();
        }
    },

    startrefresh: function(): void {
        refreshc = setInterval(function(){ Refresh.refreshLog(); }, 60000);
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

Section = {
    show: function(elem: any, panel: string): void {
        if (elem.innerHTML.match(/^Show\s/)) {
            elem.innerHTML = elem.innerHTML.replace(/^Show\s/, "Hide ");
        } else {
            elem.innerHTML = elem.innerHTML.replace(/^Hide\s/, "Show ");
        }

        $(`#${panel}`).toggleClass("enabled");
    }
};

View = {
    prevPage: -1,
    nextPage: -1,
    currentOffset: -1,

    init: function(): void {
        $("#prev-page-button").click(View.loadPrevPage);
        $("#next-page-button").click(View.loadNextPage);
        $("#create-log-button").click(AddEdit.showAddInputs);
        $("#clear-search-button").click(function() {
            $("#search-query").val("");
            Refresh.refreshLog(true);
        });
    },

    makeLogView: function(data: any): void {
        var logView = $("#log-list");
        logView.replaceWith(View.displayLogs(data));
        View.pageControls(data.metadata);
        View.currentOffset = data.metadata.offset;
    },

    displayLogs: function(data: any): string {
        var logs = `<div id="log-list">`;

        for (var key in data) {
            if (!data.hasOwnProperty(key) || key == "metadata") {
                continue;
            }

            var log = data[key];

            var creator = log.fullname;
            if (creator === "") {
                creator = "Unknown User";
            }

            // Display each log entry
            var html = `<div class="log-entry"><span class="log-title"><a href="log/${log.id}">${log.title}</a></span>`;

            if (log.canEdit) { html += `<button type="button" class="button edit-button" onClick="AddEdit.getEdit(${log.id});">Edit</button>`; }

            html += `<p class="log-body">${log.body}</p><p class="log-metadata"><span class="log-meta-author">Created by ${creator} on ${log.date_created} @ ${log.time_created} `;

            if (log.is_edited == "1") { html += "(Amended)"; }

            html += `</span><span class="log-meta-cat">Categorized as <a href="#" onClick="Search.searchLogLink('${log.category}');">${log.category}</a></span>`;
            html += `<span class="log-meta-comments">Comments: <a href="log/${log.id}#comments">${log.num_of_comments}</a></span></p></div>`;

            logs += html;
        }
        logs += "</div>";
        return logs;
    },

    pageControls: function(data: any): void {
        if (data.offset > 0) {
            View.prevPage = data.offset-data.limit;
            $("#prev-page-button").show();
        } else {
            View.prevPage = -1;
            $("#prev-page-button").hide();
        }

        if (data.offset+data.limit < data.logSize && data.resultCount == data.limit) {
            View.nextPage = data.offset+data.limit;
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
        $.getJSON("api/i/logs/read", {offset: pageOffset}, function(json) {
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

Search = {
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
            $("#search-query").val(queryBar+query);
        } else {
            $("#search-query").val(query);
        }
        Search.exec(query, 0);
    },

    // Send search query to server
    exec: function(query: string, offset?: number): boolean {
        if (typeof query === "undefined") { return false; }
        if (typeof offset === "undefined") { offset = 0; }

        $.post("api/i/logs/search", {query: query, offset: offset}, function(json) {
            search = true;
            Refresh.stoprefresh();
            View.makeLogView(json.data);
        }, "json");
    }
}; // Search

AddEdit = {
    showDialog: function(title: string, okText: string, okCall: () => void): void {
        var dialogButtons = {
            Cancel: null
        };
        dialogButtons[okText] = okCall;
        dialogButtons.Cancel = function() { $(this).dialog("close"); };

        $("#add-edit-form").dialog({
            height: 450,
            width: 610,
            title: title,
            modal: true,
            open: function(evt, ui) {
                $("#log-body").htmlarea({
                    toolbar: [
                        ["bold", "italic", "underline", "strikethrough", "|", "forecolor"],
                        ["p", "h1", "h2", "h3", "h4", "h5", "h6"],
                        ["link", "unlink", "|", "orderedList", "unorderedList", "|", "superscript", "subscript"]
                    ],
                    css: "assets/js/vendor/jhtmlarea/styles/jHtmlArea.Editor.css"
                });
                $("#log-body").htmlarea("updateHtmlArea");
            },
            show: {
                effect: "fade",
                duration: 500
            },
            hide: {
                effect: "fade",
                duration: 500
            },
            buttons: dialogButtons
        });
    },

    showEditInputs: function(logInfo: apiResponse): void {
        $("#log-title").val(logInfo.data[0].title);
        $("#log-body").val(logInfo.data[0].body);
        $("#categories").text("Loading categories...");

        Categories.renderCategoriesFromString(logInfo.data[0].category, "#categories");

        AddEdit.showDialog("Edit Log", "Save Edit", function() {
            AddEdit.saveLog(false, logInfo.data[0].id);
        });
    },

    showAddInputs: function(): void {
        $("#log-title").val("");
        $("#log-body").val("");
        $("#categories").empty();

        Categories.grabFirstLevel("#categories");

        AddEdit.showDialog("Create Log", "Save Log", function() {
            AddEdit.saveLog(true);
        });
    },

    getEdit: function(logid): void {
        $.post("api/i/logs/read", {logid: logid}, AddEdit.showEditInputs, "json");
    },

    saveLog: function(isnew: boolean, id?: number): void {
        var urlvars = {};
        var url = "";
        var title = $("#log-title").val();
        var entry = $("#log-body").val();
        var cat = Categories.getCatString();

        if (isnew) {
            urlvars = { title: title, body: entry, cat: cat };
            url = "api/i/logs/create";
        } else {
            urlvars = { logid: id, title: title, body: entry, cat: cat };
            url = "api/i/logs/edit";
        }

        if (title && entry && cat) {
            $.post(url, urlvars, function(json) {
                    Refresh.refreshLog();
                    $.alert(json.data, "Create Log", function() {
                        $("#add-edit-form").dialog("close");
                    });
                }, "json");
        } else {
            $("#messages").html(`<span class="bad">Log entries must have a title, category, and entry text.</span>`).fadeIn();
            setTimeout(function() { $("#messages").fadeOut(); }, 10000);
        }
    }
}; // AddEdit
