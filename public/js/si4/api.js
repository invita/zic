si4.api = {};

for (var apiName in si4.config.apis) {
    var createF = function(apiName) {
        si4.api[apiName] = function(data, callback) {
            var apiUrl = si4.config.apis[apiName];
            $.post(apiUrl+"?_="+Math.random(), JSON.stringify(data), function(resp) {
                //console.log(apiName+" callback", resp);
                if (typeof(callback) == "function") callback(resp);
            });
        };
    }
    createF(apiName);
}
