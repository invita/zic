si4.widget.si4HtmlTable = function(args)
{
    // Init
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons(si4.mergeObjects(args, {tagName:"table"}));

    this.lastTr = null;
    this.lastTd = null;
    this.trs = [];

    // Settings
    this.rows = si4.getArg(args, "rows", 1);
    this.columns = si4.getArg(args, "columns", 2);

    this.addTr = function(){
        var tr = new si4.widget.si4Element({parent:_p.selector, tagName:"tr"});
        return tr;
    };
    this.addTd = function(){
        var td = new si4.widget.si4Element({parent:_p.lastTr.selector, tagName:"td"});
        return td;
    };
    this.getCell = function(row, column) {
        var result = null;
        if (_p.trs[row] && _p.trs[row].tds[column])
            result = _p.trs[row].tds[column];
        return result;
    };

    for (var i = 0; i < this.rows; i++) {

        this.lastTr = this.addTr();
        this.lastTr.selector.addClass("si4HtmlTable_tr");
        this.lastTr.tds = [];
        this.trs.push(this.lastTr);

        for (var j = 0; j < this.columns; j++) {
            this.lastTd = this.addTd();
            this.lastTd.selector.addClass("si4HtmlTable_td");
            this.lastTr.tds.push(this.lastTd);
        }
    }
};
