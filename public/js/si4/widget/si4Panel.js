si4.widget.si4Panel = function(args)
{
    // Init
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons({ parent:args.parent });
    this.selector.addClass("si4Panel");
    this.groups = [];
    this.firstGroup = null;

    // Settings
    this.layoutClass = si4.getArg(args, "layoutClass", "si4PanelFlowLayout");
    this.firstGroupName = si4.getArg(args, "firstGroupName", "");

    // Implementation
    this.selector.addClass(this.layoutClass);

    this.addGroup = function(groupName){
        var group = new si4.widget.si4PanelGroup({parent:_p.selector, name:groupName, layoutClass: _p.layoutClass});
        _p.groups.push(group);
        if (!_p.firstGroup) _p.firstGroup = group;
        return group;
    };

    if (this.firstGroupName)
        this.addGroup(this.firstGroupName);
};

si4.widget.si4PanelGroup = function(args){
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons({ parent:args.parent });
    this.selector.addClass("si4PanelGroup");

    this.name = si4.getArg(args, "name", "");
    this.layoutClass = si4.getArg(args, "layoutClass", "si4PanelFlowLayout");

    this.selector.addClass(this.layoutClass);

    // Header
    this.header = new si4.widget.si4Element({parent:this.selector});
    this.header.selector.addClass("header");

    // Content
    this.content = new si4.widget.si4Element({parent:this.selector});
    this.content.selector.addClass("content");

    this.setName = function(newName){
        _p.name = newName;
        _p.header.selector.html(_p.name);
        if (_p.name)
            _p.header.display();
        else
            _p.header.displayNone();
    };

    this.setName(_p.name);
};