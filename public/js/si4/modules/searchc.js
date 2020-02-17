si4.modules.searchc = function(args) {

    //console.log("search", args)

    var naslov0Format = function(fieldValue, rowValue, field) {
        var link = $('<a>'+fieldValue+'</a>');
        link.click(function(){
            console.log("click", fieldValue);
            si4.navigation.switchPage("cit", { zid: rowValue["gtid"], cid: rowValue["cid"] });
        });
        return link;
    };

    var zicCompressedFormat = function(fieldValue, rowValue, field) {
        //console.log("fieldValue", fieldValue);
        //console.log("rowValue", rowValue);
        //console.log("field", field);

        var zicTitle = rowValue && rowValue["zicTitle"] || "";
        fieldValue = '<span>'+fieldValue.replace(zicTitle, '<a>'+zicTitle+'</a>')+'</span>';
        //console.log("fieldValue", fieldValue);

        var result = $(fieldValue);

        result.find("a").click(function() {
            console.log("click", fieldValue);
            si4.navigation.switchPage("zic", { id: rowValue["gtid"] });
        });


        return result;
    };

    this.container = new si4.widget.si4Element({ parent: si4.data.contentElement, tagClass: "defContainer moduleSearch" });

    this.dataTable = new si4.widget.si4DataTable({
        parent: this.container.selector,
        primaryKey: ['ID'],
        //entityTitleNew: si4.lookup[name].entityTitleNew,
        //entityTitleEdit: si4.lookup[name].entityTitleEdit,
        filter: { visible: true },
        dataSource: new si4.widget.si4DataTableDataSource({
            moduleName: "searchc",
            select: si4.api.searchc,
            /*
            exportPdf: function(data, callback) {
                window.open("/zicTablePdf"+location.search);
                //si4.api.zicTable
            },
            */
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
            // Handled in formatters
        },
        //tabPage: args.contentTab,
        fields: {
            zapSt: { caption: si4.translate("field_zapSt"), canSort: true, canFilter: true },
            citatiAuthorsShort: { caption: si4.translate("field_authorsShort"), canSort: true, canFilter: true },
            naslov0: { caption: si4.translate("field_naslov0"), canSort: true, canFilter: true, format: naslov0Format },
            vir: { caption: si4.translate("field_vir"), canSort: true, canFilter: true },
            leto: { caption: si4.translate("field_leto"), canSort: true, canFilter: true },
            zicCompressed: { caption: si4.translate("field_zicCompressed"), canSort: true, canFilter: true,
                tagClass: "zicCompressed", format: zicCompressedFormat },

            /*
            // Sample:
                gtid: 51
                slo: 1
                cid: 1
                COBISSid: null
                sistoryId: null
                cnastrani: "13"
                avtor0: "Bogdandy Armin"
                avtor1: null
                naslov0: "A bird's Eye View on the Science of European Law"
                naslov1: null
                vir: "European Law Journal"
                kraj: null
                zalozba: null
                letnik: "6"
                leto: 2000
                stevilka: "3"
                str: "237-238"
                URL: null
                DOI: null
                STATUS: 1
                DATETIME_ADDED: null
                USER_ID_ADDED: null
                GROUP_ID_ADDED: null
                citatiAuthors: [{GT_ID: 51, C_ID: 1, IDX: 1, IME: "Armin", PRIIMEK: "Bogdandy"}]
                citatiAuthorsShort: "Bogdandy, Armin"
                citatiAuthorsLong: "Bogdandy, Armin"
                citElasticId: 51000001
            */








            /*
            OpTipologija: { caption: si4.translate("field_OpTipologija"), canSort: true, canFilter: true,
                format: tipologyFormat, filterOptions: si4.initData.sif_tipologies,
                filterClassName: "short", hintF: tipologyHintF },
            authorsShort: { caption: si4.translate("field_OpAvtor0"), canSort: true, canFilter: true },
            OpNaslov: { caption: si4.translate("field_OpNaslov"), canSort: true, canFilter: true },
            PvLeto: { caption: si4.translate("field_PvLeto"), canSort: true, canFilter: true, width: 100, },
            PvKraj: { caption: si4.translate("field_PvKraj"), canSort: true, canFilter: true },
            citatiCount: { caption: si4.translate("field_citatiCount"), canSort: true, canFilter: true },
            citiranoCount: { caption: si4.translate("field_citiranoCount"), canSort: true, canFilter: true },
            */

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