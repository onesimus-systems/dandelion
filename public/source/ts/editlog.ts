/// <reference path="../dts/jquery.d.ts" />
/// <reference path="../dts/common.d.ts" />
/// <reference path="../dts/ckeditor.d.ts" />
/// <reference path="categories.ts" />

"use strict"; // jshint ignore:line

$(document).ready(function (): void {
    renderCategories();

    CKEDITOR.replace("body");
    $("#loading").hide();

    $("#edit-form").submit(function (event) {
        $("<input />").attr("type", "hidden")
            .attr("name", "catstring")
            .attr("value", Categories.getCatString())
            .appendTo(this);
        return true;
    });
});

function renderCategories(): void {
    Categories.setUrlPrefix("../../");
    Categories.setDomID("#categories");
    var json: string = $("#category-json").val();
    var rendered = Categories.renderSelectsFromJson(JSON.parse(json));
    $("#categories").html(rendered);
}
