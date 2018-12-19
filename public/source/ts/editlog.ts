import Categories from 'categories';

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
    $("#categories").replaceWith(rendered);
}
