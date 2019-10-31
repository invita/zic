// ------------------------
// |  Tab1  |  Tab2  | ...   } si4TabPage(this).header
// ------------------------
// | Content...              }
// |                         } si4TabPage(this).content
// |                         }
// |                         }
// ------------------------
//
//  Tab1(Tab2)  }  si4TabPage(this).tabButton
//
//
si4.widget.si4TabPage = function(args)
{
    // Init
    var _p = this;
    this.isTabPage = true;
    this.header = null;
    this.content = null;
    this.uniqId = si4.widget._nextTabId();

    this.eventb = si4.object.si4EventBase;
    this.eventb();

    this.parentTab = null;
    this.childTabs = {};

    this.tabCloseImgUrl = "/img/tabClose4.png";

    // Settings
    this.parent = si4.getArg(args, "parent", null);
    this.name = si4.getArg(args, "name", this.uniqId);
    this.caption = si4.getArg(args, "caption", args.name ? this.name : "newTab");

    this.type = si4.getArg(args, "type", "tab");
    this.isButton = this.type == "button";

    this.autoActive = si4.getArg(args, "autoActive", true);
    this.unique = si4.getArg(args, "unique", false);
    this.canClose = si4.getArg(args, "canClose", !this.isButton);
    this.canCloseFirstTab = si4.getArg(args, "canCloseFirstTab", false);
    this.contentText = si4.getArg(args, "contentText", "");
    this.fadeTime = si4.getArg(args, "fadeTime", si4.defaults.fadeTime);
    this.hideHeader = si4.getArg(args, "hideHeader", false);
    this.cropCaptionLength = si4.getArg(args, "cropCaptionLength", 30);

    switch (this.type) {
        case "tab":
            this.activeGrad = si4.getArg(args, "activeGrad", si4.defaults.tabActiveGrad);
            this.inactiveGrad = si4.getArg(args, "inactiveGrad", si4.defaults.tabInactiveGrad);
            break;
        case "button":
            this.activeGrad = si4.getArg(args, "activeGrad", si4.defaults.tabActiveGrad);
            this.inactiveGrad = si4.getArg(args, "inactiveGrad", si4.defaults.buttonGrad);
            break;
    }

    // Events
    this.onClose = function(f) { _p.subscribe("onClose", f); };
    this.onClosed = function(f) { _p.subscribe("onClosed", f); };
    this.onChildClosed = function(f) { _p.subscribe("onChildClosed", f); };
    this.onActive = function(f) { _p.subscribe("onActive", f); };

    // Implementation

    this._createHeader = function(parent){
        _p.header = new si4.widget.si4TabPageHeader({parent:parent, insertAtTop:true});
        _p.header.selector.addClass("si4TabHeader");
        _p.header.selector.si4TabPageHeader = _p.header;
        if (_p.hideHeader) _p.header.displayNone();
    };

    this._createTabButton = function(si4TabHeader){
        var isFirstTab = si4TabHeader.children().length == 0;
        _p.tabButton = new si4.widget.si4Element({parent:si4TabHeader});
        _p.tabButton.selector.addClass("si4TabButton");
        _p.tabButton.setGradient(_p.inactiveGrad, true, true);

        _p.tabButton.captionSpan = new si4.widget.si4Element({parent:_p.tabButton.selector, tagName:'span'});
        _p.tabButton.captionSpan.selector.addClass("si4TabButton_caption");
        if (_p.caption) _p.setCaption(_p.caption);

        if (_p.canClose && (_p.canCloseFirstTab || !isFirstTab)) _p._createCloseSpan();

        _p.tabButton.selector.click(function(e){
            _p.selectTab();
            //console.log("("+_p.uniqId+") Child Tabs:\n"+si4.debug(_p.childTabs, 0));
        });
    };

    this._createCloseSpan = function(){
        _p.tabButton.closeSpan = new si4.widget.si4Element({parent:_p.tabButton.selector, tagName:'span'});
        _p.tabButton.closeSpan.selector.addClass("si4TabButton_closeButton");
        _p.tabButton.closeImg = new si4.widget.si4Element({parent:_p.tabButton.closeSpan.selector, tagName:'img'});
        _p.tabButton.closeImg.selector.attr("src", _p.tabCloseImgUrl);
        _p.tabButton.closeSpan.selector.click(function(e) {
            _p.destroyTab();
        });
    };

    this._createTabContent = function(si4TabHeader){
        _p.content = new si4.widget.si4Element({parent:si4TabHeader});
        _p.content.selector.si4TabPage = _p;
        _p.content.selector.addClass("si4TabContent");
        _p.content.selector.css("display", "none");
        _p.content.selector.html(_p.contentText);
        _p.content.selector.tabPage = _p;

        if (_p.autoActive) _p.selectTab();
    };

    this.selectTab = function(){
        if (!_p.isButton) {
            if (_p.tabButton.selector.hasClass("active")) return;
            for (var i in _p.header.pages) {
                var page = _p.header.pages[i];
                page.tabButton.selector.removeClass("active");
                page.tabButton.setGradient(page.inactiveGrad, true, true);
                page.content.selector.css("display", "none");
            }
            _p.tabButton.selector.addClass("active");
            _p.tabButton.setGradient(_p.activeGrad, true, true);
            _p.content.selector.fadeIn(_p.fadeTime);
        }
        _p.trigger("onActive", {tabPage:_p});
    };

    this.destroyTab = function(){
        _p.trigger("onClose", {tabPage:_p});

        for (var childIdx in _p.childTabs) {
            _p.childTabs[childIdx].destroyTab();
        }
        if (_p.parentTab)
            delete _p.parentTab.childTabs[_p.uniqId];

        var pageToSelectAfterClose = null;
        if (_p.tabButton.selector.hasClass("active"))
            pageToSelectAfterClose = _p.header.findPageBeforeId(_p.uniqId);

        _p.header.removePageRef(_p.uniqId);
        _p.tabButton.selector.remove();
        _p.content.selector.remove();

        if (pageToSelectAfterClose)
            pageToSelectAfterClose.selectTab();

        _p.trigger("onClosed", {tabPage:_p});
        if (_p.parentTab) _p.parentTab.trigger('onChildClosed', {tabPage:_p})
    };

    this.setCaption = function(newCaption) {
        newCaption = newCaption.trim();

        if (_p.cropCaptionLength && newCaption.length > _p.cropCaptionLength)
            newCaption = newCaption.substring(0, _p.cropCaptionLength)+"...";

        _p.caption = newCaption;
        _p.tabButton.captionSpan.selector.html(newCaption);
    };

    this.debugMode = false;
    this.appendTo = function(parent, insertInFront) {

        var contentParent;
        if (parent.isTabPage){

            // Appending to parent si4TabPage
            if (_p.debugMode) console.log("append "+_p.name+" to "+_p.parent.name);
            _p.header = parent.header;
            contentParent = parent.header.parent;

            if (contentParent.hasClass("si4TabContent") && contentParent.si4TabPage){
                _p.parentTab = contentParent.si4TabPage;
                _p.parentTab.childTabs[_p.uniqId] = _p;
            }

        } else if (parent.isTabPageHeader){

            // Appending to parent si4TabPageHeader
            if (_p.debugMode) console.log("append "+_p.name+" to another tabPage header");
            _p.header = parent;
            contentParent = parent.parent;

            if (contentParent.hasClass("si4TabContent") && contentParent.si4TabPage){
                _p.parentTab = contentParent.si4TabPage;
                _p.parentTab.childTabs[_p.uniqId] = _p;
            }

        } else {

            // Appending to parent si4Element
            if (parent.issi4Element) {
                if (_p.debugMode) console.log("append "+_p.name+" to si4Element");
                parent = parent.selector;
            }
            contentParent = parent;

            // Appending to an element (jquery)
            if (_p.debugMode) console.log("append "+_p.name+" to jQuery selector");
            _p._createHeader(parent);

            if (parent.hasClass("si4TabContent") && parent.si4TabPage) {
                _p.parentTab = parent.si4TabPage;
                _p.parentTab.childTabs[_p.uniqId] = _p;
            }

        }

        //si4.dump(contentParent);
        //console.log(contentParent+" "+parent.isTabPage);

        var pageWithThatName = _p.header.findPageByName(_p.name);

        if (!_p.unique || !pageWithThatName) {
            _p._createTabButton(_p.header.selector, insertInFront);
            _p._createTabContent(contentParent);
            _p.header.addPageReference(_p.uniqId, _p);
        } else {
            for (var i in pageWithThatName)
                this[i] = pageWithThatName[i];

            pageWithThatName.selectTab();
        }
    };

    this.createTabPage = function(args) {
        if (!args) args = {};
        args.parent = _p;
        var newTabPage = new si4.widget.si4TabPage(args);
        newTabPage.parentTabPage = _p.parentTabPage;
        return newTabPage;
    };

    this.createChildPage = function(args) {
        if (!args) args = {};
        args.parent = _p.childTabHeader ? _p.childTabHeader : _p.content.selector;
        var childTabPage = new si4.widget.si4TabPage(args);
        childTabPage.parentTabPage = _p;
        childTabPage.header.parentTabPage = _p;
        _p.childTabHeader = childTabPage.header;
        return childTabPage;
    };

    if (this.parent)
        this.appendTo(this.parent);

};

