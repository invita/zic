// si4DataTable
// si4DataTableRow
// si4DataTableField
si4.widget.si4DataTable = function(args)
{
    // Init
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons({ parent:args.parent });

    this._eventb = si4.object.si4EventBase;
    this._eventb();

    // Settings - Basic
    this.name = si4.getArg(args, "name", null);
    this.caption = si4.getArg(args, "caption", "");
    this.primaryKey = si4.getArg(args, "primaryKey", null);
    this.fields = si4.getArg(args, "fields", {});
    this.fieldOrder = si4.getArg(args, "fieldOrder", "rowData"); // One of: "definedFields" - use order of given fields, "rowData" - use order as received from server
    this.showOnlyDefinedFields = si4.getArg(args, "showOnlyDefinedFields", false);
    this.actions = si4.getArg(args, "actions", {});
    this.filter = si4.getArg(args, "filter", null);
    this.entityTitleNew = si4.getArg(args, "entityTitleNew", null);
    this.entityTitleEdit = si4.getArg(args, "entityTitleEdit", null);
    this.dataSource = si4.getArg(args, "dataSource", null);
    this.editorModuleArgs = si4.getArg(args, "editorModuleArgs", null);
    this.hoverRows = si4.getArg(args, "hoverRows", true);
    this.hoverCells = si4.getArg(args, "hoverCells", !this.hoverRows);
    this.tabPage = si4.getArg(args, "tabPage", null);
    this.editable = si4.getArg(args, "editable", false);
    this.selectCallback = si4.getArg(args, "selectCallback", null);
    this.customInsert = si4.getArg(args, "customInsert", null);
    this.subDataTable = si4.getArg(args, "subDataTable", null);
    this.canExpand = si4.getArg(args, "canExpand", this.subDataTable ? true : false);
    this.initExpandAll = si4.getArg(args, "initExpandAll", false);
    this.hideNoData = si4.getArg(args, "hideNoData", false);
    this.showPaginator = si4.getArg(args, "showPaginator", true);
    this.maxRecordCount = si4.getArg(args, "maxRecordCount", 0);
    this.initRefresh = si4.getArg(args, "initRefresh", true);
    this.canExportXls = si4.getArg(args, "canExportXls", false);
    this.canExportCsv = si4.getArg(args, "canExportCsv", false);
    this.canExportPdf = si4.getArg(args, "canExportPdf", false);
    this.filterHint = si4.getArg(args, "filterHint", true);
    this.customControlls = si4.getArg(args, "customControlls", null);
    this.replaceUrlPagination = si4.getArg(args, "replaceUrlPagination", false);

    this.rowsPerPage = si4.getArg(args, "rowsPerPage", si4.defaults.dataTableRowsPerPage); // Ignored if dataSource is given

    this.canInsert = si4.getArg(args, "canInsert", true);
    this.canDelete = si4.getArg(args, "canDelete", true);

    // Settings - Appearance
    this.cssClass_holderDiv = si4.getArg(args, "cssClass_holderDiv", "si4DataTable");
    this.cssClass_table = si4.getArg(args, "cssClass_table", "si4DataTable_table");

    // Events
    this.onDataFeed = function(f) { _p.subscribe("dataFeed", f); };
    this.onDataFeedComplete = function(f) { _p.subscribe("dataFeedComplete", f); };
    this.onFirstFeedComplete = function(f) { _p.subscribe("firstFeedComplete", f); };
    this.onRowSetValue = function(f) { _p.subscribe("rowSetValue", f); };

    // Data Fields Events
    this.onRowClick = function(f) { _p.subscribe("dataRowClick", f); };
    this.onRowDoubleClick = function(f) { _p.subscribe("dataRowDoubleClick", f); };
    this.onRowRightClick = function(f) { _p.subscribe("dataRowRightClick", f); };
    this.onFieldClick = function(f) { _p.subscribe("dataFieldClick", f); };
    this.onFieldDoubleClick = function(f) { _p.subscribe("dataFieldDoubleClick", f); };
    this.onFieldRightClick = function(f) { _p.subscribe("dataFieldRightClick", f); };

    // Header Fields Events
    this.onHeaderRowClick = function(f) { _p.subscribe("headerRowClick", f); };
    this.onHeaderRowDoubleClick = function(f) { _p.subscribe("headerRowDoubleClick", f); };
    this.onHeaderRowRightClick = function(f) { _p.subscribe("headerRowRightClick", f); };
    this.onHeaderFieldClick = function(f) { _p.subscribe("headerFieldClick", f); };
    this.onHeaderFieldDoubleClick = function(f) { _p.subscribe("headerFieldDoubleClick", f); };
    this.onHeaderFieldRightClick = function(f) { _p.subscribe("headerFieldRightClick", f); };

    // All Fields Events
    this.onAnyRowClick = function(f) { _p.subscribe("rowClick", f); };
    this.onAnyRowDoubleClick = function(f) { _p.subscribe("rowDoubleClick", f); };
    this.onAnyRowRightClick = function(f) { _p.subscribe("rowRightClick", f); };
    this.onAnyFieldClick = function(f) { _p.subscribe("fieldClick", f); };
    this.onAnyFieldDoubleClick = function(f) { _p.subscribe("fieldDoubleClick", f); };
    this.onAnyFieldRightClick = function(f) { _p.subscribe("fieldRightClick", f); };


    // Implementation
    this.selector.addClass(_p.cssClass_holderDiv);
    this.constructed = false;
    this.currentPage = 1;
    this.currentPageCount = 1;
    this.firstFeed = true;

    this.filter = si4.mergeObjects({enabled:true, visible:false, autoApply: true}, this.filter);

    if (_p.dataSource) {
        _p.dataSource.dataTable = _p;
        _p.rowsPerPage = _p.dataSource.pageCount;
    }

    // Table
    this.createTable = function() {
        if (!_p.infoDiv){
            _p.infoDiv = new si4.widget.si4Element({parent:_p.selector, tagClass:"infoDiv"});
            _p.infoDiv.selector.css("display", "none");
        }

        if (!_p.table) {
            _p.table = new si4.widget.si4Element({parent:_p.selector, tagName:"table"});
            _p.table.selector.addClass(_p.cssClass_table);
            _p.table.selector.attr("cellpadding", "0");
            _p.table.selector.attr("cellspacing", "0");
        }

        if (!_p.tHead) _p.tHead = new si4.widget.si4Element({parent:_p.table.selector, tagName:"thead"});
        if (!_p.tBody) _p.tBody = new si4.widget.si4Element({parent:_p.table.selector, tagName:"tbody"});

        _p.createHeaderRow();
        if (_p.filter.enabled) _p.createFilterRow();
        _p.createRows();
        if (_p.canInsert)
            _p.createInsertButton();
        if (_p.dataSource) {
            _p.createDSControlDiv("dsControl");
            _p.createDSControlDiv("dsControlBottom");
        }
    };

    // Header
    this.createHeaderRow = function() {
        if (_p.headerRow) _p.headerRow.selector.remove();
        _p.headerRow = new si4.widget.si4DataTableRow(_p.tHead.selector, {
            headerRow: true,
            dataTable: _p
        });

        if (!_p.bluePrint || _p.bluePrint.noData) {

        } else {
            console.log("_p.bluePrint.fields", _p.bluePrint.fields);
            for (var fieldKey in _p.bluePrint.fields) {
                var fieldBP = _p.bluePrint.fields[fieldKey];
                _p.headerRow.addField(fieldBP.fieldKey, fieldBP.fieldLabel, fieldBP);
            }
        }
    };

    this.createFilterRow = function() {
        if (_p.filterRow) _p.filterRow.selector.remove();
        _p.filterRow = new si4.widget.si4DataTableRow(_p.tHead.selector, {
            filterRow: true,
            dataTable: _p
        });

        if (_p.bluePrint) {
            for (var fieldKey in _p.bluePrint.fields) {
                var fieldBP = _p.bluePrint.fields[fieldKey];
                _p.filterRow.addField(fieldBP.fieldKey, fieldBP.fieldLabel, fieldBP);
            }
        }

        if (_p.filter.value) _p.filterRow.setFilterValue(_p.filter.value);
    };

    this.createRows = function() {
        if (_p.rows && _p.rows.length) {
            for (var i in _p.rows) {
                if (_p.rows[i].subRowTr) _p.rows[i].subRowTr.selector.remove();
                _p.rows[i].selector.remove();
            }
        }

        _p.rows = [];

        if (!_p.bluePrint) return;

        //for (var rowIdx = -1; rowIdx < this.pageSize; rowIdx++){
        for (var rowIdx = 0; rowIdx < _p.rowsPerPage; rowIdx++){

            var row = new si4.widget.si4DataTableRow(_p.tBody.selector, {
                dataTable: _p,
                rowIdx: rowIdx
            });

            if (!_p.bluePrint) {
                row.addField("init", "");
            } else {
                for (var fieldKey in _p.bluePrint.fields) {
                    var fieldBP = _p.bluePrint.fields[fieldKey];
                    row.addField(fieldBP.fieldKey, fieldBP.initValue, fieldBP);
                }
            }

            if (_p.subDataTable) {
                row.createSubRow();
            }

            _p.rows.push(row);
        }
    };

    this.createInsertButton = function() {
        if (_p.insertButton) return;
        _p.insertBar = new si4.widget.si4Element({ parent:_p.selector });
        _p.insertButton = new si4.widget.si4Element({parent:_p.insertBar.selector, tagClass:"insertButton", tagName:"button"});
        _p.insertButton.selector.attr("tabindex", 0);
        _p.insertButton.img = new si4.widget.si4Element({parent:_p.insertButton.selector, tagName:"img"});
        _p.insertButton.img.selector.addClass("icon16");
        _p.insertButton.img.selector.attr("src", "/img/insert.png");
        _p.insertButton.span = new si4.widget.si4Element({parent:_p.insertButton.selector, tagName:"span"});
        _p.insertButton.span.selector.html(si4.translate("dataTable_insert"));
        if (typeof(_p.customInsert) == "function") {
            _p.insertButton.selector.click(function(e) { _p.customInsert(_p) });
        } else {
            _p.insertButton.selector.click(function(e){
                var row = _p.createEmptyRow();
                var tabName = si4.mergePlaceholders(_p.entityTitleNew, row);
                // TODO
                var editorModuleArgs = si4.mergeObjects(_p.editorModuleArgs, {entityTitle:_p.entityTitleNew});
                editorModuleArgs.onClosed = function(args){
                    _p.refresh();
                    window.scrollTo(0,0);
                };
                if (_p.dataSource && _p.dataSource.staticData)
                    editorModuleArgs.staticData = si4.mergeObjects(_p.dataSource.staticData, editorModuleArgs.staticData);
                //console.log("editorModuleArgs.mainTab = _p.tabPage", _p.tabPage);
                if (_p.tabPage) editorModuleArgs.tabPage = _p.tabPage;
                si4.loadModule(editorModuleArgs);
            });
        }
    };

    // Paginator
    this.createDSControlDiv = function(cpName) {
        if (!cpName) cpName = "dsControl";
        if (_p[cpName]) return;
        _p[cpName] = new si4.widget.si4Element({parent:_p.selector, insertAtTop:cpName == "dsControl", tagClass:"dsControl"});

        if (!_p.showPaginator)
            _p[cpName].displayNone();

        // First page button
        _p[cpName].firstPage = new si4.widget.si4Element({parent:_p[cpName].selector, tagClass:"inline prevButton vmid"});
        _p[cpName].firstPageImg = new si4.widget.si4Element({parent:_p[cpName].firstPage.selector, tagName:"img", tagClass:"icon8 vmid"});
        _p[cpName].firstPageImg.selector.attr("src", "/img/icon/dataTable_prev.png");
        _p[cpName].firstPageSpan = new si4.widget.si4Element({parent:_p[cpName].firstPage.selector, tagName:"span", tagClass:"vmid"});
        _p[cpName].firstPageSpan.selector.html(si4.translate("dataTable_navFirst"));
        _p[cpName].firstPage.selector.click(function(){ _p.switchPage(1); });

        // Prev page button
        _p[cpName].prevPage = new si4.widget.si4Element({parent:_p[cpName].selector, tagClass:"inline prevButton vmid"});
        _p[cpName].prevPageImg = new si4.widget.si4Element({parent:_p[cpName].prevPage.selector, tagName:"img", tagClass:"icon8 vmid"});
        _p[cpName].prevPageImg.selector.attr("src", "/img/icon/dataTable_prev.png");
        _p[cpName].prevPageSpan = new si4.widget.si4Element({parent:_p[cpName].prevPage.selector, tagName:"span", tagClass:"vmid"});
        _p[cpName].prevPageSpan.selector.html(si4.translate("dataTable_navPrev"));
        _p[cpName].prevPage.selector.click(function(){ _p.switchPage(_p.currentPage-1); });

        // Page inputs
        _p[cpName].pageInput = new si4.widget.si4Element({parent:_p[cpName].selector,
            tagName:"input", tagClass:"inline vmid dataTable_pageInput"});
        _p[cpName].pageInput.selector.keypress(function(e){
            if (e.which == 13) {
                var val = _p[cpName].pageInput.selector.val();
                var intVal = parseInt(val);
                if (isNaN(intVal)) intVal = 1;
                _p.switchPage(intVal);
            }
        });
        _p[cpName].pageInput.selector.val(_p.initPage);

        _p[cpName].slashSpan = new si4.widget.si4Element({parent:_p[cpName].selector, tagClass:"inline vmid"});
        _p[cpName].slashSpan.selector.html('/');

        _p[cpName].pageCount = new si4.widget.si4Element({parent:_p[cpName].selector,
            tagName:"input", tagClass:"inline vmid dataTable_pageCount"});
        _p[cpName].pageCount.selector.attr("readOnly", true);
        _p[cpName].pageCount.selector.val(1);

        // Next page button
        _p[cpName].nextPage = new si4.widget.si4Element({parent:_p[cpName].selector, tagClass:"inline nextButton vmid"});
        _p[cpName].nextPageSpan = new si4.widget.si4Element({parent:_p[cpName].nextPage.selector, tagName:"span", tagClass:"vmid"});
        _p[cpName].nextPageSpan.selector.html(si4.translate("dataTable_navNext"));
        _p[cpName].nextPageImg = new si4.widget.si4Element({parent:_p[cpName].nextPage.selector, tagName:"img", tagClass:"icon8 vmid"});
        _p[cpName].nextPageImg.selector.attr("src", "/img/icon/dataTable_next.png");
        _p[cpName].nextPage.selector.click(function(){ _p.switchPage(_p.currentPage+1); });

        // Last page button
        _p[cpName].lastPage = new si4.widget.si4Element({parent:_p[cpName].selector, tagClass:"inline lastButton vmid"});
        _p[cpName].lastPageSpan = new si4.widget.si4Element({parent:_p[cpName].lastPage.selector, tagName:"span", tagClass:"vmid"});
        _p[cpName].lastPageSpan.selector.html(si4.translate("dataTable_navLast"));
        _p[cpName].lastPageImg = new si4.widget.si4Element({parent:_p[cpName].lastPage.selector, tagName:"img", tagClass:"icon8 vmid"});
        _p[cpName].lastPageImg.selector.attr("src", "/img/icon/dataTable_next.png");
        _p[cpName].lastPage.selector.click(function(){ _p.switchPage(_p.currentPageCount); });


        _p[cpName].recsPerPageInput = new si4.widget.si4Element({parent:_p[cpName].selector,
            tagName:"input", tagClass:"inline vmid dataTable_recsPerPageInput"});
        _p[cpName].recsPerPageInput.selector[0].dsControl = _p[cpName];
        _p[cpName].recsPerPageInput.selector.keypress(function(e){
            if (e.which == 13) {
                _p.setPaginatorRowsPerPage(this.dsControl.recsPerPageInput.selector.val());
            }
        });
        _p[cpName].recsPerPageInput.selector.val(_p.dataSource.pageCount);

        _p[cpName].recsPerPageText = new si4.widget.si4Element({parent:_p[cpName].selector, tagClass:"inline vmid"});
        _p[cpName].recsPerPageText.selector.html(si4.translate("dataTable_perPage"));


        // Export
        if (_p.canExportXls) {
            _p[cpName].exportXlsDiv = new si4.widget.si4Element({parent:_p[cpName].selector, tagClass:"inline exportButton vmid", hint:"Export Excel file (xlsx)"});
            _p[cpName].exportXlsImg = new si4.widget.si4Element({parent:_p[cpName].exportXlsDiv.selector, tagName:"img", tagClass:"icon16 vmid"});
            _p[cpName].exportXlsImg.selector.attr("src", "/img/icon/exportXls.png");
            _p[cpName].exportXlsDiv.selector.click(function(){
                if (!_p.dataSource) return;
                _p.dataSource.exportXls();
            });
        }
        if (_p.canExportCsv) {
            _p[cpName].exportCsvDiv = new si4.widget.si4Element({parent:_p[cpName].selector, tagClass:"inline exportButton vmid", hint:"Export Comma separated values file (csv)"});
            _p[cpName].exportCsvImg = new si4.widget.si4Element({parent:_p[cpName].exportCsvDiv.selector, tagName:"img", tagClass:"icon16 vmid"});
            _p[cpName].exportCsvImg.selector.attr("src", "/img/icon/exportCsv.png");
            _p[cpName].exportCsvDiv.selector.click(function(){
                if (!_p.dataSource) return;
                _p.dataSource.exportCsv();
            });
        }

        if (_p.canExportPdf) {
            _p[cpName].exportPdfDiv = new si4.widget.si4Element({parent:_p[cpName].selector, tagClass:"inline exportButton vmid", hint:"Export as PDF (pdf)"});
            _p[cpName].exportPdfImg = new si4.widget.si4Element({parent:_p[cpName].exportPdfDiv.selector, tagName:"img", tagClass:"icon16 vmid"});
            _p[cpName].exportPdfImg.selector.attr("src", "/img/icon/pdf.png");
            _p[cpName].exportPdfDiv.selector.click(function(){
                if (!_p.dataSource) return;
                _p.dataSource.exportPdf();
            });
        }

        // Filter
        if (_p.filter.enabled) {
            _p[cpName].filterDiv = new si4.widget.si4Element({parent:_p[cpName].selector, tagClass:"inline filterButton vmid"});
            _p[cpName].filterImg = new si4.widget.si4Element({parent:_p[cpName].filterDiv.selector, tagName:"img", tagClass:"icon16 vmid"});
            _p[cpName].filterImg.selector.attr("src", "/img/icon/dataTable_filter.png");
            _p[cpName].filterSpan = new si4.widget.si4Element({parent:_p[cpName].filterDiv.selector, tagName:"span", tagClass:"vmid"});
            _p[cpName].filterSpan.selector.html("Filter");
            _p[cpName].filterDiv.selector.click(function(){ _p.toggleFilter(); });
        }

        if (_p.customControlls) {
            _p.customControlls(_p, cpName);
        }
    };

    this.switchPage = function(pageIdx, noPageCountCheck, noRefresh) {
        if (isNaN(pageIdx*1)) return;
        _p.currentPage = pageIdx;
        if (_p.currentPage > _p.currentPageCount && !noPageCountCheck) _p.currentPage = _p.currentPageCount;
        if (_p.currentPage < 1) _p.currentPage = 1;
        _p.dsControl.pageInput.selector.val(_p.currentPage);
        _p.dsControlBottom.pageInput.selector.val(_p.currentPage);
        if (_p.dataSource) {
            _p.dataSource.pageStart = (_p.currentPage -1) * _p.dataSource.pageCount;
            if (!noRefresh) _p.refresh();
        }
        this.replacePaginationUrlParams();
    };

    this.replacePaginationUrlParams = function() {
        if (_p.replaceUrlPagination && _p.dataSource) {
            var pageName = window.location.pathname;
            var params = si4.queryStringToJson(window.location.search);
            params["page"] = _p.currentPage;
            params["size"] = _p.rowsPerPage;
            if (_p.dataSource.sortField) params["sort"] = _p.dataSource.sortField; else delete params["sort"];
            if (_p.dataSource.sortOrder) params["order"] = _p.dataSource.sortOrder; else delete params["order"];
            if (Object.keys(_p.dataSource.filter).length) {
                params["filter"] = btoa(JSON.stringify(_p.dataSource.filter));
            } else {
                delete params["filter"];
            }
            var url = pageName+si4.jsonToQueryString(params);
            window.history.replaceState(null, null, url);
        }
    };

    this.goToFirstPage = function(noRefresh) {
        _p.switchPage(1, true, noRefresh);
    };

    this.goToLastPage = function(noRefresh) {
        var lastPage = _p.currentPageCount;
        //alert(_p.rowCount+" "+_p.dataSource.pageCount+" "+(_p.rowCount % _p.dataSource.pageCount == 0));
        if (_p.rowCount % _p.dataSource.pageCount == 0) lastPage++;
        _p.switchPage(lastPage, true, noRefresh);
    };

    this.toggleFilter = function(bool){
        _p.filter.visible = (bool === undefined) ? !_p.filter.visible : bool;

        if (_p.filter.visible) {
            _p.filterRow.display();
            _p.filterRow.recalculateInputs();
        } else {
            _p.filterRow.displayNone();
        }

        //si4.dump(_p.getValue(), 0);
    };

    this.setColumnVisible = function(colName, visible) {
        // Header row
        if (_p.headerRow.fields[colName])
            if (visible) _p.headerRow.fields[colName].display(); else _p.headerRow.fields[colName].displayNone();

        // Filter row
        if (_p.filterRow.fields[colName])
            if (visible) _p.filterRow.fields[colName].display(); else _p.filterRow.fields[colName].displayNone();

        // Data rows
        for (var i = 0; i < _p.rows.length; i++) {
            var field = _p.rows[i].fields[colName];
            if (!field) continue;
            if (visible) field.display(); else field.displayNone();
        }
    };

    this.expandAllRows = function() {
        if (!_p.canExpand) return;

        return;
        for (var i = 0; i < _p.rows.length; i++)
            if (_p.rows[i].lastRowData && !_p.rows[i].subRowTr.isDisplay())
                _p.rows[i].expandToggleSubRow();
    };

    this.recalculateInputs = function(){
        for (var i = 0; i < _p.rows.length; i++)
            _p.rows[i].recalculateInputs();
    };

    this.applyFilter = function() {
        if (_p.filterRow)
            _p.filter.value = _p.filterRow.getFilterValue();
        if (_p.dataSource) {
            _p.dataSource.filter = _p.filter.value;
        }
        _p.replacePaginationUrlParams();
    };

    this.rowReprValue = function(row, entityTitle, primaryKey){
        if (!entityTitle) entityTitle = _p.entityTitleEdit;
        if (!primaryKey) primaryKey = _p.primaryKey;
        var reprValue = "";
        //var row = _p.getValue();

        if (entityTitle) {
            reprValue = si4.mergePlaceholders(entityTitle, row);
        } else if (primaryKey) {
            reprValue = "Record ";
            for (var i in primaryKey){
                if (reprValue) reprValue += ', ';
                reprValue += row[primaryKey[i]];
            }
        }
        return reprValue;
    };

    this.getEventArgs = function(){
        return { dataTable: _p };
    };


    this.getValueType = function(val) {
        if (jQuery.isNumeric(val)) {
            return 'int';
        }
        return 'str';
    };

    this.getInitValueForType = function(valType) {
        switch (valType) {
            case 'int': return 0;
            case 'delete': return '<img src="/img/delete.png" class="icon16" />';
            case 'expand': return '<img src="/img/expand.png" class="icon12" />';
            default: return '';
        }
    };

    this.createBluePrintFromData = function(tableData, onlyCheckFirstRow) {
        var bluePrint = {
            fields: {}
        };

        if (!tableData ||
            Array.isArray(tableData) && !tableData.length ||
            typeof(tableData) == "object" && !Object.keys(tableData).length)
        {
            if (_p._lastTableData)
                tableData = _p._lastTableData;
            else {
                bluePrint.noData = true;
            }
        }

        // Is bluePrint Modified
        bluePrint.modified = !_p.bluePrint || !_p._lastTableData || !_p._lastTableData.length;
        if (!bluePrint.modified && tableData && tableData[0] && _p._lastTableData && _p._lastTableData[0]) {
            for (var i in tableData[0]) {
                if (_p._lastTableData[0][i] === undefined) {
                    bluePrint.modified = true;
                    break;
                }
            }
            for (var i in _p._lastTableData[0]) {
                if (tableData[0][i] === undefined) {
                    bluePrint.modified = true;
                    break;
                }
            }
        }
        _p._lastTableData = tableData;

        // Expand subDataTable
        if (_p.subDataTable) {
            var expandName = '_expand';
            var expandBP = si4.mergeObjects({}, _p.fields[expandName]);
            expandBP.fieldKey = expandName;
            expandBP.fieldLabel = 'Expand';
            expandBP.fieldType = 'expand';
            expandBP.canSort = false;
            expandBP.canFilter = false;
            expandBP.editable = false;
            expandBP.initValue = _p.getInitValueForType(expandBP.fieldType);
            bluePrint.fields[expandName] = expandBP;
        }


        for (var i in tableData) {
            var row = tableData[i];

            var fieldNamesOrdered;
            switch (this.fieldOrder) {
                // definedFields ordering strategy
                case "definedFields":
                    // First remember field names, order as defined
                    fieldNamesOrdered = Object.keys(_p.fields);
                    // Then append non-defined fieldNames from row data
                    for (var fieldName in row) {
                        if (fieldNamesOrdered.indexOf(fieldName) === -1) fieldNamesOrdered.push(fieldName);
                    }
                    break;
                // rowData ordering strategy
                case "rowData": default:
                fieldNamesOrdered = Object.keys(row);
                break;
            }


            for (var fieldNameIdx in fieldNamesOrdered) {
                var fieldName = fieldNamesOrdered[fieldNameIdx];
                if (!bluePrint.fields[fieldName]) {
                    var fieldBP = si4.mergeObjects({}, _p.fields[fieldName]);
                    fieldBP.fieldKey = fieldName;
                    fieldBP.fieldLabel = fieldBP.caption ? fieldBP.caption : si4.captionize(fieldName);
                    fieldBP.fieldType = _p.getValueType(row[fieldName]);
                    fieldBP.initValue = _p.getInitValueForType(fieldBP.fieldType);

                    var isFieldDefined = _p.fields[fieldName] ? true : false;
                    if (_p.showOnlyDefinedFields && !isFieldDefined) fieldBP.visible = false;

                    bluePrint.fields[fieldName] = fieldBP;
                }
            }
            if (onlyCheckFirstRow) break;
        }

        // Actions Field
        if (Object.keys(_p.actions).length) {
            var actionName = '_actions';
            var actionBP = si4.mergeObjects({}, _p.fields[actionName]);
            actionBP.fieldKey = actionName;
            actionBP.fieldLabel = 'Actions';
            actionBP.fieldType = 'actions';
            actionBP.canSort = false;
            actionBP.canFilter = false;
            actionBP.editable = false;
            actionBP.actions = _p.actions;
            bluePrint.fields[actionName] = actionBP;
        }

        // Delete Button
        if (_p.canDelete) {
            var fieldName = '_delete';
            var fieldBP = si4.mergeObjects({}, _p.fields[fieldName]);
            fieldBP.fieldKey = fieldName;
            fieldBP.fieldLabel = si4.translate("dataTable_columnDelete");
            fieldBP.fieldType = 'delete';
            fieldBP.canSort = false;
            fieldBP.canFilter = false;
            fieldBP.editable = false;
            fieldBP.tagClass = "dataTable_deleteField";
            fieldBP.initValue = _p.getInitValueForType(fieldBP.fieldType);
            bluePrint.fields[fieldName] = fieldBP;
        }

        return bluePrint;
    };

    this.createEmptyRow = function(){
        var result = {};
        if (_p.bluePrint) {
            for (var i in _p.bluePrint.fields) {
                var fieldBP = _p.bluePrint.fields[i];
                result[fieldBP.fieldKey] = _p.getInitValueForType(fieldBP.fieldType);
            }
        }
        return result;
    };

    this.hideSubrows = function() {
        for (var i in _p.rows)
            if (_p.rows[i].subRowTr && _p.rows[i].subRowTr.isDisplay())
                _p.rows[i].subRowTr.displayNone();
    };

    this.setValue = function(tableData) {
        if (!tableData) return;
        for (var i = 0; i < _p.rows.length; i++){
            if (_p.rows[i] && tableData[i]) {
                _p.rows[i].setValue(tableData[i]);
            } else {
                _p.rows[i].hide();
            }
        }
    };

    this.getValue = function(){
        var result = [];
        for (var i in _p.rows) {
            if (_p.rows[i].active)
                result.push(_p.rows[i].getValue());
        }
        return result;
    };

    this.info = function(infoText) {
        _p.infoDiv.selector.html(infoText);
        _p.infoDiv.selector.fadeIn(si4.defaults.fadeTime);
    };

    this.reconstruct = function(args){
        console.log("dataTable reconstruct");
        _p.bluePrint = _p.createBluePrintFromData(args.data);
        if (_p.bluePrint.modified || !_p.constructed) {
            _p.createTable();
            _p.constructed = true;
        }

        _p.table.display();
        if (_p.bluePrint.noData) {
            if (_p.hideNoData) {
                _p.table.displayNone();
            } else {
                _p.info(si4.translate("dataTable_noData"));
            }
        }
    };

    this.setPaginator = function(rowCount) {
        console.log("setPaginator", rowCount);
        if (!rowCount) return;
        if (!_p.dsControl) return;
        if (!_p.dsControlBottom) return;
        var maxRowCountExceeded = false;

        if (this.maxRecordCount && rowCount > this.maxRecordCount) {
            maxRowCountExceeded = true;
            rowCount = this.maxRecordCount;
        }

        if (_p.dataSource && _p.dataSource.pageCount) _p.rowsPerPage = _p.dataSource.pageCount;

        _p.rowCount = rowCount;
        _p.currentPageCount = Math.floor((rowCount -1) /_p.rowsPerPage) +1;
        //si4.dump({ rowCount: _p.rowCount, rowsPerPage: _p.rowsPerPage, currentPageCount: _p.currentPageCount, currentPage: _p.currentPage });

        if (_p.currentPage > _p.currentPageCount) _p.currentPage = _p.currentPageCount;

        _p.currentPageCountDisplay = _p.currentPageCount;
        if (maxRowCountExceeded) _p.currentPageCountDisplay += "+";

        _p.dsControl.pageInput.selector.val(_p.currentPage);
        _p.dsControl.pageCount.selector.val(_p.currentPageCountDisplay);
        _p.dsControlBottom.pageInput.selector.val(_p.currentPage);
        _p.dsControlBottom.pageCount.selector.val(_p.currentPageCountDisplay);
    };

    this.setPaginatorRowsPerPage = function(newMaxRows) {
        if (newMaxRows > 300) {
            if (!confirm("Datatable will prepare to display "+newMaxRows+" rows. This will take a few seconds. Do you wish to continue?")) return;
        }

        _p.rowsPerPage = newMaxRows;
        _p.dataSource.pageCount = newMaxRows;
        _p.dataSource.pageStart = 0;
        _p.dsControl.recsPerPageInput.selector.val(newMaxRows);
        _p.dsControlBottom.recsPerPageInput.selector.val(newMaxRows);
        //_p.setPaginator(_p.rowCount);

        this.replacePaginationUrlParams();

        _p.createRows();
        _p.refresh();
    };

    this.findLastVisibleRow = function() {
        var row = null;
        for (var i in _p.rows)
            if (_p.rows[i].isDisplay()) row = _p.rows[i];
        return row;
    };

    this.feedData = function(args) {

        console.log("feedData", args);
        _p.trigger('dataFeed', args);

        _p.reconstruct(args);
        _p.setValue(args.data);
        _p.setPaginator(args.rowCount);

        _p.trigger('dataFeedComplete', args);

        if (_p.firstFeed) {
            _p.trigger('firstFeedComplete', args);
            if (_p.dataSource && _p.dataSource.filter && _p.filterRow) {
                _p.filterRow.setFilterValue(_p.dataSource.filter);
            }
            if (_p.filter.value && _p.filter.autoApply){
                _p.applyFilter();
                _p.refresh();
            }

            _p.firstFeed = false;
        }

        if (_p.initExpandAll) {
            _p.expandAllRows();
        }
    };

    this.initAndPopulate = function(tableData){
        if (tableData) {
            _p.feedData(tableData);
        }
        else {
            if (!_p.dataSource) return;

            if (_p.replaceUrlPagination) {
                var params = si4.queryStringToJson(window.location.search);
                var size = params["size"] ? parseInt(params["size"]) : _p.rowsPerPage;
                if (params["page"]) {
                    _p.currentPage = parseInt(params["page"]);
                    _p.rowsPerPage = size;
                    _p.dataSource.pageStart = (_p.currentPage -1) * size;
                    _p.dataSource.pageCount = size;
                    if (params["sort"]) _p.dataSource.sortField = params["sort"];
                    if (params["order"]) _p.dataSource.sortOrder = params["order"];
                    if (params["filter"]) {
                        var filter = JSON.parse(atob(params["filter"]));
                        _p.dataSource.filter = filter;
                        _p.filter.visible = true;
                    }
                }
            }

            _p.dataSource.callbacks.feedData = _p.feedData;
            _p.dataSource.select();
        }
    };

    this.refresh = function(resetPagination) {
        if (!_p.dataSource) return;

        if (resetPagination) {
            _p.goToFirstPage(true);
        }

        _p.infoDiv.selector.css("display", "none");
        if (_p.subDataTable) _p.hideSubrows();
        _p.dataSource.select();
    };


    // if EditorModule given, bind edit events
    if (_p.editorModuleArgs) {
        _p.onFieldClick(function (args) {
            if (typeof(_p.selectCallback) == "function") {
                _p.selectCallback(args);
                if (_p.tabPage) {
                    _p.tabPage.destroyTab();
                }
                else if (_p.parent.tabPage && _p.parent.tabPage.parentTab) {
                    _p.parent.tabPage.parentTab.destroyTab();
                }
                else if (_p.parent.tabPage) {
                    _p.parent.tabPage.destroyTab();
                }

                return;
            }

            var fieldKey = args.field.fieldKey;
            var editorModuleArgs = _p.editorModuleArgs;
            var row = args.row.getValue();
            if (_p.editorModuleArgs[fieldKey]) {
                row = si4.mergeObjects(row, row[fieldKey]);
                editorModuleArgs = _p.editorModuleArgs[fieldKey];
            }
            if (typeof(editorModuleArgs.parseRow) == "function") row = editorModuleArgs.parseRow(row);

            //var tabName = args.row.reprValue();
            editorModuleArgs = si4.mergeObjects({
                entityTitle: _p.entityTitleEdit,
                primaryKey: _p.primaryKey
            }, editorModuleArgs);

            editorModuleArgs = si4.mergeObjects(editorModuleArgs, {
                //newTab: _p.rowReprValue(row, editorModuleArgs.entityTitle, editorModuleArgs.primaryKey),
                row: row
            });

            editorModuleArgs.onClosed = function(args) {
                _p.refresh();
                window.scrollTo(0,0);
            };

            if (_p.dataSource && _p.dataSource.staticData)
                editorModuleArgs.staticData = si4.mergeObjects(_p.dataSource.staticData, editorModuleArgs.staticData);
            if (_p.primaryKey)
                for (var pkIdx in _p.primaryKey) {
                    editorModuleArgs[_p.primaryKey[pkIdx]] = row[_p.primaryKey[pkIdx]];
                }

            if (_p.tabPage) editorModuleArgs.tabPage = _p.tabPage;
            //console.log(editorModuleArgs);
            si4.loadModule(editorModuleArgs);
        });
    }

    // if canDelete, bind delete click event
    if (_p.canDelete) {
        _p.onFieldClick(function(args){
            if (args.field.fieldKey == "_delete") {
                if (confirm(si4.translate("dataTable_confirmDelete", {record:args.row.reprValue()}))) {
                    var response = _p.dataSource.delete(args.row.getValue());
                    if (response) _p.setValue(response.data);
                }
            }
        });
    }

    // if canExpand, bind expand click event
    if (_p.canExpand) {
        _p.onFieldClick(function(args){
            if (args.field.fieldKey == "_expand") {
                args.row.expandToggleSubRow();
            }
        });
    }
    // Sort
    _p.onHeaderFieldClick(function(args){

        if (!_p.dataSource) return;
        if (!args.field.canSort) return;

        if (_p.dataSource.sortField == args.field.fieldKey) {
            // Change order
            _p.dataSource.sortOrder = (_p.dataSource.sortOrder == "asc") ? "desc" : "asc"
        } else {
            // Change sort field
            _p.dataSource.sortField = args.field.fieldKey;
            _p.dataSource.sortOrder = "asc";
        }
        args.field.setSort(_p.dataSource.sortOrder);

        _p.replacePaginationUrlParams();

        _p.refresh();
    });


    if (_p.dataSource) {
        _p.dataSource.callbacks.feedData = _p.feedData;

        if (_p.initRefresh) {
            _p.initAndPopulate();
        }
    }
};


