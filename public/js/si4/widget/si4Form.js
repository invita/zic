si4.widget.si4Form = function(args)
{
    // Init
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons({ parent:args.parent, tagName:"div" });

    this._eventb = si4.object.si4EventBase;
    this._eventb();

    this.selector.addClass("si4Form");

    this.inputs = {};
    this._submitInput = null;
    this.lastCaptionHeader = null;
    this.lastCaptionContent = null;

    // Settings
    this.enterSubmits = si4.getArg(args, "enterSubmits", true);
    this.showModified = si4.getArg(args, "showModified", true);
    this.captionWidth = si4.getArg(args, "captionWidth", null);
    this.showCopyPaste = si4.getArg(args, "showCopyPaste", false);
    this.skipTypes = ["submit", "button"];
    this.inputClass = si4.getArg(args, "inputClass", "");

    // Events
    this.onSubmit = function(f) { _p.subscribe("onSubmit", f); };

    // Implementation

    if (this.showCopyPaste) {
        this.actionDiv = new si4.widget.si4Element({parent:this.selector, tagClass:"actionBar"});

        this.copyButton = new si4.widget.si4Element({parent:this.actionDiv.selector, tagClass:"actionButton pasteButton",
            tagName:"img", hint:"Copy to clipboard", attr: { src: "/img/icon/copy.png" }});
        setTimeout(function(){
            si4.attachCopyToClipboard(
                _p.copyButton.selector,
                function(){ return JSON.stringify(_p.getValue());},
                function(){ alert("ok"); });
        }, 100);

        //this.pasteButton = new si4.widget.si4Element({parent:this.actionDiv.selector, tagClass:"actionButton copyButton",
        //    tagName:"img", hint:"Paste", attr: { src: "/img/icon/paste.png" }});
        //this.pasteButton.selector.click(function(){ _p.pasteFromClipboard(); });
    }

    this.addInput = function(args){
        var input;
        var parent = _p.lastCaptionContent ? _p.lastCaptionContent.selector :  _p.selector;
        var defArgs = {
            showModified: _p.showModified,
            captionWidth:_p.captionWidth,
            parent:parent,
            type:"text",
            inputConstruct: si4.widget.si4Input,
            inputClass:_p.inputClass,
            form: _p
        };
        args = si4.mergeObjects(defArgs, args);
        if (args.isArray) {
            input = new si4.widget.si4InputArray({ parent:parent, name:args.name, caption:args.caption,
                    withCode:args.withCode, inputArgs:args });
        } else {
            input = new args.inputConstruct(args);
        }
        if (input.onPaste)
            input.onPaste(_p._onPaste);
        if (args.type != "textarea") input.onKeyPressed(_p._onKeyPressed);
        if (args.type == "submit") {
            _p._submitInput = input;
            _p._submitInput.selector.click(_p._onSubmit);
        }
        if (args.type == "select") {

        }
        _p.inputs[input.name] = input;
        return input;
    };

    this.addHr = function(){
        var parent = _p.lastCaptionContent ? _p.lastCaptionContent.selector :  _p.selector;
        var hr = new si4.widget.si4Element({ parent:parent, tagName:"hr" });
    };

    this.addCaption = function(args){
        var caption = si4.getArg(args, "caption", "");
        var canMinimize = si4.getArg(args, "canMinimize", false);
        var initHide = si4.getArg(args, "initHide", false);
        var className = si4.getArg(args, "className", "");

        _p.lastCaptionHeader = new si4.widget.si4Element({ parent:_p.selector, tagClass:"header" });
        if (caption)
            _p.lastCaptionHeader.selector.html(caption);
        else
            _p.lastCaptionHeader.displayNone();

        _p.lastCaptionHeader.selector.addClass(className);

        _p.lastCaptionContent = new si4.widget.si4Element({ parent:_p.selector, tagClass:"content" });

        if (canMinimize) {
            _p.lastCaptionHeader.selector.css("cursor", "pointer")
            _p.lastCaptionHeader.selector[0].content = _p.lastCaptionContent;
            _p.lastCaptionHeader.selector.click(function(){
                this.content.expandToggle();
            });
            if (initHide) _p.lastCaptionContent.expandToggle();
        }

    };

    // Get Form data
    this.getValue = function(){
        var formData = {};
        for (var i in _p.inputs) {
            if (_p.skipTypes.indexOf(_p.inputs[i].type) != -1) continue;
            var key = _p.inputs[i].name;
            if (key[0] == "_") continue;
            var val = _p.inputs[i].getValue();
            formData[key] = val;
        }
        return formData;
    };

    // Set Form data
    this.setValue = function(formData){
        for (var key in formData) {
            var val = formData[key];
            if (_p.inputs[key]) _p.inputs[key].setValue(val);
        }
    };

    // Get value as FormData
    this.getValueAsFormData = function(){
        var formData = new FormData();
        var val = _p.getValue();
        for (var key in val) {
            formData.append(key, val[key]);
        }
        return formData;

        /*
        var formData = new FormData();
        var val = _p.getValue();
        for (var k in val) {
            formData.append(k, val[k]);
            console.log("append",k, val[k]);
        }
        return formData;
        */
    };


    this.submit = function() {
        if (_p._submitInput) _p._submitInput.selector.click();
    };

    this.allInputs = {
        resetModified: function(){
            for (var i in _p.inputs) {
                _p.inputs[i].setValue(_p.inputs[i].getValue());
                _p.inputs[i].calcModified();
            }
        },
        clear: function(){
            for (var i in _p.inputs) {
                if (_p.inputs[i].type != "submit" && _p.inputs[i].type != "button")
                    _p.inputs[i].clear();
            }
            _p.allInputs.resetModified();
        }
    };

    // Internal Event handlers
    this._onSubmit = function(e) {
        _p.trigger('onSubmit', _p);
        _p.allInputs.resetModified();
    };
    this._onKeyPressed = function(e) {
        if (e.which == 13 && _p.enterSubmits) _p.submit();
    };
    this._onPaste = function(e) {
        setTimeout(function(){
            var strValue = e.si4Input.getValue();
            if (typeof(strValue) == "object") {
                if (typeof(strValue[0]) == "string")
                    strValue = strValue[0];
                if (typeof(strValue[0]) == "object" && typeof(strValue[0].value) == "string")
                    strValue = strValue[0].value;
            }
            try {
                var value = JSON.parse(strValue);
                _p.setValue(value);
            } catch (err) {

            }
        }, 30);
    };
};
