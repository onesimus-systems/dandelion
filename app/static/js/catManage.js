/* global $, window, alert */

"use strict"; // jshint ignore:line

var CategoryManage = {
	currentID: -1,
	currentSelection: [],
	addLog: false,
	
	grabNextLevel: function(parentID, container) {
	    var pid;
		if (parentID == "0:0") { pid = "0:0"; }
		else if (parentID.value) { pid = parentID.value; }
		else { pid = parentID; }
		
		container = (this.addLog) ? '#catSpace' : '#categorySelects';
		
		var level = pid.split(':');
		
		if (this.currentSelection[level[1]]) {
			this.currentSelection.splice(level[1]);
		}
		
		this.currentSelection[level[1]] = pid;
		
        if (this.currentSelection.length === 0) {
			this.currentSelection[0] = '0:0';
		}
		
		$.ajax({
            type: "POST",
            url: "lib/categories.php",
            data: { action: "grabcats", pastSelections: JSON.stringify(this.currentSelection)},
            async: false
        })
            .done(function( html ) {
                if (typeof $("#categorySelects")[0] !== 'undefined') {
                    $("#categorySelects").html("");
                    $(container).html( html );
                    CategoryManage.currentID = pid;
                    
                    for (var i=1; i<CategoryManage.currentSelection.length; i++) {
                        $("#level"+i).val( CategoryManage.currentSelection[i] );
                    }
                }
            });
	},
	
	createNew: function() {
		var catString = this.getCatString();
		var message = 'Add new category\n\n';
		
		if (this.currentSelection.length == 1) {
			message = 'Create new root category:';
			catString = '';
		}
		
		while (true) {
			var newCatDesc = window.prompt(message+catString);
			
			if (newCatDesc === '') {
				alert('Please enter a category description');
			}
			else if (newCatDesc === null) {
				return false;
			}
			else {
				this.addNew(encodeURIComponent(newCatDesc));
				break;
			}
		}
	},
	
	addNew: function(catDesc) {
		var newCatDesc = catDesc;
		var parent = this.currentSelection[this.currentSelection.length-1].split(':');
		parent = parent[0];
		
		$.post("lib/categories.php", { action: "addcat", parentID: parent, catDesc: newCatDesc })
            .done(function( html ) {
                alert( html );
				CategoryManage.grabNextLevel(CategoryManage.currentSelection[CategoryManage.currentSelection.length-2]);
            });
	},
	
	editCat: function() {
		var cid = this.currentSelection[this.currentSelection.length-1].split(':');
		
		var elt = $("#level"+cid[1]+" option:selected");

		if (typeof elt.val() !== 'undefined') {
			var editString = elt.text();
            var editedCat = window.prompt("Edit Category Description:",editString);

            if (editedCat !== null && editedCat !== '') {
                $.post("lib/categories.php", { action: "editcat", cid: cid[0], catDesc: encodeURIComponent(editedCat) })
                    .done(function( html ) {
                        alert( html );
                        CategoryManage.grabNextLevel(CategoryManage.currentSelection[CategoryManage.currentSelection.length-2]);
                    });
            }
		}
	},
	
	deleteCat: function() {
		var myCatString = this.getCatString();
		var cid = this.currentSelection[this.currentSelection.length-1].split(':');
		cid = cid[0];
	
		if (!window.confirm('Delete '+ myCatString +'?\n\nWarning: All child categories will be moved up one level!')) {
			return false;
		}
		
        $.post("lib/categories.php", { action: "delcat", cid: cid })
            .done(function( html ) {
                alert( html );
                CategoryManage.grabNextLevel(CategoryManage.currentSelection[CategoryManage.currentSelection.length-2]);
            });
	},
	
	getCatString: function() {
		var catString = '';
		
		for (var i=0; i<this.currentSelection.length; i++) {
			if ($("#level"+(i+1))) {
				var elt = $("#level"+(i+1)+" option:selected");

				if (elt.text() != 'Select:') {
					catString += elt.text() + ':';
				}
			}
		}
		
		if (catString.length > 0) {
			return catString.substring(0, catString.length - 1); 
		}
		else {
			return false;
		}
	},
};