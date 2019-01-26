// Generic Elm application interfaces
interface ElmInit<T> {
    node: HTMLElement;
    flags: T;
}

declare module '*.elm' {
    export var Elm: {
        Main: {
            init<T, U>(obj: ElmInit<U>): T;
        };
    };
}

// Elm ports in both directions
interface JstoElmPort<T> {
    send: (params: T) => void;
}

interface ElmToJsPort<T> {
    subscribe: (callback: T) => void;
}

// Dashboard Elm application
interface DialogInfo {
    target: string;
    trigger: string;
}

interface DashboardElmApp {
    ports: {
        detectOverflow: ElmToJsPort<() => void>;
        reportOverflow: JstoElmPort<number[]>;

        bindDialogDrag: ElmToJsPort<(di: DialogInfo) => void>;
        centerDialog: ElmToJsPort<(id: string) => void>;
    };
};

interface DashboardElmFlags {
    showCreateButton: boolean;
    showLog: boolean;
    cheestoEnabledClass: string;
}
