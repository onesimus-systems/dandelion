import "common";

function init(): void {
    const username = getCookie("dan_username");

    if (username !== null) {
        $("#remember-username").prop("checked", true);
        $("#username").val(username);
        $("#password").focus();
    } else {
        $("#username").focus();
    }

    $("#login-btn").click(attemptLogin);
    $("#username").on("keypress", check);
    $("#password").on("keypress", check);
}

const loginResetRedirect = "2";
const loginSuccessRedirect = "1";

function attemptLogin(): boolean {
    const user: string = $("#username").val();
    const pass: string = $("#password").val();
    const remember: boolean = $("#remember-username").prop("checked");

    if (!user || !pass) {
        return false;
    }

    $.post("login", { user: user, pass: pass, remember: remember }, null, "json")
        .done(function(response: any) {
            if (response != loginSuccessRedirect && response != loginResetRedirect) {
                $.alert("Login failed, please check your username and password", "Dandelion Login");
                return;
            }

            if (response == loginResetRedirect) {
                location.assign("reset");
                return;
            }

            const redirect = $.urlParams('redirect');

            if (redirect !== null) {
                location.assign(decodeURIComponent(redirect).substr(1));
            } else {
                location.assign(".");
            }
        });
}

function check(e: any): void {
    if (e.keyCode === 13) {
        attemptLogin();
    }
}

function getCookie(name: string): string {
    const re = new RegExp(name + "=([^;]+)");
    const value = re.exec(document.cookie);
    return (value !== null) ? decodeURI(value[1]) : null;
}

init();
