import Categories from '../modules/categories';
import "../modules/common";

function init() {
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
}

function renderCategories(): void {
    Categories.setDomID("#categories");
    const json: string = $("#category-json").val();
    const rendered = Categories.renderSelectsFromJson(JSON.parse(json));
    $("#categories").replaceWith(rendered);
}

init();
