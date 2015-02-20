/* global $ */

"use strict"; // jshint ignore:line

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
        $.get('api/i/rights/getlist', {}, null, 'json')
          .done(function(json) {
            // Build select dropdown of current user groups
            var select = $('<select/>').attr('id', 'groupList');
            select.append('<option value="0">Select:</option>');

            for (var key in json.data) {
                if (!json.data.hasOwnProperty(key))
                    continue;

                var group = json.data[key];
                select.append('<option value="' + group.id + '">' + group.role + '</option>');
            }

            $('#groups').empty();
            $('#groups').append(select);
          });
    },

    getPermissions: function(gid) {
        var group = typeof gid !== 'undefined' ? gid : $("#groupList")[0].value;

        $.get('api/i/rights/getgroup', {groupid: group}, null, 'json')
            .done(function(json) {
                permissions.showPermissions(json.data);
            });
    },

    showPermissions: function(json) {
        this.currentPermissions = json;

        this.drawGrid(json);

        $("#permissionsBlock").css( "display", "block" );

        return true;
    },

    savePermissions: function() {
        // Copy allPermissions
        var newPermissions = this.getGrid();
        var group = $("#groupList").val();

        if (group !== 0) {
			var newPermissionsJson = JSON.stringify(newPermissions);

			$.post('api/i/rights/save', { rights: newPermissionsJson, groupid: group }, null, 'json')
				.done(function( json ) {
					$.alert(json.data, 'User Groups');
					$("#permissionsForm")[0].reset();
					$("#permissionsBlock").css( "display", "none" );
					$("#groupList").val( 0 );
				});
        }
    },

    checkGrid: function(permission) {
		var newGrid = this.allPermissions;
		var theID = "#"+permission;
		var changed;

		if (permission == 'admin') {
			if ($(theID).is( ":checked" )) {
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
			if ($(theID).is( ":checked" )) {
				changed = this.getGrid();
				changed.viewlog = true;
				changed[permission] = true;
				this.drawGrid(changed);
			}
		}

		else if (permission == 'updatecheesto') {
			if ($(theID).is( ":checked" )) {
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

    createNew: function() {
        $( "#add-form" ).dialog({
          height: 225,
          width: 350,
          modal: true,
          buttons: {
            "Create Group": function() {
                permissions.sendNew();
                $( this ).dialog( "close" );
            },
            Cancel: function() {
              $( this ).dialog( "close" );
            }
          }
        });
    },

    sendNew: function() {
        $.post('api/i/rights/create',
			{ name: $("#name").val(), rights: JSON.stringify(permissions.allPermissions) }, null, 'json')
          .done(function(response) {
              $( "#dialogBox" ).html( response.data );
              $( "#dialogBox" ).dialog({
                  modal: true,
                  width: 400,
                  show: {
                    effect: "fade",
                    duration: 500
                  },
                  hide: {
                    effect: "fade",
                    duration: 500
                  },
                  buttons: {
                    Ok: function() {
                      $( this ).dialog( "close" );
                    }
                  }
                });

                $("#permissionsForm")[0].reset();
                $("#permissionsBlock").css( "display", "none" );
                permissions.getList();
          });
    },

    deleteGroup: function() {
        var delGroup = $("#groupList option:selected").text();

        $( "#dialogBox" ).html( "<p>Do you really want to delete the group '"+delGroup+"'?</p>" );
        $( "#dialogBox" ).dialog({
          resizable: false,
          modal: true,
          buttons: {
            "Delete Group": function() {
                $( this ).dialog( "close" );

                var group = $("#groupList").val();

                $.post('api/i/rights/delete', { groupid: group }, null, 'json')
                  .done(function( json ) {

                      $( "#dialogBox" ).html( "<p>"+json.data+"</p>" );
                      $( "#dialogBox" ).dialog({
                          modal: true,
                          width: 400,
                          show: {
                            effect: "fade",
                            duration: 500
                          },
                          hide: {
                            effect: "fade",
                            duration: 500
                          },
                          buttons: {
                            Ok: function() {
                              $( this ).dialog( "close" );
                            }
                          }
                        });

						$("#permissionsForm")[0].reset();
						$("#permissionsBlock").css( "display", "none" );
                        permissions.getList();
                  });
            },
            Cancel: function() {
              $( this ).dialog( "close" );
            }
          }
        });
    },

    drawGrid: function(object) {
		for (var key in object) {
           if (object.hasOwnProperty(key)) {
              var getID = "#"+key;

              $(getID).prop( "checked", object[key] );
           }
        }

        return true;
    },

    getGrid: function() {
		var theGrid = JSON.parse(JSON.stringify(this.allPermissions));

		for (var key in theGrid) {
			if (theGrid.hasOwnProperty(key)) {
				var getID = "#"+key;

				theGrid[key] = $(getID).prop( "checked" );
			}
		}

		return theGrid;
    },

    // Checks if enter key was pressed, if so submit the form
    check: function(e) {
        if (e.keyCode == 13) {
            $( "#add-form" ).dialog( "close" );
            this.sendNew();
            e.preventDefault();
        }
    },
};
