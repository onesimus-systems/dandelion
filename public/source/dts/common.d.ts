/// <reference path="jquery.d.ts" />

interface dialogBoxOptions {
	height?: number;
	width?: number;
	title?: string;
	buttonText1?: string;
	buttonText2?: string;
}

interface apiResponse {
    errorcode: number;
}

interface JQueryStatic {
	alert(message: string, title: string, callback?: () => void): void;
    apiSuccess(response: apiResponse): boolean;
	flashMessage(message: string, domid?: string): void;
	confirmBox(message: string, title: string, ycallback?: () => void, ncallback?: () => void): void;
	dialogBox(html: string, ycallback?: () => void, ncallback?: () => void, options?: dialogBoxOptions): void;
}
