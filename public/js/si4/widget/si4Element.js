si4.widget.si4Element = function(args)
{
    // Init
    var _p = this;
    this.issi4Element = true;

    if (!args) args = {};
    this.args = args;

    // Settings
    this.parent = si4.getArg(args, "parent", null);
    this.tagName = si4.getArg(args, "tagName", "div");
    this.tagClass = si4.getArg(args, "tagClass", "");
    this.tagId = args.tagId ? args.tagId : null;
    this.hidden = si4.getArg(args, "hidden", false);
    this.attr = si4.getArg(args, "attr", {});
    this.style = si4.getArg(args, "style", {});
    this.hint = si4.getArg(args, "hint", null);
    this.draggable = si4.getArg(args, "draggable", null);
    this.fromElement = si4.getArg(args, "fromElement", null);
    this.insertAtTop = si4.getArg(args, "insertAtTop", false);
    this.fadeTime = si4.getArg(args, "fadeTime", 600);

    this.fromElementSelector = null;
    if (this.fromElement) {
        var fromEl = $(this.fromElement);
        if (fromEl && fromEl.length)
            this.fromElementSelector = fromEl;
    }


    // Implementation

    this.appendTo = function(parent, insertAtTop){
        this.parent = parent;
        if (typeof(insertAtTop) == "undefined") insertAtTop = _p.insertAtTop;

        if (insertAtTop)
            parent.prepend(_p.selector);
        else
            parent.append(_p.selector);
    };

    if (this.fromElementSelector) {

        // From existing element
        this.selector = this.fromElementSelector;

    } else {

        // Create new element
        var htmlEl = document.createElement(this.tagName);
        this.selector = $(htmlEl);

        if (this.parent)
            this.appendTo(this.parent);
    }


    var cl = this.tagClass ? this.tagClass : this.selector.attr('class');
    if (cl) this.attr['class'] = cl;

    var id = this.tagId ? this.tagId : this.selector.attr('id');
    if (!id) id = si4.widget._nextId();
    this.attr['id'] = id;
    this.tagId = id;

    if (this.hidden) this.style['display'] = 'none';


    // Apply attributes
    for (var k in this.attr) {
        try { this.selector.attr(k, this.attr[k]); }
        catch(e) {};
    }

    // Apply styles
    for (var k in this.style)
        this.selector.css(k, this.style[k]);



    // Hint
    this.hintInited = false;
    this.setHint = function(text) {
        if (!_p.hintInited){
            _p.lastMousePos = si4.mergeObjects(si4.mouse);
            _p.showHint = function(){ si4.showHint(_p.hint); }
            _p.selector.mouseenter(function(e){
                _p.hintTimeout = setTimeout(_p.showHint, si4.defaults.hintTriggerDelay);
            });
            _p.selector.mousemove(function(e){
                if (_p.lastMousePos.x == si4.mouse.x && _p.lastMousePos.y == si4.mouse.y) return;
                _p.lastMousePos = si4.mergeObjects(si4.mouse);

                if (_p.hintTimeout) clearTimeout(_p.hintTimeout);
                _p.hintTimeout = setTimeout(_p.showHint, si4.defaults.hintTriggerDelay);
            });
            _p.selector.mouseleave(function(e){
                if (_p.hintTimeout) clearTimeout(_p.hintTimeout);
            });
            _p.hintInited = true;
        }
        _p.hint = text;
    };

    if (this.hint) this.setHint(this.hint);


    this.setGradient = function(newGrad, hoverP, activeP) {
        this.gradient = newGrad;

        var classNames = this.selector.attr("class").split(' ');
        for (var classIdx in classNames) {
            var className = classNames[classIdx];
            if (className.substr(0,4) == "grad")
                this.selector.removeClass(className);
        }

        var gradClass = "grad"+si4.capitalize(newGrad);
        this.selector.addClass(gradClass);

        if (hoverP) this.selector.addClass(gradClass+'H');
        if (activeP) this.selector.addClass(gradClass+'A');

        return this;
    };

    this.expandInterval = this.fadeTime;

    this.expandToggleOnClick = function(onOff) {
        if (onOff === false)
            this.selector.unbind('click');
        else
            this.selector.click(function(event) { _p.expandToggle(); });

        return this;
    };

    this.toggleHeight = 100;

    // Expands or collapses the tag
    this.expandToggle = function() {
        _p.selector.slideToggle(_p.expandInterval);
        //_p.selector.slideDown(_p.expandInterval);
    };

    this.fadeToggle = function() {
        // Thank you people of jQuery. :)
        if (_p.selector.css("display") == "none")
            _p.selector.fadeIn(this.fadeTime);
        else
            _p.selector.fadeOut(this.fadeTime);
    };

    this.setAbsolute = function(left, top){
        if (!left) left = "0px";
        if (!top) top = "0px";
        if (typeof(left) == "number") left = left+"px";
        if (typeof(top) == "number") top = top+"px";
        _p.selector.css("position", "absolute").css("top", top).css("left", left);
    };

    this.getAbsolutePosition = function() {
        return _p.selector.position();
    };

    this.addHtml = function(html){
        _p.selector.html(_p.selector.html()+html);
    };

    this.isDisplay = function(){ return _p.selector.css("display") != "none"; };
    this.displayNone = function(){ _p.selector.css("display", "none"); return _p; };
    this.display = function(){ _p.selector.css("display", ""); return _p; };
    this.fadeIn = function(){ _p.selector.fadeIn(this.fadeTime); return _p; };
    this.fadeOut = function(){ _p.selector.fadeOut(this.fadeTime); return _p; };
    this.fadeTo = function(value){ _p.selector.fadeTo(this.fadeTime, value); return _p; };

    return this;
};

si4.widget.si4LineBreak = function(args)
{
    var _p = this;
    this._cons = si4.widget.si4Element;
    args = si4.mergeObjects(args, { tagName:"span", tagClass:"lineBreak" });
    this._cons(args);
}

// Id Generator
si4.widget._lastId = 0;
si4.widget._nextId = function(){
    si4.widget._lastId += 1;
    return "si4"+si4.widget._lastId;
};
