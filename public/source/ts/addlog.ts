/// <reference path="../dts/ckeditor.d.ts" />
import Categories from 'categories';
import "common";

$(document).ready(function(): void {
    Categories.grabFirstLevel("#categories");

    CKEDITOR.replace("body");
    $("#loading").hide();

    $("#edit-form").submit(function(): boolean {
        $("<input/>").attr("type", "hidden")
            .attr("name", "catstring")
            .attr("value", Categories.getCatString())
            .appendTo(this);
        return true;
    });
});
