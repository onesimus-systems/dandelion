/* global $ */

"use strict"; // jshint ignore:line

var userManager = {
	rights: {},
    currentuid: null,

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
            		$.alert(json.status, 'User Management');
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
	                    	<td><input type="radio" name="uid" value="' + user.userid + '"></td>\
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
        // Get the action to perform
		var action = $('#commands').val();
		$('#commands').prop("selectedIndex", 0);
		if (action === '') {
			return false;
		}

        // Make sure a user is selected
        var uid = $('input[name=uid]:checked').val();
        if (typeof uid == 'undefined' && action != 'add') {
            return false;
        }
        this.currentuid = uid;

        // Show dialog for action
		userManager.uiforms[action]();
		return;
	},

	add: function() { // Create user
		var username = $('#add_user').val();
        var password = $('#add_pass').val();
        var fullname = $('#add_fullname').val();
        var group = $('#add_group').val();

        $.post('api/i/users/create', {username: username, password: password, fullname: fullname, role: group}, null, 'json')
            .done(function(data) {
                userManager.loadUserList();
            });
        return;
	},

	edit: function() { // Edit user
        var uid = $('#edit_uid').val();
        var theme = $('#edit_theme').val();
        var fullname = $('#edit_fullname').val();
        var group = $('#edit_group').val();
        var prompt = $('#edit_prompt').val();

        $.post('api/i/users/save', {uid: uid, fullname: fullname, role: group, prompt: prompt, theme: theme}, null, 'json')
            .done(function(data) {
                userManager.loadUserList();
            });
        return;
	},

	delete: function() { // Delete user
        $.post('api/i/users/delete', {uid: userManager.currentuid}, null, 'json')
            .done(function(data) {
                if (data.errorcode === 0 && data.data === true) {
                    $.alert('User deleted', 'User Management');
                    userManager.loadUserList();
                } else {
                    $.alert('Error deleting user', 'User Management');
                }
            });
        return;
	},

	reset: function() { // Reset user password
        var pass1 = $('#pass1').val();
        var pass2 = $('#pass2').val();
        $("#pass1").val('');
        $("#pass2").val('');

        if (pass1 === pass2 && pass1 !== "") {
            $.post('api/i/users/resetPassword', {pw: pass1, uid: userManager.currentuid}, null, "json")
                .done(function(data) {
                    $.alert(data.data, 'User Management');
                });
        } else {
            $.alert('Passwords do not match or are empty', 'User Management');
        }
        return;
	},

	cxeesto: function() { // Change user status
        var uid = $('#status_uid').val();
        var status = $("select#status_text").prop("selectedIndex") - 1;
        var message = $('#status_message').val();
        var returntime = $('#status_return').val();

        $.post('api/i/cheesto/update', {uid: uid, status: status, returntime: returntime, message: message}, null, 'json')
            .done(function(data) {
                userManager.loadUserList();
            });
        return;
	},

	revokeKey: function() { // Revoke user's api key
		$.post('api/i/keymanager/revokekey', {uid: userManager.currentuid}, null, 'json')
            .done(function(data) {
                if (data.errorcode === 0 && data.data.key === true) {
                    $.alert('API key revoked', 'User Management');
                } else {
                    $.alert('Error revoking API key', 'User Management');
                }
            });
        return;
	},
};
