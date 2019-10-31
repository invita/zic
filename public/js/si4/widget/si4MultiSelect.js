si4.widget.si4MultiSelect = function(args)
{
    // Init
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons({parent: args.parent});

    this._eventb = si4.object.si4EventBase;
    this._eventb();

    this.selector.addClass("si4MultiSelect");


    // Settings
    this.name = si4.getArg(args, "name", null);
    this.caption = si4.getArg(args, "caption", null);
    this.captionWidth = si4.getArg(args, "captionWidth", null);
    this.values = si4.getArg(args, "values", null);
    this.value = si4.getArg(args, "value", null);
    this.multiSelect = si4.getArg(args, "multiSelect", true);

    // Events
    this.onKeyDown = function(f) { _p.subscribe("onKeyDown", f); };
    this.onKeyPressed = function(f) { _p.subscribe("onKeyPressed", f); };
    this.onKeyUp = function(f) { _p.subscribe("onKeyUp", f); };
    this.onSelectionChange = function(f) { _p.subscribe("onSelectionChange", f); };

    this.buttonsContainer = new si4.widget.si4Element({ parent:this.selector });
    this.buttonsContainer.selector.addClass("buttonsContainer inline");

    if (typeof(this.caption) == "string" && this.caption) {
        this.captionDiv = new si4.widget.si4Element({ parent:this.selector, insertAtTop:true, tagName:"div" });
        this.captionDiv.selector.addClass("si4InputCaption");
        if (_p.captionWidth) this.captionDiv.selector.css("width", _p.captionWidth);
        this.captionDiv.selector.html(this.caption);
    }

    this.buttons = {};

    this.addButton = function(index, text){
        if (!text) text = si4.captionize(index);
        var button = new si4.widget.si4Element({parent:_p.buttonsContainer.selector, tagName:"div"});
        button.selector.addClass("multiSelectButton").html(text);
        button.index = index;
        //button.onKeyDown(function(e) { e.si4Input = _p; _p.trigger('onKeyDown', e); });
        //button.onKeyPressed(function(e) { e.si4Input = _p; _p.trigger('onKeyPressed', e); });
        //button.onKeyUp(function(e) { e.si4Input = _p; _p.trigger('onKeyUp', e); });
        button.isSelected = false;
        button.setSelected = function(bool){
            if (!_p.multiSelect && bool) _p.clear();
            button.isSelected = bool;
            if (button.isSelected)
                button.selector.addClass("selected");
            else
                button.selector.removeClass("selected");
        };
        button.selector.click(function(e){ button.setSelected(!button.isSelected); _p.trigger("onSelectionChange", button); });
        _p.buttons[index] = button;
        return button;

    };

    this.addHr = function(){
        var hr = new si4.widget.si4Element({parent:_p.buttonsContainer.selector, tagName:"hr"});
        //_p.buttons[index] = hr;
        return hr;

    };

    this.getValue = function(){
        if (_p.multiSelect) {
            var result = [];
            for (var i in _p.buttons)
                if (_p.buttons[i].isSelected)
                    result.push(_p.buttons[i].index);
            return result;
        } else {
            for (var i in _p.buttons)
                if (_p.buttons[i].isSelected)
                    return _p.buttons[i].index;
        }
    };

    this.setValues = function(value){
        if (value && value.length) {
            _p.buttons = {};
            _p.buttonsContainer.selector.empty();
            for (var i in value) {
                _p.addButton(value[i]);
            }
        }
    };

    this.setValue = function(value){
        if (value && $.isArray(value)) {
            _p.clear();
            for (var i in value) _p.buttons[value[i]].setSelected(true);
            this.value = value;
        } else if (_p.buttons[value]) {
            _p.buttons[value].setSelected(true);
        }
    };

    this.clear = function(){
        for (var i in _p.buttons) _p.buttons[i].setSelected(false);
    };

    this.calcModified = function(){
        //for (var i in _p.buttons) _p.buttons[i].calcModified();
    };

    if (this.values) this.setValues(this.values);
    if (this.value) this.setValue(this.value);

    //this. = this.addInput();

    /*
    this.delButton = new si4.widget.si4Element({ parent:this.mainInput.selector, tagName:"div" });
    this.delButton.selector.addClass("inputButton delButton").html("-");
    this.delButton.selector.click(function(e){
        if (_p.buttons.length > 1) {
            var delInput = _p.buttons[_p.buttons.length -1];
            delInput.selector.remove();
            _p.buttons.splice(_p.buttons.length -1, 1);
        }
    });
    this.addButton = new si4.widget.si4Element({ parent:this.mainInput.selector, tagName:"div" });
    this.addButton.selector.addClass("inputButton addButton").html("+");
    this.addButton.selector.click(function(e){
        var newInput = _p.addInput();
    });
    */

    /*

    // Settings
    this.name = si4.getArg(args, "name", null);
    this.caption = si4.getArg(args, "caption", null);
    this.inputArgs = si4.getArg(args, "inputArgs", {});
    this.inputConstruct = si4.getArg(args, "inputConstruct", si4.widget.si4Input);

    // Events
    this.onKeyDown = function(f) { _p.subscribe("onKeyDown", f); };
    this.onKeyPressed = function(f) { _p.subscribe("onKeyPressed", f); };
    this.onKeyUp = function(f) { _p.subscribe("onKeyUp", f); };

    // Implementation
    if (!this.name) this.name = si4.widget._nextInputId();
    if (this.caption === null) this.caption = si4.captionize(this.name);


    */

}