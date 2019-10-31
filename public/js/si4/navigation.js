si4.si4Navigation = function(args)
{
    // Init
    var _p = this;

    // Implementation
    this.switchPage = function(pageName, pageArgs) {
        var url = "/" +pageName+ si4.jsonToQueryString(pageArgs);
        window.location = url;
    };

    this.back = function() {
        window.history.back();
    };

    this.init = function() {
        var pageName = window.location.pathname.replace("/", "");
        if (!pageName) pageName = "index";
        var params = si4.queryStringToJson(window.location.search);
        //console.log("renderPage", pageName, params);
        if (typeof(si4.modules[pageName]) == "function") {
            si4.modules[pageName](params);
        }
    };

    this.init();

    return this;
};
