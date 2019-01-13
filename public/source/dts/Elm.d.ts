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

interface LogMetadata {
    offset: number;
    limit: number;
    logSize: number;
    resultCount: number;
}

type DashboardElmApp = {
    ports: {
        searchQuery: IElmToJsPort<(text: string) => void>;
        logList: IJstoElmPort<any>;
        startTimedRefresh: IJstoElmPort<boolean>;
        pageInfo: IElmToJsPort<(metadata: LogMetadata) => void>;
        detectOverflow: IElmToJsPort<() => void>;
        reportOverflow: IJstoElmPort<number[]>;
    };
};
