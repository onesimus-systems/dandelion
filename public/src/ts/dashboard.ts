/// <reference path="../dts/Elm.d.ts" />
import Cheesto from '../modules/cheesto';
import Categories from '../modules/categories';
import "../modules/common";
import { Elm } from '../elm/Dashboard.elm';

declare const props: {
    showCreateButton: boolean;
    showLog: boolean;
    cheestoEnabledClass: string;
};

let app: DashboardElmApp;

function init() {
    Cheesto.mount("messages-cheesto");

    $("#show-cheesto-button").click(function() {
        showSection(this, "messages-panel");
    });

    $("#show-logs-button").click(function() {
        showSection(this, "logs-panel");
    });

    app = Elm.Main.init({
        node: document.getElementById('logs-panel'),
        flags: props
    });

    app.ports.detectOverflow.subscribe(() => requestAnimationFrame(checkOverflow));
    app.ports.openSearchBuilder.subscribe(SearchBuilder.show);
}

function showSection(elem: any, panel: string): void {
    if (elem.innerHTML.match(/^Show\s/)) {
        elem.innerHTML = elem.innerHTML.replace(/^Show\s/, "Hide ");
    } else {
        elem.innerHTML = elem.innerHTML.replace(/^Hide\s/, "Show ");
    }

    $(`#${panel}`).toggleClass("enabled");
}

function checkOverflow(): void {
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

namespace SearchBuilder {
    let initRan = false;

    function init(): void {
        $("#qb-date1").change(function() {
            if (!$("#qb-date2").val()) {
                $("#qb-date2").val($("#qb-date1").val());
            }
        });
    }

    export function show(): void {
        if (!initRan) {
            init();
            initRan = true;
        }

        $("#query-builder-form").dialog({
            height: 480,
            width: 540,
            title: "Search Query Builder",
            modal: true,
            open: function(evt, ui) {
                $(".qb-date").datepicker();
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
        app.ports.searchQueryExt.send(query);
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
}; // SearchBuilder

init();
