if (!si4.translations) si4.translations = {};

si4.hasTranslate = function(key) {
    var result = typeof(si4.translations[key]) != "undefined";
    if (!result) console.log("Missing translation", key);
    return result;
};

si4.translate = function(key, replaceMap) {
    var result = key;
    if (si4.hasTranslate(key))
        result = si4.translations[key];
    if (replaceMap) {
        for (var replaceKey in replaceMap) {
            result = result.replace(new RegExp("\\[\\["+replaceKey+"\\]\\]", "gi"), replaceMap[replaceKey]);
        }
    }
    return result;
};

si4.translateTab = function(moduleName, name) {
    var key = si4.moduleNameNormalize(moduleName)+"_"+name+"_text";
    //console.log("translateTab", moduleName, name, "->", key);
    return si4.translate(key);
};

