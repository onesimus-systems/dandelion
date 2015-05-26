/// <reference path="../dts/jquery.d.ts" />
/// <reference path="../dts/common.d.ts" />
/* global $, window */

"use strict"; // jshint ignore:line

module Categories {
    var currentSelection: number[] = [];
    var domid: string = '';

    export function grabNextLevel(pid: string): void {
        var pidSplit = pid.split(':');
        var level = +pidSplit[0] + 1;
        var cid = +pidSplit[1];

        if (currentSelection[level]) {
            // This is to ensure that if a category is changed in the upper levels,
            // no residual children will remain
            currentSelection.splice(level);
        }

        currentSelection[level] = cid;

        $.get('render/categoriesJson', {pastSelection: JSON.stringify(currentSelection)}, null, 'json')
            .done(function(json) {
                $(domid).empty();
                $(domid).html(renderSelectsFromJson(json));
            });
    }

    export function grabFirstLevel(elemid: string): void {
        // Reset current selection
        currentSelection = [];
        domid = elemid;
        // Get root categories
        grabNextLevel('-1:0');
    }

    export function selectOnChange(elem: any): void {
        Categories.grabNextLevel(elem.value);
    }

    function renderSelectsFromJson(json: any): string {
        currentSelection = json.currentList;
        var span = $('<span/>');

        for (var key in json.levels) {
            if (!json.levels.hasOwnProperty(key))
                continue;

            var select = $('<select onChange="Categories.selectOnChange(this);">').attr('id', 'level'+key);
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

        return span.html();
    }

    export function renderCategoriesFromString(str: string, elemid: string): void {
        $.get('render/editcat', {catstring: str}, null, 'json')
            .done(function(json) {
                var rendered = renderSelectsFromJson(json);
                domid = elemid;

                if (!json.error) {
                    $(domid).html(rendered);
                } else {
                    rendered = "There was an error getting the category.<br><br>"+rendered;
                    $(domid).html(rendered);
                }
            });
    }

    export function createNew(): void {
        var catString = getCatString()+': ';
        var message = 'Add new category<br><br>';

        if (currentSelection.length == 1) {
            message = 'Create new root category:<br><br>';
            catString = '';
        }

        var dialog = message+catString+'<input type="text" id="new_category">';
        $.dialogBox(dialog, addNew, null, {title: 'Create new category', buttonText1: 'Create', height: 200, width: 500});
    }

    function addNew(): void {
        var newCatDesc = $('#new_category').val();
        var parent = currentSelection[currentSelection.length-1];

        if (newCatDesc) {
            $.post("api/i/categories/create", { pid: parent, description: newCatDesc }, null, 'json')
                .done(function( json ) {
                    $.alert(json.data, 'Categories');
                    getCatsAfterAction();
                });
        } else {
            $.alert('Please enter a category description.', 'Categories');
        }
    }

    export function editCat(): void {
        var cid = currentSelection[currentSelection.length-1];
        var lvl = currentSelection.length-2;

        var elt = $("#level"+lvl+" option:selected");

        if (typeof elt.val() !== 'undefined') {
            var editString = elt.text();

            var dialog = 'Edit Category Description:<br><br><input type="text" id="edited_category" value="'+editString+'">';
            $.dialogBox(dialog,
                function() {
                    var editedCat = $('#edited_category').val();
                    if (editedCat) {
                        $.post("api/i/categories/edit", { cid: cid, description: encodeURIComponent(editedCat) }, null, 'json')
                            .done(function( json ) {
                                $.alert(json.data, 'Categories');
                                getCatsAfterAction();
                            });
                    } else {
                        $.alert('Please enter a category description.', 'Categories');
                    }
                },
                null,
                {title: 'Edit category', buttonText1: 'Save', height: 200, width: 300}
            );
        }
    }

    export function deleteCat(): void {
        var myCatString = getCatString();
        var cid = currentSelection[currentSelection.length-1];

        if (myCatString !== '') {
            $.confirmBox('Delete "'+ myCatString +'"?\n\nChildren categories will be reassigned one level up',
                'Delete Category',
                function() {
                    $.post("api/i/categories/delete", { cid: cid }, null ,'json')
                    .done(function( json ) {
                        $.alert(json.data, 'Categories');
                        getCatsAfterAction();
                    });
                });
        }
    }

    function getCatsAfterAction(): void {
        if (currentSelection.length <= 2) {
            grabFirstLevel(domid);
        } else {
            grabNextLevel((currentSelection.length-3)+':'+currentSelection[currentSelection.length-2]);
        }
    }

    export function getCatString(): string {
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
        for (var i=0; i<currentSelection.length; i++) {
            if ($("#level"+(i))) {
                var optVal = i + ':' + currentSelection[i+1];
                var elt = $('#level'+(i)+' option[value=\''+optVal+'\']:first');
                catString += elt.text() + ':';
            }
        }

        if (catString.length > 0) {
            return catString.substring(0, catString.length - 2);
        } else {
            return '';
        }
    }
}
