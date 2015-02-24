/* global $, window, console */

"use strict"; // jshint ignore:line

var CategoryManage = {
	currentSelection: [],
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
				}
			});
	},

	grabFirstLevel: function() {
		// Reset current selection
		CategoryManage.currentSelection = [];
		// Get root categories
		CategoryManage.grabNextLevel('-1:0');
	},

	renderSelectsFromJson: function(json) {
		CategoryManage.currentSelection = json.currentList;
		var span = $('<span/>');

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
			span.append(select);
		}

		return span;
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
				$.alert('Please enter a category description', 'Categories');
			} else if (newCatDesc === null) {
				return false;
			} else {
				this.addNew(encodeURIComponent(newCatDesc));
				break;
			}
		}
	},

	addNew: function(description) {
		var newCatDesc = description;
		var parent = this.currentSelection[this.currentSelection.length-1];

		$.post("api/i/categories/add", { pid: parent, description: newCatDesc }, null, 'json')
            .done(function( json ) {
                $.alert(json.data, 'Categories');
                CategoryManage.getCatsAfterAction();
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
                $.post("api/i/categories/edit", { cid: cid, description: encodeURIComponent(editedCat) }, null, 'json')
                    .done(function( json ) {
                        $.alert(json.data, 'Categories');
                        CategoryManage.getCatsAfterAction();
                    });
            }
		}
	},

	deleteCat: function() {
		var myCatString = this.getCatString();
		var cid = this.currentSelection[this.currentSelection.length-1];

		if (!window.confirm('Delete "'+ myCatString +'"?\n\nChildren categories will be reassigned one level up')) {
			return false;
		}

        $.post("api/i/categories/delete", { cid: cid }, null ,'json')
            .done(function( json ) {
                $.alert(json.data, 'Categories');
                CategoryManage.getCatsAfterAction();
            });
	},

	getCatsAfterAction: function() {
        if (CategoryManage.currentSelection.length <= 2) {
        	CategoryManage.grabFirstLevel();
        } else {
			CategoryManage.grabNextLevel((CategoryManage.currentSelection.length-3)+':'+CategoryManage.currentSelection[CategoryManage.currentSelection.length-2]);
		}
	},

	getCatString: function() {
		var catString = '';
		/*
		 * Note to future self: The jQuery statement below is messier than I would like. Here's the reason.
		 * For some reason, jQuery doesn't recognize that the first select in a category has a selected option.
		 * thus it will only return the strings from levels 1 up instead of 0 up. I also tried to select using only
		 * the value. However jQuery then thought that there were two options with the same value despite there only
		 * being one option with a value at all. Thus, the below query to select the id of levelX getting the first
		 * option with the value of level : ID.
		 *
		 * Cause: Somehow, there's a difference between the way categories are handled when adding a new log vs editing
		 * and existing log. However they use the same rendering function and use the same jQuery function to display
		 * the select elements. What's weirder, is according to the dev console, the option's selected attribute is
		 * applied appropiatly, and yet jQuery doesn't see it. I don't know. The below statement works. So that's nice.
		 */
		for (var i=0; i<this.currentSelection.length; i++) {
			if ($("#level"+(i))) {
				var optVal = i + ':' + this.currentSelection[i+1];
				var elt = $('#level'+(i)+' option[value=\''+optVal+'\']:first');
				catString += elt.text() + ':';
			}
		}

		if (catString.length > 0) {
			return catString.substring(0, catString.length - 2);
		} else {
			return '';
		}
	},
};
