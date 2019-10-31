si4.widget.si4Hint = function(args)
{
    // Init
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons({ parent:si4.data.contentElement });
    this.selector.addClass("si4Hint");

    // Settings
    this.text = si4.getArg(args, "text", null);
    this.hideOnMouseMove = si4.getArg(args, "hideOnMouseMove", true);
    this.destroyOnHide = si4.getArg(args, "destroyOnHide", true);

    // Implementation
    this.displayNone();

    if (this.text)
        this.selector.html(this.text);

    this.moveToCursor = function(){
        _p.selector.css("left", (si4.mouse.x +12)+"px");
        _p.selector.css("top", (si4.mouse.y -10)+"px");
    };

    this.moveToPoint = function(point){
        _p.selector.css("left", (point.left)+"px");
        _p.selector.css("top", (point.top)+"px");
    };

    this.show = function(){
        _p.selector.stop().fadeIn(si4.defaults.hintFadeTime);
        _p.lastMousePos = si4.mergeObjects(si4.mouse);
        if (this.hideOnMouseMove) {
            var f = function(e) {
                if (_p.lastMousePos.x == si4.mouse.x && _p.lastMousePos.y == si4.mouse.y) return;
                $(document).unbind("mousemove", f);
                _p.hide();
            };
            $(document).mousemove(f);
        }
    };

    this.hide = function(){
        _p.selector.stop().fadeOut(si4.defaults.hintFadeTime, function(){
            if (_p.destroyOnHide) {
                _p.selector.remove();
            }
        });
    };
};

si4.showHint = function(text){
    if (!text) return;
    var hint = new si4.widget.si4Hint({text:text});
    hint.moveToCursor();
    hint.show();
};