si4.widget.si4DataTableRow = function(tableSectionWnd, args){

    // Init
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons({parent:tableSectionWnd, tagName:"tr"});
    this.selector.addClass("si4DataTableRow");

    this._ebase = si4.object.si4EventBase;
    this._ebase();

    this.fields = {};
    this.active = false;
    this.subRowTr = null;
    this.tempClassName = "";

    this.isModified = false;

    // Settings
    this.dataTable = si4.getArg(args, "dataTable", null);

    this.headerRow = si4.getArg(args, "headerRow", false);
    this.filterRow = si4.getArg(args, "filterRow", false);
    this.subRow = si4.getArg(args, "subRow", false);
    this.parentRowTr = si4.getArg(args, "row", null);
    this.dataRow = !this.headerRow && !this.filterRow && !this.subRow;


    // Implementation
    if (!this.subRow && this.dataTable.hoverRows) this.selector.addClass("hoverable");
    if (this.dataRow || this.subRow) this.displayNone();
    if (this.filterRow && !this.dataTable.filter.visible) this.displayNone();

    this.addField = function(colName, colValue, args) {
        _p.fields[colName] = new si4.widget.si4DataTableField(_p.selector, si4.mergeObjects({dataTable:_p.dataTable, row:_p,
            fieldKey:colName, fieldValue:colValue}, args));
    };

    this.show = function(){
        _p.display();
        //if (_p.subRowTr) _p.subRowTr.show();
        _p.active = true;
    };

    this.hide = function(){
        _p.displayNone();
        if (_p.subRowTr) _p.subRowTr.hide();
        _p.active = false;
    };

    this.setValue = function(rowData){

        if (_p.tempClassName) {
            _p.selector.removeClass(_p.tempClassName);
            _p.tempClassName = "";
        }
        _p.show();
        _p.lastRowData = rowData;

        for (var fieldName in rowData) {
            if (_p.fields[fieldName]) _p.fields[fieldName].setValue(rowData[fieldName], rowData);
        }

        _p.dataTable.trigger('rowSetValue', si4.mergeObjects(_p.getEventArgs(), {rowData:rowData}));
    };

    this.getValue = function(){
        var result = {};
        for (var i in _p.fields) {
            if (_p.fields[i].fieldKey[0] == '_') continue;
            result[_p.fields[i].fieldKey] = _p.fields[i].getValue();
        }
        return result;
    };

    this.updateRow = function() {
        if (!_p.dataTable.dataSource) return;
        _p.dataTable.dataSource.updateRow({orig:_p.lastRowData, row:_p.getValue()});
        _p.isModified = false;
    };

    this.reprValue = function(){
        return _p.dataTable.rowReprValue(_p.getValue());
    };

    this.getFilterValue = function() {
        var result = {};
        if (!_p.dataTable.filterRow) return result;
        for (var i in _p.dataTable.filterRow.fields) {
            var filterField = _p.dataTable.filterRow.fields[i];
            var filterFieldValue = filterField.getFilterValue();
            if (filterFieldValue)
                result[filterField.fieldKey] = filterFieldValue;
        }
        return result;
    };

    this.setFilterValue = function(filterValue){

        for (var i in _p.dataTable.filterRow.fields) {
            if (filterValue[i])
                _p.dataTable.filterRow.fields[i].setFilterValue(filterValue[i]);
        }
    };

    this.createSubRow = function() {
        _p.subRowTr = new si4.widget.si4DataTableRow(tableSectionWnd, si4.mergeObjects(_p.getEventArgs(), {
            subRow: true }));
        _p.subRowTr.addField('subField', '', si4.mergeObjects(_p.getEventArgs(), {
            colSpan: Object.keys(_p.fields).length, subRowField: true }));
        var subRowField = _p.subRowTr.fields['subField'];
        subRowField.selector.css("padding-left", "50px");

        _p.subRowTr.subDataTable = new si4.widget.si4DataTable(si4.mergeObjects(_p.dataTable.subDataTable, {
            parent: subRowField.selector, initRefresh: false, hideNoData: true
        }));

    };

    this.expandToggleSubRow = function() {
        //_p.subRowTr.expandToggle();
        if (_p.subRowTr.isDisplay()) {
            _p.subRowTr.displayNone();
        } else {
            _p.subRowTr.display();
            if (_p.subRowTr.subDataTable.dataSource) {
                _p.subRowTr.subDataTable.dataSource.staticData.parentRow = _p.getValue();
            }
            _p.subRowTr.subDataTable.initAndPopulate();
            if (_p.subRowTr.subDataTable.rows)
                _p.subRowTr.subDataTable.recalculateInputs();
        }
    };

    this.getEventArgs = function() {
        return si4.mergeObjects(_p.dataTable.getEventArgs(), { row: _p });
    };

    this.recalculateInputs = function(){
        if (_p.filterRow)
            for (var i in _p.fields) _p.fields[i]._recalcInputWidth();

        for (var i in _p.fields) {
            if (_p.fields[i].editable && _p.fields[i].dataField)
                _p.fields[i]._recalcInputWidth();
        }
    };

    this.addTempClassName = function(className) {
        if (_p.tempClassName) _p.selector.removeClass(_p.tempClassName);
        _p.tempClassName = className;
        _p.selector.addClass(_p.tempClassName);
    };

    // Bind Events
    this.selector.click(function(e){
        if (_p.headerRow) _p.dataTable.trigger("headerRowClick", _p.getEventArgs());
        if (!_p.headerRow) _p.dataTable.trigger("dataRowClick", _p.getEventArgs());
        _p.dataTable.trigger("rowClick", _p.getEventArgs());
        //e.preventDefault();
    });
    this.selector.dblclick(function(e){
        if (_p.headerRow) _p.dataTable.trigger("headerRowDoubleClick", _p.getEventArgs());
        if (!_p.headerRow) _p.dataTable.trigger("dataRowDoubleClick", _p.getEventArgs());
        _p.dataTable.trigger("rowDoubleClick", _p.getEventArgs());
        window.getSelection().removeAllRanges();
        e.preventDefault();
    });
    this.selector.bind("contextmenu", function(e){
        if (_p.headerRow) _p.dataTable.trigger("headerRowRightClick", _p.getEventArgs());
        if (!_p.headerRow) _p.dataTable.trigger("dataRowRightClick", _p.getEventArgs());
        _p.dataTable.trigger("rowRightClick", _p.getEventArgs());
        if (_p.dataTable.preventContextMenu) { e.preventDefault(); return false; }
    });
};


