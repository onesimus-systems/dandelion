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

interface SearchRequest {
    query: string;
    offset: number;
}

type DashboardElmApp = {
    ports: {
        detectOverflow: IElmToJsPort<() => void>;
        openSearchBuilder: IElmToJsPort<() => void>;
        reportOverflow: IJstoElmPort<number[]>;
        searchQueryExt: IJstoElmPort<string>;
    };
};
