declare module "*.elm" {
    export var Elm: {
        Main: any
    };
}

interface IJstoElmPort<T> {
    send: (params: T) => void;
}

interface IElmToJsPort<T> {
    subscribe: (callback: T) => void;
}

interface DialogInfo {
    target: string;
    trigger: string;
}

type DashboardElmApp = {
    ports: {
        detectOverflow: IElmToJsPort<() => void>;
        reportOverflow: IJstoElmPort<number[]>;

        bindDialogDrag: IElmToJsPort<(DialogInfo) => void>;
        centerDialog: IElmToJsPort<(string) => void>;
    };
};