si4.widget.si4DataTableField = function(tableRowWnd, args) {

    // Init
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons({parent:tableRowWnd, tagName:"td" });

    this.fieldKey = si4.getArg(args, "fieldKey", null);
    this.fieldValue = si4.getArg(args, "fieldValue", null);
    this.canSort = si4.getArg(args, "canSort", true);
    this.canFilter = si4.getArg(args, "canFilter", this.canSort);
    this.filterOptions = si4.getArg(args, "filterOptions", null);
    this.filterClassName = si4.getArg(args, "filterClassName", null);
    this.row = si4.getArg(args, "row", null);
    this.dataTable = si4.getArg(args, "dataTable", this.row ? this.row.dataTable : null);
    this.clearValue = si4.getArg(args, "clearValue", "");
    this.visible = si4.getArg(args, "visible", typeof(this.fieldKey) == "string" && this.fieldKey.substr(0,2) != "__");
    this.tagClass = si4.getArg(args, "tagClass", "");
    this.caption = si4.getArg(args, "caption", "");
    this.hint = si4.getArg(args, "hint", null);
    this.hintF = si4.getArg(args, "hintF", null);
    this.width = si4.getArg(args, "width", null);
    this.editable = si4.getArg(args, "editable", this.dataTable.editable);
    this.colSpan = si4.getArg(args, "colSpan", null);
    this.editorType = si4.getArg(args, "editorType", "text");
    this.selectOptions = si4.getArg(args, "selectOptions", null);
    this.updateOnEnter = si4.getArg(args, "updateOnEnter", true);
    this.displayType = si4.getArg(args, "displayType", "");
    this.maxCharLength = si4.getArg(args, "maxCharLength", null);
    this.valueTranslatePrefix = si4.getArg(args, "valueTranslatePrefix", null);

    this.headerField = si4.getArg(args, "headerField", this.row ? this.row.headerRow : false);
    this.filterField = si4.getArg(args, "filterField", this.row ? this.row.filterRow : false);
    this.subRowField = si4.getArg(args, "subRowField", false);
    this.dataField = !this.headerField && !this.filterField && !this.subRowField;

    this.cellDataTable = si4.getArg(args, "cellDataTable", null);
    this.formView = si4.getArg(args, "formView", null);
    this.actions = si4.getArg(args, "actions", null);

    this.format = si4.getArg(args, "format", null);

    //this.autoSplitPipes = si4.getArg(args, "autoSplitPipes", ", ");

    if (!this.subRowField) {
        this.valueDiv = new si4.widget.si4Element({parent:this.selector, tagClass:"inline"});
        if (this.displayType == "button" && this.dataField)
            this.valueDiv.setGradient("red");

        if (this.width) {
            this.selector.css("width", this.width+"px");
            this.valueDiv.selector.css("width", this.width+"px");
        }
    }

    if (this.displayType == "button")
        this.selector.addClass("dataTable_td_button");

    this.hasInput = false;

    if (this.colSpan) this.selector.attr("colSpan", this.colSpan);

    if (this.headerField) {
        // Header field
        this.sortImg = new si4.widget.si4Element({parent:this.selector, tagName:"img", tagClass:"dataTableSortIcon icon8"});
        this.sortImg.selector.css("display", "none");
        if (this.canSort)
            this.selector.addClass("sortable");
    } else if (this.filterField) {
        // Filter field

        if (this.filterOptions) {
            this.input = new si4.widget.si4Input({parent:this.valueDiv.selector, name:this.fieldKey,
                type: "select", values: this.filterOptions, addEmptyOption: true,
                caption:false, readOnly:!this.canFilter, showModified:false});
            this.input.selector.addClass('dataTableFilterInput');
            if (this.filterClassName) this.input.selector.addClass(this.filterClassName);
            if (!this.canFilter) this.input.selector.addClass('disabled');
            this.input.input.selector.change(function(e) {
                _p.dataTable.applyFilter();
                _p.dataTable.refresh();
            });
        } else {
            this.input = new si4.widget.si4Input({parent:this.valueDiv.selector, name:this.fieldKey,
                caption:false, readOnly:!this.canFilter, showModified:false});
            this.input.selector.addClass('dataTableFilterInput');
            if (this.filterClassName) this.input.selector.addClass(this.filterClassName);
            if (!this.canFilter) this.input.selector.addClass('disabled');
            this.input.onEnterPressed(function(e) {
                _p.dataTable.applyFilter();
                _p.dataTable.refresh();
            });
            this.hasInput = true;
        }
    } else if (this.subRowField) {
        // Subrow Field
    } else {
        // Data field
        if (this.editable && this.dataField) {

            if (this.editorType == "checkbox")
                this.selector.addClass('dataTableCheckboxCell');
            if (this.editorType == "input")
                this.selector.addClass('dataTableInputCell');

            this.input = new si4.widget.si4Input({parent:this.valueDiv.selector, name:this.fieldKey,
                caption:false, showModified:true, type:this.editorType, values:this.selectOptions });
            this.input.selector.addClass('dataTableValueInput');

            this.input.onModified(function(modArgs) {
                if (modArgs.modified) _p.row.isModified = true;
            });

            if (this.editorType == "checkbox") {
                this.input.selector.click(function(e) {
                    //$(this).prop('checked', true);
                    //si4.dump($._data( $(this)[0], "events" ), 10);
                    //var foo = $._data( $(this)[0], "events" );
                    //si4.dump(foo.click[0].handler, 10);
                    //$(this).prop("checked", !$(this).prop("checked"));
                });
            }

            if (this.editorType == "input") {
            }

            //if (!this.canEdit) this.input.selector.addClass('disabled');
            if (this.updateOnEnter) {
                this.input.onEnterPressed(function(e) {
                    //_p.dataTable.applyFilter();
                    //_p.dataTable.refresh();
                    //si4.dump(_p.row.getValue());
                    _p.row.updateRow();
                });
            }
            this.hasInput = true;
        } else {
            if (this.dataTable.hoverCells) this.selector.addClass("hoverable");
        }
    }

    this.setSort = function(sortOrder){
        var sortFieldKey = _p.fieldKey
        var field = _p.dataTable.headerRow.fields[sortFieldKey];
        sortOrder = (sortOrder == "asc") ? "asc" : "desc";

        for (var i in field.row.fields) field.row.fields[i].sortImg.selector.css("display", "none");
        field.sortImg.selector.attr("src", "/img/icon/dataTable_"+sortOrder+".png");
        field.sortImg.selector.css("display", "inline-table");
    };

    this.setValue = function(fieldValue, rowValue){
        _p.fieldValue = fieldValue;

        if (_p.dataField && _p.cellDataTable) {
            _p.valueDiv.selector.empty();
            _p.cellDataTableInstance = new si4.widget.si4DataTable({
                parent: _p.valueDiv.selector,
                canInsert: false,
                canDelete: false
            });
            _p.cellDataTable.initAndPopulate(_p.fieldValue);

        } else if (_p.dataField && _p.formView) {
            _p.valueDiv.selector.empty();
            _p.formViewInstance = new si4.widget.si4Form(
                si4.mergeObjects({parent:_p.valueDiv.selector, captionWidth:"100px"}, _p.formView));
            _p.formViewInstance.selector.addClass("dataTableFormView");
            for (var fKey in _p.fieldValue) {
                var fVal = _p.fieldValue[fKey];
                if (typeof(fKey) == "string" && fKey.substr(0,3) == "---")
                    _p.formViewInstance.addHr();
                else {
                    if (typeof(fVal) == "object" && typeof(fVal[0]) == "object") {
                        var newval = "";
                        for (var k in fVal) {
                            if (newval) newval += "<br />";
                            newval += fVal[k].value;
                        }
                        fVal = newval;
                    }
                    _p.formViewInstance.addInput({name:fKey, type:"flat", value:fVal, readOnly:true});

                    /*
                     var inputArgs = {name:fKey, type:"flat", value:fVal, readOnly:true};
                     if (fVal && fVal[0] && fVal[0].codeId) {
                     inputArgs.isArray = true;
                     inputArgs.withCode = si4.codes.pubSource;
                     }
                     _p.formViewInstance.addInput(inputArgs);
                     */
                }
            }
        } else if (_p.dataField && _p.actions) {
            _p.valueDiv.actions = {};
            for (var aKey in _p.actions) {
                var actionArgs = _p.actions[aKey];
                var actionType = si4.getArg(actionArgs, 'type', 'link');
                var actionLabel = si4.getArg(actionArgs, 'label', si4.captionize(aKey));
                var actionOnClick = si4.getArg(actionArgs, 'onClick', function(args) {});
                var action;
                switch (actionType) {
                    case "link": default:
                    action = new si4.widget.si4Element({parent:_p.valueDiv.selector});
                    action.selector.html(actionLabel);
                    break;
                    case "button":
                        action = new si4.widget.si4Input({parent:_p.valueDiv.selector, type:'button'});
                        action.setValue(actionLabel);
                        break;
                }
                action.selector[0].actionOnClick = actionOnClick;
                action.selector.click(function(e){
                    if (!this.actionOnClick) return;
                    var clickArgs = si4.mergeObjects(_p.getEventArgs(), {action: actionArgs});
                    this.actionOnClick(clickArgs);
                });
                _p.valueDiv.actions[aKey] = action;
            }
            //_p.valueDiv.selector.html('Actions!');
            //} else if (_p.dataField && _p.editorType == "input") {

        } else if (_p.filterField) {

        } else if (_p.headerField) {
            _p.valueDiv.selector.html(_p.fieldValue);
        } else {
            // Replace pipes
            var fVal = _p.fieldValue;
            //if (_p.autoSplitPipes) fVal = si4.replacePipes(fVal, _p.autoSplitPipes, 0);
            if (_p.maxCharLength) fVal = si4.clipString(fVal, _p.maxCharLength);

            if (_p.editable && _p.dataField) {
                _p.input.setValue(fVal);
                _p.input.calcModified();
                //_p.valueDiv.selector.html(fVal);
            } else {
                if (!_p.subRowField) {
                    if (_p.valueTranslatePrefix && fVal) fVal = si4.translate(_p.valueTranslatePrefix+fVal);
                    if (typeof(_p.format) == "function") fVal = _p.format(fVal, rowValue, _p);
                    _p.valueDiv.selector.html(fVal);
                }
            }
        }
    };

    this.getValue = function(){
        if (_p.hasInput)
            return _p.input.getValue();

        return _p.fieldValue;
    };

    this.getFilterValue = function() {
        if (!_p.dataTable.filterRow) return null;
        var filterField = _p.dataTable.filterRow.fields[_p.fieldKey];
        if (filterField && filterField.input)
            return filterField.input.getValue();
        else
            return null;
    };

    this.setFilterValue = function(filterValue) {
        var filterField = _p.dataTable.filterRow.fields[_p.fieldKey];
        filterField.input.setValue(filterValue);
    };

    this.getEventArgs = function(){
        return si4.mergeObjects(_p.row.getEventArgs(), { field: _p });
    };

    this._recalcInputWidth = function(){
        if (_p.hasInput && _p.editorType != "select") {
            _p.input.input.selector.css("width", "");
            var newWidth = _p.selector.width() +8;
            if (_p.width && newWidth < _p.width) newWidth = _p.width;
            _p.input.input.selector.css("width", newWidth+"px");
        }
    };

    // Bind events
    this.selector.click(function(e){
        if (_p.headerField) _p.dataTable.trigger("headerFieldClick", _p.getEventArgs());
        if (_p.dataField) _p.dataTable.trigger("dataFieldClick", _p.getEventArgs());
        _p.dataTable.trigger("fieldClick", _p.getEventArgs());
        //e.preventDefault();
    });
    this.selector.dblclick(function(e){
        if (_p.headerField) _p.dataTable.trigger("headerFieldDoubleClick", _p.getEventArgs());
        if (_p.dataField) _p.dataTable.trigger("dataFieldDoubleClick", _p.getEventArgs());
        _p.dataTable.trigger("fieldDoubleClick", _p.getEventArgs());
        e.preventDefault();
        return false;
    });
    this.selector.bind("contextmenu", function(e){
        if (_p.headerField) _p.dataTable.trigger("headerFieldRightClick", _p.getEventArgs());
        if (_p.dataField) _p.dataTable.trigger("dataFieldRightClick", _p.getEventArgs());
        _p.dataTable.trigger("fieldRightClick", _p.getEventArgs());
        if (_p.dataTable.preventContextMenu) { e.preventDefault(); return false; }
    });

    // Set initial value
    this.setValue(this.fieldValue);

    if (this.hint) this.setHint(this.hint);
    if (this.hintF && _p.dataField) {
        this.setHint("");
        this.showHint = function(){ _p.hintF(_p.getEventArgs()); }
    }
    if (!this.visible) this.displayNone();
    if (this.tagClass) this.selector.addClass(this.tagClass);
    if (this.filterField || (this.editable && this.dataField)) this.dataTable.onDataFeedComplete(_p._recalcInputWidth);
};


