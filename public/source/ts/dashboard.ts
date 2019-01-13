/// <reference path="../dts/Elm.d.ts" />
import Cheesto from '../modules/cheesto';
import Categories from '../modules/categories';
import "../modules/common";
import { Elm } from '../elm/LogTable.elm';

let search = false;
let app: DashboardElmApp;

function init() {
    View.init();
    Search.init();
    Cheesto.mount("messages-cheesto");

    $("#show-cheesto-button").click(function() {
        showSection(this, "messages-panel");
    });

    $("#show-logs-button").click(function() {
        showSection(this, "logs-panel");
    });

    app = Elm.Main.init({
        node: document.getElementById('log-list')
    });

    app.ports.searchQuery.subscribe(Search.searchLogLink);
    app.ports.pageInfo.subscribe(View.pageControls);
    // Wait for the DOM to update before checking overflown elements
    app.ports.detectOverflow.subscribe(() => requestAnimationFrame(View.checkOverflow));
}

function showSection(elem: any, panel: string): void {
    if (elem.innerHTML.match(/^Show\s/)) {
        elem.innerHTML = elem.innerHTML.replace(/^Show\s/, "Hide ");
    } else {
        elem.innerHTML = elem.innerHTML.replace(/^Hide\s/, "Show ");
    }

    $(`#${panel}`).toggleClass("enabled");
}

namespace View {
    let prevPage = -1;
    let nextPage = -1;

    export function init(): void {
        $("#prev-page-button").click(loadPrevPage);
        $("#next-page-button").click(loadNextPage);
        $("#create-log-button").click(function() {
            location.assign("log/new");
        });
        $("#clear-search-button").click(function() {
            $("#search-query").val("");
            search = false;
            app.ports.startTimedRefresh.send(true);
        });
    }

    export function checkOverflow(): void {
        const ids: number[] = [];
        const logs = Array.from(($("#log-list")[0]).childNodes);

        logs.forEach(element => {
            if (element.childNodes.length < 2) return;

            const b = $(element.childNodes[1]);
            if (b.overflown()) {
                ids.push(parseInt(b.data("log-id")))
            }
        });

        app.ports.reportOverflow.send(ids);
    }

    export function pageControls(data: any): void {
        if (data.offset > 0) {
            prevPage = data.offset - data.limit;
            $("#prev-page-button").show();
        } else {
            prevPage = -1;
            $("#prev-page-button").hide();
        }

        if (data.offset + data.limit < data.logSize && data.resultCount == data.limit) {
            nextPage = data.offset + data.limit;
            $("#next-page-button").show();
        } else {
            nextPage = -1;
            $("#next-page-button").hide();
        }

        if (search) {
            $("#clear-search-button").show();
        } else {
            $("#clear-search-button").hide();
        }
    }

    function loadPrevPage(): void {
        if (prevPage >= 0) {
            if (search) {
                Search.searchLog(prevPage);
            } else {
                pagentation(prevPage);
            }
        }
    }

    function loadNextPage(): void {
        if (nextPage >= 0) {
            if (search) {
                Search.searchLog(nextPage);
            } else {
                pagentation(nextPage);
            }
        }
    }

    function pagentation(pageOffset: number): void {
        $.getJSON("api/i/logs/read", { offset: pageOffset }, function(json) {
            app.ports.logList.send(json);
            pageControls(json.data.metadata);

            if (pageOffset <= 0) {
                app.ports.startTimedRefresh.send(true);
            }
        });
    }
}; // View

namespace Search {
    export function init(): void {
        $("#search-btn").click(searchLog);
        $("#query-builder-btn").click(showBuilder);
        $("#search-query").on("keypress", check);
        $("#qb-date1").change(function() {
            if (!$("#qb-date2").val()) {
                $("#qb-date2").val($("#qb-date1").val());
            }
        });
    }

    function showBuilder(): void {
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
                    buildQuery();
                    $(this).dialog("close");
                    clearBuilderForm();
                },
                Cancel: function() {
                    $(this).dialog("close");
                    clearBuilderForm();
                }
            }
        });
    }

    function buildQuery(): void {
        const title = $("#qb-title");
        const titleNot = $("#qb-title-not");
        const body = $("#qb-body");
        const bodyNot = $("#qb-body-not");
        const dateNot = $("#qb-date-not");
        const date1 = $("#qb-date1");
        const date2 = $("#qb-date2");
        const cat = Categories.getCatString();
        const catNot = $("#qb-cat-not");
        let query = "";

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
            const negate = dateNot.prop("checked") ? "!" : "";
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
        exec(query, 0);
    }

    function clearBuilderForm(): void {
        $("#qb-title").val("");
        $("#qb-body").val("");
        $("#qb-date1").val("");
        $("#qb-date2").val("");
        $("#qb-title-not").prop("checked", false);
        $("#qb-body-not").prop("checked", false);
        $("#qb-date-not").prop("checked", false);
        $("#qb-cat-not").prop("checked", false);
        $("#categories2").empty();
    }

    // Checks if enter key was pressed, if so search
    function check(e: any): void {
        if (e.keyCode == 13) {
            e.preventDefault();
            searchLog();
        }
    }

    // Execute search from button or enter key
    export function searchLog(offset?: number): void {
        if (typeof offset !== "number") { offset = 0; }
        const query = $("#search-query").val();
        exec(query, offset);
    }

    // Execute category search from link
    export function searchLogLink(query: string): void {
        query = ` category:"${query}"`;
        if (search) {
            const queryBar = $("#search-query").val();
            $("#search-query").val(queryBar + query);
        } else {
            $("#search-query").val(query);
        }
        exec(query, 0);
    }

    // Send search query to server
    function exec(query: string, offset?: number): boolean {
        if (typeof query === "undefined") { return false; }
        if (typeof offset === "undefined") { offset = 0; }

        $.get("api/i/logs/search", { query: query, offset: offset }, function(json) {
            search = true;
            app.ports.logList.send(json)
        }, "json")
            .fail(function(json) {
                $.alert(json.responseJSON.status, "Server Error");
            });
    }
}; // Search

init();
