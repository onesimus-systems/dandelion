$(function(){
    api.getApiKey(); 
});

var api = {
    getApiKey: function() {
        $.getJSON("iapi/keyManager/getKey",
            function(data) {
                $("#apiKey").html(data['data']['key']);
        });
    },
    
    generateKey: function() {
        $.getJSON("iapi/keyManager/newKey",
            function(data) {
                $("#apiKey").html(data['data']['key']);
        });
    }
}