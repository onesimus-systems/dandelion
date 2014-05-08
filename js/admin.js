var adminAction = {
	editSlogan: function(current) {
		var newSlogan = window.prompt('Website Slogan', current);
		
		if (newSlogan !== '' && newSlogan !== null) {
			var params = new Object;
			
			params.address = "scripts/admin_actions.php";
			params.data = 'sub_action=slogan&slogan=' + encodeURIComponent(newSlogan);
			params.success = function() { alert(responseText); };
			
			_.ajax(params);
		}
	},
	
	backupDB: function() {
        var params = new Object;
		
		params.address = "scripts/admin_actions.php";
		params.data = 'sub_action=backupdb';
		params.success = function() { alert(responseText); };
		
		_.ajax(params);
	},
	
	saveDefaultTheme: function() {
        var newTheme = document.getElementById('userTheme').value;
        
		var params = new Object;
		
		params.address = "scripts/admin_actions.php";
		params.data = 'sub_action=defaultTheme&theme=' + encodeURIComponent(newTheme);
		params.success = function() { alert(responseText); };
		
		_.ajax(params);
	},
	
	saveCheesto: function() {
        var cheesto = document.getElementById('cheesto_enabled').value;
        
		var params = new Object;
		
		params.address = "scripts/admin_actions.php";
		params.data = 'sub_action=cheesto&enabled=' + encodeURIComponent(cheesto);
		params.success = function() { alert(responseText); };
		
		_.ajax(params);
	},
};