// jQuery UI extention for an alert box
// Code from: http://coding.abel.nu/2012/01/jquery-ui-replacement-for-alert/
$.extend({
    alert: function(message: string, title: string, callback: CallableFunction): void {
        callback = callback || function() {};

        $('<div/>').dialog({
            buttons: { 'Ok': function() { $(this).dialog('close'); } },
            close: function(event, ui) { $(this).remove(); callback(event, ui); },
            resizable: false,
            title: title,
            modal: true,
        }).text(message);
    },
});

$.extend({
    decodeHTMLEntities: function(str: string): void {
        const element = document.createElement('div');
        // strip script/html tags
        str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
        str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
        element.innerHTML = str;
        str = element.textContent;
        element.textContent = '';
    },
});

$.extend({
    apiSuccess: function(response: APIResponse): boolean {
        if (response.errorcode === 0) {
            return true;
        }

        if (response.errorcode === 3) {
            // Code 3 means the user is not logged in
            location.assign('login');
        }
        return false;
    },
});

$.extend({
    flashMessage: function(msg: string, domid: string = '#message'): void {
        const message = $(domid);
        message.hide();
        message.text(msg);
        message.fadeIn();
        setTimeout(function() { message.fadeOut(); }, 5000);
    },
});

$.extend({
    confirmBox: function(message: string, title: string, yescallback: CallableFunction, nocallback: CallableFunction) {
        yescallback = yescallback || function() {};
        nocallback = nocallback || function() {};

        $('<div></div>').dialog({
            buttons: {
                'Ok': function() {
                    $(this).dialog('close');
                    yescallback();
                    $(this).remove();
                },
                'Cancel': function() {
                    $(this).dialog('close');
                    nocallback();
                    $(this).remove();
                },
            },
            resizable: false,
            title: title,
            modal: true,
        }).html(message);
    },
});

$.extend({
    urlParams: function(param: string): string {
        const parts = location.search.substring(1).split('&');
        for (let i = 0; i < parts.length; i++) {
            const nv = parts[i].split('=');
            if (!nv[0]) continue;
            if (nv[0] === param) return nv[1];
        }
        return null;
    },
});

$.extend({
    dialogBox: function(html: JQuery, yescallback: CallableFunction, nocallback: CallableFunction, options: DialogOptions): void {
        // Check customization settings
        options = options || {};
        options.height = options.height || 300;
        options.width = options.width || 450;
        options.title = options.title || '';
        options.buttonText1 = options.buttonText1 || 'Okay';
        options.buttonText2 = options.buttonText2 || 'Cancel';

        yescallback = yescallback || function() {};
        nocallback = nocallback || function() {};

        // Build dialog buttons
        const dialogButtons = {};
        dialogButtons[options.buttonText1] = function() {
            $(this).dialog('close');
            yescallback();
            $(this).remove();
        };
        dialogButtons[options.buttonText2] = function() {
            $(this).dialog('close');
            nocallback();
            $(this).remove();
        };

        // Build and show dialog
        const dialogBox = $('<div/>');
        dialogBox.append(html);
        dialogBox.dialog({
            height: options.height,
            width: options.width,
            modal: true,
            title: options.title,
            show: {
                effect: 'fade',
                duration: 500,
            },
            hide: {
                effect: 'fade',
                duration: 250,
            },
            buttons: dialogButtons,
        });
    },
});

export const overflown = (elem: HTMLElement): boolean => {
    return (elem.scrollHeight > elem.clientHeight && elem.scrollHeight > 220) || elem.scrollWidth > elem.clientWidth;
};
