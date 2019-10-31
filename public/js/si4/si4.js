var si4 = { object:{}, widget:{}, data:{}, defaults:{}, modules:{} };

si4.defaults = {
    fadeTime: 600,
    loadingFadeTime: 200,
    hintFadeTime: 200,
    hintTriggerDelay: 200,
    autoCompleteDelay: 500,

    buttonGrad: "gray",
    submitGrad: "red",
    tabActiveGrad: "red",
    tabInactiveGrad: "brown",

    dataTableRowsPerPage: 10
}


si4.loadModule = function(loadArgs) {

    si4.loading.show();

    console.log("si4.loadModule", loadArgs);

    var moduleName = si4.getArg(loadArgs, "moduleName", null); // Module Name
    var newTab = si4.getArg(loadArgs, "newTab", null); // new TabPage Name string

    loadArgs.createMainTab = function(name) {
        name = si4.translateTab(moduleName, name ? name : "mainTab");
        loadArgs.mainTab = new si4.widget.si4TabPage({
            name: si4.mergePlaceholders(name, loadArgs.row),
            parent: loadArgs.tabPage ? loadArgs.tabPage : si4.data.mainTab,
        });

        if (loadArgs.onClose && typeof(loadArgs.onClose) == "function") loadArgs.mainTab.onClose(loadArgs.onClose);
        if (loadArgs.onClosed && typeof(loadArgs.onClosed) == "function") loadArgs.mainTab.onClosed(loadArgs.onClosed);

        return loadArgs.mainTab;
    };
    loadArgs.createContentTab = function(name, args) {
        name = si4.translateTab(moduleName, name ? name : "contentTab");
        loadArgs.contentTab = new si4.widget.si4TabPage(si4.mergeObjects({
            name: name,
            parent: loadArgs.contentTab ? loadArgs.contentTab : loadArgs.mainTab.content.selector,
            autoActive: !loadArgs.contentTab,
        }, args));
        return loadArgs.contentTab;
    };

    $.get(si4.config.modulePath+moduleName+".js", function(data) {
        eval(data);
        if (F && typeof(F) == "function") F(loadArgs);
    });

    si4.loading.hide();
};


si4.callMethod = function(args, f) {

};


si4.error = {
    ERR_API_STATUS_FALSE: "ERR_API_STATUS_FALSE",

    show: function(text, code, context) {
        var codeStr = code ? "["+code+"]" : "";
        alert("Error "+codeStr+"\n"+text);
        console.log("Error context", context);
    }
};

// Loading Animation
si4.loading = {
    isVisible: false,
    show: function(){
        //$('img#loadingGif').stop().css("display", "");
        $('img#loadingGif').stop().fadeIn(si4.defaults.loadingFadeTime);
        $('img#loadingGif2').stop().fadeIn(si4.defaults.loadingFadeTime);
        si4.loading.isVisible = true;
        si4.mouse.loadingMove();
    },
    hide: function(){
        //$('img#loadingGif').stop().css("display", "none");
        $('img#loadingGif').stop().fadeOut(si4.defaults.loadingFadeTime);
        $('img#loadingGif2').stop().fadeOut(si4.defaults.loadingFadeTime);
        si4.loading.isVisible = false;
    }
};

// Mouse Movement
si4.mouse = { x: 0, y: 0 };
$(document).mousemove(function(e) {
    si4.mouse.x = e.pageX;
    si4.mouse.y = e.pageY;
    if (si4.loading.isVisible)
        si4.mouse.loadingMove();
});

// Move loadingGif2 with cursor
si4.mouse.loadingMove = function() {
    if (!si4.mouse.loadingGif2)
        si4.mouse.loadingGif2 = $("img#loadingGif2");
    si4.mouse.loadingGif2.css("left", (si4.mouse.x+5)+"px").css("top", (si4.mouse.y+5)+"px");
};


