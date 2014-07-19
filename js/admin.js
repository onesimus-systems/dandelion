/* global $, window, alert */
/* exported adminAction */

"use strict"; // jshint ignore:line

var adminAction =
{
	editSlogan: function(current) {
		var newSlogan = window.prompt('Website Slogan', current);
		
		if (newSlogan !== '' && newSlogan !== null)
            this.performAction("saveSlogan", newSlogan);
	},
	
	backupDB: function() {
		$.post("lib/admin_actions.php", { action: "backupDB" })
            .done(function( msg ) {
                alert(msg);
            });
	},
	
	saveDefaultTheme: function() {
        var newTheme = $("#userTheme").val();
        this.performAction("saveDefaultTheme", newTheme);
	},
	
	saveCheesto: function() {
        var cheesto = $("#cheesto_enabled").val();
        this.performAction("saveCheesto", cheesto);
	},
	
	saveApiSetting: function() {
	    var pAPI = $("#api_enabled").val();
	    this.performAction("savePAPI", pAPI);
	},
	
	performAction: function(action, data) {
        $.post("lib/admin_actions.php", { action: action, data: encodeURIComponent(data) })
            .done(function( msg ) {
                alert(msg);
            });
	}
};