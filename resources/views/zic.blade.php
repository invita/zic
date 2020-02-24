@extends("layout")

@section("content")

<div id="initView">

    <h5 style="float:left;">{{ __("zic.title_zic") }} {{ $zicId }}</h5>

    <div class="zicCDiv">
        <a class="zicPdfButton noHover" href="/zicPdf?id={{$zicId}}" target="_blank"><img src="/img/icon/pdf.png" class="imgPdf"></a>
    </div>

    <div class="zicDetails">
        @foreach($fields as $fieldName)
            @if ($zic[$fieldName])
                <div class="attrRow row collapse">
                    <div class="attrName large-2 medium-2 small-12 columns">{{ __("zic.field_".$fieldName) }}:</div>
                    @if (isset($zic[$fieldName."_link"]))
                        <div class="attrValue large-10 medium-10 small-12 columns"><a href="{{ $zic[$fieldName."_link"] }}">{{ $zic[$fieldName] }}</a></div>
                    @elseif ($fieldName === "oneline")
                        <div class="attrValue large-10 medium-10 small-12 columns copyOnClick">
                            <input type="text" value="{{ $zic[$fieldName] }}" />
                        </div>
                    @else
                        <div class="attrValue large-10 medium-10 small-12 columns">{{ $zic[$fieldName] }}</div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    <div class="zicCDiv">
        <a class="zicPdfButton noHover" href="/zicPdf?id={{$zicId}}" target="_blank"><img src="/img/icon/pdf.png" class="imgPdf"></a>
    </div>


    @if (isset($zic["citing"]) && $zic["citing"])
        <h5 id="citingToggleHandle" class="">{{ __("zic.field_citirano") }}{{ isset($zic["citiranoCount"]) ? " (".$zic["citiranoCount"].")" : "" }}</h5>
        <div id="citingToggleDiv" style="display:none;">

            <div class="samocitatiDetails">
                <label for="samocitati">Vključi tudi samocitate</label>
                <input type="checkbox" id="samocitati">
            </div>
            <table class="citing">
                <thead>
                    <tr>
                        @foreach ($citingFields as $cFieldName)
                            <th>{{ __("zic.field_".$cFieldName) }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach ($zic["citing"] as $citing)
                        <tr onclick="location.href='/zic?id={{ $citing["ID"] }}'">
                        @foreach($citingFields as $cFieldName)
                            <td>
                                {{ isset($citing[$cFieldName]) && $citing[$cFieldName] ? $citing[$cFieldName] : "" }}
                            </td>
                        @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <script>
            $(document).ready(function() {
                var qp = si4.queryStringToJson(location.search);
                var showCiting = qp.show == "citing";
                var sc = !!(qp.sc === "1" || qp.sc === undefined);
                $("#citingToggleHandle").click(function() {
                    $("#citingToggleHandle").toggleClass("active");
                    $("#citingToggleDiv").slideToggle();
                });

                if (showCiting) {
                    $("#citingToggleHandle").addClass("active");
                    $("#citingToggleDiv").toggle();
                    $("#citingToggleHandle")[0].scrollIntoView();
                }

                if (sc) {
                    $("#samocitati").prop("checked", true);
                }

                $(".copyOnClick").click(function() {
                    var input = $(this).find("input")[0];
                    input.select();
                    input.setSelectionRange(0, 99999);
                    document.execCommand("copy");
                    console.log("Copied to clipboard.");
                    var div = $(this);
                    div.addClass("copied");
                    setTimeout(function() { div.removeClass("copied") }, 3000);
                });

                $("#samocitati").click(function() {
                    var value = $("#samocitati").prop("checked");
                    var queryParams = si4.queryStringToJson(location.search);
                    queryParams.show = "citing";
                    if (value) delete queryParams.sc; else queryParams.sc = "0";
                    //console.log(value, si4.jsonToQueryString(queryParams));
                    location.href = "/zic" + si4.jsonToQueryString(queryParams);
                });
            });
        </script>

    @endif


    @if (isset($zic["citati"]) && $zic["citati"])
        <h5 id="citatiToggleHandle" class="">{{ __("zic.field_citati") }}{{ isset($zic["citatiCount"]) ? " (".$zic["citatiCount"].")" : "" }}</h5>
        <div id="citatiToggleDiv"style="display:none;">

            <table class="citati">
                <thead>
                    <tr>
                        @foreach ($citatiFields as $cFieldName)
                            <th>{{ __("zic.field_".$cFieldName) }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach ($zic["citati"] as $citat)
                        <?php
                        //<tr onclick="location.href='/redirectCited?gtid={{ $citat["gtid"] }}&cid={{ $citat["cid"] }}'">
                        ?>

                        <tr onclick="location.href='/cit?zid={{ $citat["gtid"] }}&cid={{ $citat["cid"] }}'">
                        @foreach($citatiFields as $cFieldName)
                            <td>
                                {{ isset($citat[$cFieldName]) && $citat[$cFieldName] ? $citat[$cFieldName] : "" }}
                            </td>
                        @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <script>
            $(document).ready(function() {
                $("#citatiToggleHandle").click(function() {
                    $("#citatiToggleHandle").toggleClass("active");
                    $("#citatiToggleDiv").slideToggle();
                });
            });
        </script>

    @endif



</div>

@endsection