si4.widget.si4DataTableDataSource = function(args) {
    // Init
    var _p = this;
    this._cons = si4.object.si4EventBase;
    this._cons();

    this.moduleName = si4.getArg(args, "moduleName", null);
    this.methodNames = si4.getArg(args, "methodNames", {
        select:'dataTableSelect',
        delete:'dataTableDelete',
        updateRow:'dataTableUpdateRow',
        exportXls:'dataTableExportXls',
        exportCsv:'dataTableExportCsv',
        exportPdf:'dataTableExportPdf'
    });

    this.selectF = si4.getArg(args, "select", function(){});
    this.deleteF = si4.getArg(args, "delete", function(){});
    this.updateRowF = si4.getArg(args, "updateRow", function(){});
    this.exportXlsF = si4.getArg(args, "exportXls", function(){});
    this.exportCsvF = si4.getArg(args, "exportCsv", function(){});
    this.exportPdfF = si4.getArg(args, "exportPdf", function(){});

    this.filter = si4.getArg(args, "filter", {});
    this.filterMode = si4.getArg(args, "filterMode", "normal");
    this.sortField = si4.getArg(args, "sortField", null);
    this.sortOrder = si4.getArg(args, "sortOrder", "asc");
    this.pageStart = si4.getArg(args, "pageStart", 0);
    this.pageCount = si4.getArg(args, "pageCount", si4.defaults.dataTableRowsPerPage);
    this.editModule = si4.getArg(args, "editModule", null);
    this.staticData = si4.getArg(args, "staticData", {});

    this.callbacks = {};
    this.callbacks.feedData = function(args) { };
    this.aSync = true;

    this.getPaginationData = function(){
        //console.log("getPaginationData", { sortField:_p.sortField, sortOrder:_p.sortOrder, pageStart:_p.pageStart, pageCount:_p.pageCount });
        return { sortField:_p.sortField, sortOrder:_p.sortOrder, pageStart:_p.pageStart, pageCount:_p.pageCount };
    };

    this.getMethodCallData = function(methodName, args) {
        var methodCallData = {
            moduleName:_p.moduleName,
            methodName:methodName,
            aSync:_p.aSync,
            filter: _p.filter,
            filterMode: _p.filterMode,
            data:args
        };

        if (_p.staticData) {
            if (typeof(_p.staticData) === "function") {
                methodCallData.staticData = _p.staticData(_p, methodName, args);
            } else {
                methodCallData.staticData = _p.staticData;
            }
        }

        methodCallData = si4.mergeObjects(methodCallData, _p.getPaginationData());

        return methodCallData;
    };

    this.select = function(args) {
        _p.selectF(_p.getMethodCallData(_p.methodNames.select, args), _p.callbacks.feedData);
    };

    this.delete = function(args) {
        _p.deleteF(_p.getMethodCallData(_p.methodNames.delete, args), _p.callbacks.feedData);
    };

    this.updateRow = function(args) {
        _p.updateRowF(_p.getMethodCallData(_p.methodNames.updateRow, args), _p.callbacks.feedData);
        //return si4.callMethod(_p.getMethodCallData(_p.methodNames.updateRow, args), _p.callbacks.feedData);
    };

    this.exportXls = function(args) {
        _p.exportXlsF(_p.getMethodCallData(_p.methodNames.exportXls, args), function(rArgs) {
            if (rArgs.status && rArgs.link) location.href = rArgs.link;
        });
    };

    this.exportCsv = function(args) {
        _p.exportCsvF(_p.getMethodCallData(_p.methodNames.exportCsv, args), function(rArgs) {
            if (rArgs.status && rArgs.link) location.href = rArgs.link;
        });
    };

    this.exportPdf = function(args) {
        _p.exportPdfF(_p.getMethodCallData(_p.methodNames.exportPdf, args), function(rArgs) {
            if (rArgs.status && rArgs.link) location.href = rArgs.link;
        });
    };
};