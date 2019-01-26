import { overflown } from '../modules/common';
import { Elm } from '../elm/Dashboard.elm';
import { bindMouseMove, centerDialog } from '../modules/dialogUtils';
import '../styles/dashboard.scss';

// Declared in a script block in the PHP template
declare const props: DashboardElmFlags;
let app: DashboardElmApp;

function checkOverflow(): void {
    const ids: number[] = [];
    const logs = Array.from(document.querySelector('#log-list').childNodes);

    logs.forEach(element => {
        if (element.childNodes.length < 2) return;

        const node = element.childNodes[1] as HTMLElement;
        if (overflown(node)) {
            ids.push(parseInt(node.dataset.logId));
        }
    });

    app.ports.reportOverflow.send(ids);
}

app = Elm.Main.init<DashboardElmApp, DashboardElmFlags>({
    node: document.getElementById('elm'),
    flags: props,
});

app.ports.detectOverflow.subscribe(() => requestAnimationFrame(checkOverflow));
app.ports.bindDialogDragAndCenter.subscribe((info: DialogInfo) =>
    requestAnimationFrame(() => {
        bindMouseMove(info.trigger, info.target);
        centerDialog(info.target);
    }));
