/// <reference path="../dts/jquery.d.ts" />
/// <reference path="../dts/common.d.ts" />
/// <reference path="../dts/ckeditor.d.ts" />
/// <reference path="categories.ts" />

"use strict"; // jshint ignore:line

$(document).ready(function(): void {
    Categories.setUrlPrefix("../");
    Categories.grabFirstLevel("#categories");

    CKEDITOR.replace("body");
    $("#loading").hide();

    $("#edit-form").submit(function(event) {
        $("<input />").attr("type", "hidden")
            .attr("name", "catstring")
            .attr("value", Categories.getCatString())
            .appendTo(this);
        return true;
    });
});
