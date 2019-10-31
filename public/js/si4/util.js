si4.getArg = function(args, idx, defaultVal) {
    if (!args) return defaultVal;
    var result = args[idx];
    if (typeof(result) == "undefined") result = defaultVal;

    if (typeof(defaultVal) == "boolean") result = result ? true : false;
    else if (typeof(defaultVal) == "float") result = parseFloat(result);
    else if (typeof(defaultVal) == "integer") result = parseInt(result);
    else if (typeof(defaultVal) == "string") result = result ? result : defaultVal;

    return result;
};

si4.mergeObjects = function(obj1, obj2) {
    var result = {};

    if (typeof(obj1) != "undefined")
        for (var idx in obj1)
            result[idx] = obj1[idx];

    if (typeof(obj2) != "undefined")
        for (var idx in obj2)
            result[idx] = obj2[idx];

    return result;
};

si4.capitalize = function(strVal) {
    return strVal && typeof(strVal) == "string" ? strVal.substr(0, 1).toUpperCase() + strVal.substr(1) : "";
};

si4.captionize = function(strVal) {
    if (!strVal || typeof(strVal) != 'string') return '';
    var result = '';
    var lastChar = '';
    var nextUpper = true;
    for(var i = 0; i < strVal.length; i++) {
        var char = strVal[i];

        if (nextUpper) {
            char = char.toUpperCase();
            nextUpper = false;
        }

        if (char == '_') { char = ' '; nextUpper = true; }
        if (char >= 'A' && char <= 'Z' && lastChar != ' ') char = ' '+char;

        lastChar = char;
        result = result + char;
    }
    return result;
};

si4.clipString = function(dataString, maxLen) {
    if (!dataString || typeof(dataString) != "string") return "";
    if (dataString.length <= maxLen) return dataString;
    return dataString.substr(0, maxLen -3)+"...";
};

si4.moduleNameNormalize = function(moduleName) {
    var components = moduleName.split("/");
    for (var i in components)
        components[i] = components[i].slice(0,1).toLowerCase()+components[i].slice(1);
    var result = components.join("_");
    return result;
};


si4.mergePlaceholders = function(str, valueMapObj) {
    console.log("mergePlaceholders", str, valueMapObj);
    if (!str) return "";
    if (typeof(str) != "string") return "";

    if (typeof(valueMapObj) == "object") {
        for (var key in valueMapObj) {
            var searchRegEx = new RegExp('\\[\\['+key+'\\]\\]', 'ig');
            var replaceVal = undefined;
            if (typeof(valueMapObj[key]) == "string" || typeof(valueMapObj[key]) == "number") replaceVal = valueMapObj[key];
            else if (valueMapObj[key] && typeof(valueMapObj[key]) == "object" && Object.keys(valueMapObj).length) {
                replaceVal = "";
                for (var k in valueMapObj[key]){
                    if (replaceVal) replaceVal += ', ';
                    replaceVal += valueMapObj[key][k];
                }
            }
            if (replaceVal !== undefined) {
                str = str.replace(searchRegEx, replaceVal);
            }
        }
    }
    while (str.indexOf('[[') != -1) str = str.replace('[[', '[').replace(']]', '=?]');
    return str;
};

si4.replacePipes = function(value, newSeparator, depth) {
    if (!value) return value;
    if (typeof(newSeparator) == "undefined") newSeparator = ", ";
    if (typeof(depth) == "undefined") depth = 3;

    // If object given, duplicate, don't touch original
    if (typeof(value) == "object") value = si4.mergeObjects(value);

    return si4._replacePipes(value, newSeparator, depth);
};

si4._replacePipes = function(value, newSeparator, depth) {
    if (depth > 0 && typeof(value) == "object")
        for (var k in value)
            value[k] = si4._replacePipes(value[k], newSeparator, depth -1);

    if (typeof(value) == "string")
        value = value.replace(/\|\|/g, newSeparator);

    return value;
};

si4.splitPipes = function(value) {
    if (typeof(value) != "string") return value;
    return value.split("||");
};

si4.attachCopyToClipboard = function(sel, text, afterCopyF) {

    if (!afterCopyF) afterCopyF = function() {};

    sel.zclip({
        path: 'lib/jquery/ZeroClipboard.swf?noCache'+Math.random(),
        copy: text,
        afterCopy: afterCopyF
    });

};


si4.elasticReplaceMap = {" ": "+"};
si4.stripElasticSpecialChars = function(text) {
    // Strip Elastic chars
    for (var sourceChar in si4.elasticReplaceMap) {
        var targetChar = si4.elasticReplaceMap[sourceChar];
        var searchRegEx = new RegExp(sourceChar, 'g');
        text = text.replace(searchRegEx, targetChar);
    }

    if (text.length > 300)
        text = text.substring(0, 300);

    //text = "+"+text+"+";

    return text;
};

si4.findKeyByValue = function(dict, value) {
    if (typeof(dict) != "object") return 0;
    for (var i in dict) {
        if (dict[i] == value) return i;
    }
    return 0;
};



si4.dateToISOString = function(date) {
    function pad(number) {
        if (number < 10) {return '0' + number; }
        return number;
    }
    if (!date) date = new Date();
    return date.getUTCFullYear() +
    '-' + pad(date.getUTCMonth() + 1) +
    '-' + pad(date.getUTCDate()) +
    'T' + pad(date.getUTCHours()) +
    ':' + pad(date.getUTCMinutes()) +
    ':' + pad(date.getUTCSeconds()) +
    'Z';
};


si4.apiUrlFromName = function(apiName) {
    if (!apiName) return "";
    return apiName.replace(/\.?([A-Z])/g, function (x,y){ return "-" + y.toLowerCase()}).replace(/^_/, "");
};

// *** Url ***

si4.queryStringToJson = function(queryStr) {
    if (!queryStr || typeof(queryStr) !== "string") return {};
    queryStr = queryStr.replace("?", "");
    var paramsSplit = queryStr.split("&");
    var result = {};
    for (var i = 0; i < paramsSplit.length; i++) {
        var paramKeyVal = paramsSplit[i].split("=");
        if (paramKeyVal.length !== 2) continue;
        result[paramKeyVal[0]] = decodeURIComponent(paramKeyVal[1]);
    }
    return result;
};

si4.jsonToQueryString = function(json) {
    if (!json) return "";
    if (!Object.keys(json).length) return "";
    var routeParamsArr = [];
    for (var key in json) routeParamsArr.push(key+"="+encodeURIComponent(json[key]));
    return "?"+routeParamsArr.join("&");
};

