si4.modules.search = function(args) {

    //console.log("search", args)

    var tipologyFormat = function(fieldValue, rowValue, field) {
        var sif = si4.initData.sif_tipologies;
        var strValue = ""+fieldValue;
        //var val = sif && sif[fieldValue] ? sif[fieldValue] : fieldValue;
        //return sif && sif[fieldValue] ? "("+fieldValue+") "+ sif[fieldValue] : fieldValue;

        var val = strValue ? strValue[0]+"."+strValue.substr(1) : "";
        return val;

        //console.log("tipologyFormat", fieldValue, val);
    };

    var tipologyHintF = function(args) {
        var fieldValue = args.field.getValue();
        var sif = si4.initData.sif_tipologies;

        if (!sif || !sif[fieldValue])
            return;

        var hint = sif[fieldValue];
        console.log("tipologyHintF", hint);
        si4.showHint(hint);
        //return hint;
    };

    this.container = new si4.widget.si4Element({ parent: si4.data.contentElement, tagClass: "defContainer moduleSearch" });

    this.dataTable = new si4.widget.si4DataTable({
        parent: this.container.selector,
        primaryKey: ['ID'],
        //entityTitleNew: si4.lookup[name].entityTitleNew,
        //entityTitleEdit: si4.lookup[name].entityTitleEdit,
        filter: { visible: true },
        dataSource: new si4.widget.si4DataTableDataSource({
            moduleName: "search",
            select: si4.api.search,
            exportPdf: function(data, callback) {
                window.open("/zicTablePdf"+location.search);
                //si4.api.zicTable
            },
            staticData : { q: args.q },
            pageCount: 20
        }),
        editorModuleArgs: {
            //moduleName:"Entities/EntityDetails",
            //caller: "entityList"
        },
        canInsert: false,
        canDelete: false,
        selectCallback: function(selArgs) {
            var rowValue = selArgs.row.getValue();
            //console.log("rowValue", rowValue);

            var paramId = rowValue.ID;
            if (rowValue.PRIIMEK) paramId += "-"+rowValue.PRIIMEK;
            if (rowValue.IME) paramId += "-"+rowValue.IME;
            if (rowValue.ROJSTVO_LETO) paramId += "-"+rowValue.ROJSTVO_LETO;
            if (rowValue.SMRT_LETO) paramId += "-"+rowValue.SMRT_LETO;

            si4.navigation.switchPage("zic", { id: paramId });
        },
        //tabPage: args.contentTab,
        fields: {
            ID: { caption: "Id" },
            OpTipologija: { caption: si4.translate("field_OpTipologija"), canSort: true, canFilter: true,
                format: tipologyFormat, filterOptions: si4.initData.sif_tipologies,
                filterClassName: "short", hintF: tipologyHintF },
            authorsShort: { caption: si4.translate("field_OpAvtor0"), canSort: true, canFilter: true },
            OpNaslov: { caption: si4.translate("field_OpNaslov"), canSort: true, canFilter: true },
            PvLeto: { caption: si4.translate("field_PvLeto"), canSort: true, canFilter: true, width: 100, },
            PvKraj: { caption: si4.translate("field_PvKraj"), canSort: true, canFilter: true },
            citatiCount: { caption: si4.translate("field_citatiCount"), canSort: true, canFilter: true },
            citiranoCount: { caption: si4.translate("field_citiranoCount"), canSort: true, canFilter: true },

            //PvZalozba: { caption: si4.translate("field_PvZalozba"), canSort: true, canFilter: true },
            //OpJezik: { caption: si4.translate("field_OpJezik"), canSort: true, canFilter: true },
            //OpSistoryUrnId: { caption: si4.translate("field_OpSistoryUrnId"), canSort: true, canFilter: true },
            //PvCobId: { caption: si4.translate("field_PvCobId"), canSort: true, canFilter: true },
        },
        fieldOrder: "definedFields",
        showOnlyDefinedFields: true,
        maxRecordCount: 10000,
        replaceUrlPagination: true,
        canExportPdf: true,
        filterHint: si4.translate("filter_hint"),
        //cssClass_table: "si4DataTable_table width100percent"
    });
};