/// <reference path="jquery.d.ts" />

interface DialogOptions {
    height?: number;
    width?: number;
    title?: string;
    buttonText1?: string;
    buttonText2?: string;
}

interface APIResponse {
    data: any;
    errorcode: number;
    module: string;
    status: string;
    requestTime: string;
}

interface JQueryStatic {
    alert(message: string, title: string, callback?: () => void): void;
    decodeHTMLEntities(str: string): string;
    apiSuccess(response: APIResponse): boolean;
    flashMessage(message: string, domid?: string): void;
    confirmBox(message: string, title: string, ycallback?: () => void, ncallback?: () => void): void;
    dialogBox(html: string, ycallback?: () => void, ncallback?: () => void, options?: DialogOptions): void;
    urlParams(param: string): string;
}
