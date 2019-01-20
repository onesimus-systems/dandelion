/// <reference path="../dts/Elm.d.ts" />
import "../modules/common";
import { Elm } from '../elm/Dashboard.elm';
import { bindMouseMove, centerDialog } from '../modules/dialogUtils';
import "../styles/dashboard.scss";

declare const props: {
    showCreateButton: boolean;
    showLog: boolean;
    cheestoEnabledClass: string;
};

let app: DashboardElmApp;

function init() {
    app = Elm.Main.init({
        node: document.getElementById('elm'),
        flags: props
    });

    app.ports.detectOverflow.subscribe(() => requestAnimationFrame(checkOverflow));
    app.ports.centerDialog.subscribe((id: string) => requestAnimationFrame(() => centerDialog(id)));
    app.ports.bindDialogDrag.subscribe((info: DialogInfo) =>
        requestAnimationFrame(() => bindMouseMove(info.trigger, info.target)));
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

init();
