$(document).ready(function() {
    applyAutosuggest(document.getElementById("searchInput"), function() {
        return { scope: "search", st: $("#searchForm select[name=st]").val() };
    });
});

function applyAutosuggest(inputEl, getConfigF) {
    //console.log("applyAutosuggest", inputEl);
    var termTemplate = "<span class=\"searchAutocompleteTerm\">%s</span>";
    $(inputEl).autocomplete({
        //source: "/ajax/searchSuggest",
        source: function(request, response) {
            if (getConfigF && typeof(getConfigF) === "function") {
                var reqConf = getConfigF();
                for (var p in reqConf) request[p] = reqConf[p];
            }

            var searchInsideCurrent = $("#searchInsideCurrent").val();
            var searchInsideCurrentChecked = $("#searchInsideCurrent")[0] && $("#searchInsideCurrent")[0].checked;
            if (searchInsideCurrent && searchInsideCurrentChecked) request.parent = searchInsideCurrent;

            $.getJSON("/api/searchSuggest", request, response);
        },
        open: function(e,ui) {
            var acData = $(this).data('ui-autocomplete');
            var styledTerm = termTemplate.replace('%s', acData.term);

            //console.log(acData.menu);
            acData.menu.element.find('.ui-menu-item-wrapper').each(function() {
                var me = $(this);
                //console.log(me.text());
                me.html(me.text().replace(acData.term, styledTerm));
            });
        }
    });
}
