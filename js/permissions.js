var permissions = {
    // All permissions to reference for iteration
    allPermissions: {
      'createlog': false,
      'editlog': false,
      'viewlog': false,
      
      'addcat': false,
      'editcat': false,
      'deletecat': false,
      
      'adduser': false,
      'edituser': false,
      'deleteuser': false,
      
      'addgroup': false,
      'editgroup': false,
      'deletegroup': false,
      
      'viewcheesto': false,
      'updatecheesto': false,
      
      'admin': false,
    },
    
    currentPermissions: {
    },
    
    getList: function() {
        $.ajax({
          url: "scripts/editgroups.php",
          data: { action: "getlist" }
        })
          .done(function( msg ) {
            $("#groups").html(msg);
          });
    },
    
    getPermissions: function(group) {
        $.ajax({
            url: "scripts/editgroups.php",
            data: { action: "getpermissions", groups: group }
        })
          .done(function( html ) {
              permissions.showPermissions(html);
          });
    },
    
    showPermissions: function(json) {
        var permissionsObject = JSON.parse(json);
        this.currentPermissions = permissionsObject;
        
        this.drawGrid(permissionsObject);
        
        $("#permissionsBlock").css( "display", "block" );
        
        return true;
    },
    
    savePermissions: function() {
        // Copy allPermissions
        var newPermissions = this.getGrid();
        var group = $("#groupList")[0].value;
        
        if (group !== 0) {
			var newPermissionsJson = JSON.stringify(newPermissions);

			$.ajax({
				type: "POST",
				url: "scripts/editgroups.php",
				data: { action: "save", permissions: newPermissionsJson, gid: group }
			})
				.done(function( msg ) {
					alert(msg);
					$("#permissionsForm")[0].reset();
					$("#permissionsBlock").css( "display", "none" );
					$("#groupList")[0].value = 0;
				});
        }
    },
    
    checkGrid: function(permission) {
		var newGrid = this.allPermissions;
		var theID = "#"+permission;
		var changed;
		
		if (permission == 'admin') {
			if ($(theID)[0].checked) {
				// An admin user has full permissions, basically a select all
				for (var key in newGrid) {
					if (newGrid.hasOwnProperty(key)) {
						newGrid[key] = true;
					}
				}
				
				this.drawGrid(newGrid);
			}
			else {
				// Revert to original permissions grid
				changed = this.currentPermissions;
				changed[permission] = false;
				this.drawGrid(changed);
			}
		}
		
		else if (permission == 'createlog' || permission == 'editlog') {
			// Creating and editing a log inherently means the user can view logs
			if ($(theID)[0].checked) {
				changed = this.getGrid();
				changed.viewlog = true;
				changed[permission] = true;
				this.drawGrid(changed);
			}
		}
		
		else if (permission == 'updatecheesto') {
			if ($(theID)[0].checked) {
				changed = this.getGrid();
				changed.viewcheesto = true;
				changed[permission] = true;
				this.drawGrid(changed);
			}
		}
    },
    
    goBack: function() {
		this.drawGrid(this.currentPermissions);
    },
    
    drawGrid: function(object) {
		for (var key in object) {
           if (object.hasOwnProperty(key)) {
              var getID = "#"+key;

              $(getID)[0].checked = object[key];
           }
        }
        
        return true;
    },
    
    getGrid: function() {
		var theGrid = JSON.parse(JSON.stringify(this.allPermissions));

		for (var key in theGrid) {
			if (theGrid.hasOwnProperty(key)) {
				var getID = "#"+key;

				theGrid[key] = $(getID)[0].checked;
			}
		}
		
		return theGrid;
    }
};