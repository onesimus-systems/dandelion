/// <reference path="../dts/ckeditor.d.ts" />
import Categories from 'categories';

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
