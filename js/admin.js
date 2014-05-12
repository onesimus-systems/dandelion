var adminAction =
{
	editSlogan: function(current) {
		var newSlogan = window.prompt('Website Slogan', current);
		
		if (newSlogan !== '' && newSlogan !== null)
            this.performAction("slogan", newSlogan);
	},
	
	backupDB: function() {
		$.post("scripts/admin_actions.php", { sub_action: "backupdb" })
            .done(function( msg ) {
                alert(msg);
            });
	},
	
	saveDefaultTheme: function() {
        var newTheme = $("#userTheme").val();
        this.performAction("defaultTheme", newTheme);
	},
	
	saveCheesto: function() {
        var cheesto = $("#cheesto_enabled").val();
        this.performAction("cheesto", cheesto);
	},
	
	performAction: function(action, data) {
        $.post("scripts/admin_actions.php", { sub_action: action, data: encodeURIComponent(data) })
            .done(function( msg ) {
                alert(msg);
            });
	}
};