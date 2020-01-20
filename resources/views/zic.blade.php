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
                    <span class="attrName large-2 medium-2 small-12 columns">{{ __("zic.field_".$fieldName) }}:</span>
                    @if (isset($zic[$fieldName."_link"]))
                        <span class="attrValue large-10 medium-10 small-12 columns"><a href="{{ $zic[$fieldName."_link"] }}">{{ $zic[$fieldName] }}</a></span>
                    @else
                        <span class="attrValue large-10 medium-10 small-12 columns">{{ $zic[$fieldName] }}</span>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    <div class="zicCDiv">
        <a class="zicPdfButton noHover" href="/zicPdf?id={{$zicId}}" target="_blank"><img src="/img/icon/pdf.png" class="imgPdf"></a>
    </div>


    @if (isset($zic["citati"]) && $zic["citati"])
        <h5 id="citatiToggleHandle" class="active">Citati{{ isset($zic["citatiCount"]) ? " (".$zic["citatiCount"].")" : "" }}</h5>
        <div id="citatiToggleDiv">

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
                        <tr>
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


        <!--
        @foreach ($zic["citati"] as $citat)
            <div class="zicDetails citat">
                @foreach($citatiFields as $cFieldName)
                    @if ($citat[$cFieldName])
                        <div class="attrRow row collapse">
                            <span class="attrName large-2 medium-2 small-12 columns">{{ __("zic.field_".$cFieldName) }}:</span>
                            <span class="attrValue large-10 medium-10 small-12 columns">{{ $citat[$cFieldName] }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
        -->

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
