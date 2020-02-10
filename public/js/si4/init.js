$(document).ready(function() {

    si4.data.contentElement = $('div#pageHolder');

    si4.api.initialData(null, function(initData) {
        //console.log("initData", initData);
        si4.initData = JSON.parse(initData);

        si4.api.dictionary({ lang: lang }, function(data) {
            var dict = JSON.parse(data);
            si4.translations = dict;
            //console.log("dict", dict);

            $('.translatePlaceholder').each(function(idx, el) {
                $(el).attr("placeholder", si4.translate($(el).attr("data-translatePlaceholder")));
                $(el).removeAttr("data-translatePlaceholder");
                $(el).removeClass("translatePlaceholder");
            });
            $('.translateHtml').each(function(idx, el) {
                $(el).html(si4.translate($(el).attr("data-translateHtml")));
                $(el).removeAttr("data-translateHtml");
                $(el).removeClass("translateHtml");
            });
            $('.translateValue').each(function(idx, el) {
                $(el).attr("value", si4.translate($(el).attr("data-translateValue")));
                $(el).removeAttr("data-translateValue");
                $(el).removeClass("translateValue");
            });

            //si4.data.initView = $('#initView');
            //si4.data.initView.detach();

            //if (!location.pathname || location.pathname == '/')
            //si4.navigation.switchPage("index");

            $("#searchFormZic").submit(function(e) {
                e.preventDefault();
                var q = $("#searchInput").val();
                var t = $("#searchType").val();
                if (q) {
                    switch (t) {
                        case "zic":default:
                            si4.navigation.switchPage("search", { q: q });
                            break;
                        case "cit":
                            si4.navigation.switchPage("searchc", { q: q });
                            break;
                    }

                }
            });

            // Chart
            //var chartCanvas = document.getElementById("chartCanvas");
            //if (chartCanvas) si4.initChart();

            // Logic
            si4.navigation = new si4.si4Navigation();
        });
    });

});
