$(function(){
    api.getApiKey(); 
});

var api = {
    getApiKey: function() {
        $.getJSON("api/keyManager/getKey",
            function(data) {
                $("#apiKey").html(data['key']);
        });
    },
    
    generateKey: function() {
        $.getJSON("api/keyManager/newKey",
            function(data) {
                $("#apiKey").html(data['key']);
        });
    }
}