si4.widget.si4TabPageHeader = function(args){
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons(args);

    this.isTabPageHeader = true;

    this.pages = {};
    this.addPageReference = function(uniqId, tabPage){
        _p.pages[uniqId] = tabPage;
    };
    this.removePageRef = function(uniqId){
        delete _p.pages[uniqId];
    };
    this.referenceExists = function(uniqId) {
        return _p.pages[uniqId] ? true : false;
    };
    this.findPageIdByName = function(pageName) {
        for (var pageId in _p.pages) {
            if (_p.pages[pageId].name == pageName)
                return pageId;
        }
        return null;
    };
    this.findPageByName = function(pageName) {
        var pageId = _p.findPageIdByName(pageName);
        if (pageId) return _p.pages[pageId];
        return null;
    };
    this.findPageById = function(uniqId) {
        return _p.pages[uniqId];
    };
    this.findPageBeforeId = function(uniqId) {
        var result = null;
        for (var i in _p.pages){
            if (i == uniqId) break;
            if (_p.pages[i].isButton) continue;
            result = _p.pages[i];
        }
        //if (!result) console.log(Object.keys(_p.pages));
        if (!result && Object.keys(_p.pages).length > 1) result = _p.pages[Object.keys(_p.pages)[1]];

        return result;
    };
};

// Id Generator
si4.widget._lastTabId = 0;
si4.widget._nextTabId = function(){
    si4.widget._lastTabId += 1;
    return "tab"+si4.widget._lastTabId;
};
