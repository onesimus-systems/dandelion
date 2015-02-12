/* global $, window, alert */

"use strict"; // jshint ignore:line

var CategoryManage = {
	currentID: -1,
	currentSelection: [0],
	addEditLog: false,

	grabNextLevel: function(pid) {
		var container = (CategoryManage.addEditLog) ? '#catSpace' : '#categorySelects';

		var pidSplit = pid.split(':');
		var level = +pidSplit[0] + 1;
		var cid = +pidSplit[1];

		if (CategoryManage.currentSelection[level]) {
			// This is to ensure that if a category is changed in the upper levels,
			// no residual children will remain
			CategoryManage.currentSelection.splice(level);
		}

		CategoryManage.currentSelection[level] = cid;

		$.get('render/categoriesJson', {pastSelection: JSON.stringify(CategoryManage.currentSelection)}, null, 'json')
			.done(function(json) {
				if (typeof $(container)[0] !== 'undefined') {
					$(container).empty();
					$(container).html(CategoryManage.renderSelectsFromJson(json));
					CategoryManage.currentID = pid;
				}
			});
	},

	grabFirstLevel: function() {
		// Reset current selection
		CategoryManage.currentSelection = [0];
		// Get root categories
		CategoryManage.grabNextLevel('-1:0');
	},

	renderSelectsFromJson: function(json) {
		CategoryManage.currentSelection = json.currentList;
		var div = $('<span/>');

		var onChangeFunc = function() {
			CategoryManage.grabNextLevel(this.value);
		};

		for (var key in json.levels) {
			if (!json.levels.hasOwnProperty(key))
				continue;

			var select = $('<select/>').attr('id', 'level'+key);
			select.change(onChangeFunc);
			select.append('<option value="">Select:</option>');

			for (var category in json.levels[key]) {
				if (!json.levels[key].hasOwnProperty(category))
					continue;

				var cat = json.levels[key][category];
				var option = $('<option value="'+key+':'+cat.id+'">'+cat.desc+'</option>');
				option.attr('selected', cat.selected);
				select.append(option);
			}
			div.append(select);
		}

		return div;
	},

	renderCategoriesFromString: function(str, callback) {
		$.get('render/editcat', {catstring: str}, null, 'json')
			.done(function(json) {
				var rendered = CategoryManage.renderSelectsFromJson(json);
				callback(rendered);
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
			} else if (newCatDesc === null) {
				return false;
			} else {
				this.addNew(encodeURIComponent(newCatDesc));
				break;
			}
		}
	},

	addNew: function(catDesc) {
		var newCatDesc = catDesc;
		var parent = this.currentSelection[this.currentSelection.length-1];

		$.post("api/i/categories/add", { parentID: parent, catDesc: newCatDesc }, null, 'json')
            .done(function( json ) {
                alert(json.data);
				CategoryManage.grabNextLevel(CategoryManage.currentSelection[CategoryManage.currentSelection.length-2]);
            });
	},

	editCat: function() {
		var cid = this.currentSelection[this.currentSelection.length-1];
		var lvl = this.currentSelection.length-2;

		var elt = $("#level"+lvl+" option:selected");

		if (typeof elt.val() !== 'undefined') {
			var editString = elt.text();
            var editedCat = window.prompt("Edit Category Description:",editString);

            if (editedCat !== null && editedCat !== '') {
                $.post("api/i/categories/edit", { cid: cid, catDesc: encodeURIComponent(editedCat) }, null, 'json')
                    .done(function( json ) {
                        alert(json.data);
                        CategoryManage.grabNextLevel(CategoryManage.currentSelection[CategoryManage.currentSelection.length-2]);
                    });
            }
		}
	},

	deleteCat: function() {
		var myCatString = this.getCatString();
		var cid = this.currentSelection[this.currentSelection.length-1];

		if (!window.confirm('Delete '+ myCatString +'?\n\nWarning: All child categories will be moved up one level!')) {
			return false;
		}

        $.post("api/i/categories/delete", { cid: cid }, null ,'json')
            .done(function( json ) {
                alert(json.data);
                CategoryManage.grabNextLevel(CategoryManage.currentSelection[CategoryManage.currentSelection.length-2]);
            });
	},

	getCatString: function() {
		var catString = '';

		for (var i=0; i<this.currentSelection.length; i++) {
			if ($("#level"+(i))) {
				var elt = $("#level"+(i)+" option:selected");

				if (elt.text() != 'Select:') {
					catString += elt.text() + ':';
				}
			}
		}

		if (catString.length > 0) {
			return catString.substring(0, catString.length - 1);
		} else {
			return false;
		}
	},
};
