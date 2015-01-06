/* global $, alert, console */

"use strict"; // jshint ignore:line

var userManager = {
	rights: {},

	init: function() {
		this.getUsersRights();
		this.loadCommandList();
		this.loadUserList();
	},

	getUsersRights: function() {
        $.ajax('api/i/rights/getUsersRights', {
        	async: false, // To ensure permissions are loaded before command list is generated
        	dataType: 'json'
        })
            .done(function(json) {
            	if (json.errorcode === 0) {
					userManager.rights = json.data;
            	} else {
            		alert(json.status);
            	}
            	return;
            });
        return;
	},

	loadCommandList: function() {
		var commands = $('<select/>').attr('id', 'commands');

		commands.append('<option value="">Select Command:</option>');

	    if (this.rights.adduser) {
            commands.append('<option value="add">Add User</option>');
        }
        
        if (this.rights.deleteuser) {
            commands.append('<option value="delete">Delete</option>');
        }
        
        if (this.rights.edituser) {
            commands.append('<option value="edit">Edit</option>');
            commands.append('<option value="reset">Reset Password</option>');
            commands.append('<option value="cxeesto">Change &#264;eesto</option>');
            commands.append('<option value="revokeKey">Revoke API Key</option>');
        }

        $('#userManageForm').prepend(commands);
        return;
	},

	loadUserList: function() {
		$.getJSON("api/i/users/getUsersList",
                    function(data){
                    	if (data.errorcode === 0) {
                    		userManager.displayUserList(data.data);
                    	} else {
                    		$('#userlist').html(data.status);
                    	}
                    	return;
                	});
		return;
	},

	displayUserList: function(userdata) {
		var userTable = $('<table/>');
        // jshint multistr:true
		var header = '<tr>\
		                <th>&nbsp;</th>\
		                <th>Real Name</th>\
		                <th>Username</th>\
		                <th>Role</th>\
		                <th>Date Created</th>\
		                <th>Theme</th>\
		                <th>First Login</th>\
		            </tr>';
        userTable.append(header);

        for (var key in userdata) {
            if (!userdata.hasOwnProperty(key))
                continue;

			var user = userdata[key];
            var html = '<tr>\
	                    	<td><input type="radio" name="the_choosen_one" value="' + user.userid + '"></td>\
	                    	<td style="text-align: left;">' + user.realname + '</td>\
	                    	<td>' + user.username + '</td>\
	                    	<td>' + user.role + '</td>\
	                    	<td>' + user.datecreated + '</td>\
	                    	<td>' + user.theme + '</td>\
	                    	<td>' + user.firsttime + '</td>\
                    	</tr>';
        	userTable.append(html);
        }

		$('#userlist').empty();
        $('#userlist').append(userTable);
        return;
	},

	performAction: function() {
		var action = $('#commands').val();
		$('#commands').prop("selectedIndex", 0);

		if (action === '') {
			return false;
		}
		userManager[action]();
		return;
	},

	add: function() { // Create user
		console.log('I add a user');
	},

	edit: function() { // Edit user
		console.log('I edit a user');
	},

	delete: function() { // Delete user
		console.log('I delete a user');
	},

	reset: function() { // Reset user password
		console.log('I reset a user');
	},

	cxeesto: function() { // Change user status
		console.log('I cxeesto a user');
	},

	revokeKey: function() { // Revoke user's api key
		console.log('I revokeKey a user');
	},
};
