si4.widget.si4Dialog = function(args)
{
    // Init
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons({ parent:$("body"), tagClass:"si4Dialog" });

    // Settings
    this.backgroundOpacity = si4.getArg(args, "backgroundOpacity", 0.4);
    this.title = si4.getArg(args, "title", "Dialog");
    this.text = si4.getArg(args, "text", null);
    this.canClose = si4.getArg(args, "canClose", true);
    this.createMainTab = si4.getArg(args, "createMainTab", true);

    // Implementation
    this.background = new si4.widget.si4Element({parent:this.selector, tagClass:"dialogBg"});
    this.background.selector.css("opacity", this.backgroundOpacity);

    this.container = new si4.widget.si4Element({parent:this.selector, tagClass:"dialogContainer"});

    this.header = new si4.widget.si4Element({parent:this.container.selector, tagClass:"dialogHeader"});

    this.titleDiv = new si4.widget.si4Element({parent:this.header.selector, tagClass:"titleDiv"});

    if (this.canClose) {
        this.closeDiv = new si4.widget.si4Element({parent:this.header.selector, tagClass:"closeDiv"});
        this.closeDiv.selector.html("x");
        this.closeDiv.selector.click(function(e) { _p.close(); });
    }

    this.content = new si4.widget.si4Element({parent:this.container.selector, tagClass:"dialogContent"});

    this.header.setGradient('blue');

    if (this.title) this.titleDiv.selector.html(this.title);
    if (this.text) this.content.selector.html(this.text);

    if (this.createMainTab) {
        this.mainTab = new si4.widget.si4TabPage({
            name: this.title ? this.title : "Dialog",
            parent: this.content.selector,
            canClose: false,
            hideHeader: true
        });
        this.mainTab.onChildClosed(function() { _p.close(); });
    }

    this.close = function() {
        _p.selector.remove();
    };
};