@extends("layout")

@section("content")

<div id="initView">
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
                        <tr onclick="location.href='/redirectCited?gtid={{ $citat["gtid"] }}&cid={{ $citat["cid"] }}'">
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


    @if (isset($zic["citing"]) && $zic["citing"])
        <h5 id="citingToggleHandle" class="">{{ __("zic.field_citirano") }}{{ isset($zic["citiranoCount"]) ? " (".$zic["citiranoCount"].")" : "" }}</h5>
        <div id="citingToggleDiv" style="display:none;">

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
                $("#citingToggleHandle").click(function() {
                    $("#citingToggleHandle").toggleClass("active");
                    $("#citingToggleDiv").slideToggle();
                });

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
            });
        </script>

    @endif



</div>

@endsection
