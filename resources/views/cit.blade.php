@extends("layout")

@section("content")

<div id="initView">
    <!--
    <div class="zicCDiv">
        <a class="citPdfButton noHover" href="/zicPdf?id={{$zicId}}" target="_blank"><img src="/img/icon/pdf.png" class="imgPdf"></a>
    </div>
    -->

    <h5>{{ __("zic.title_cit") }} {{ $zicId }}-{{ $cId }}</h5>

    <div class="zicDetails">

        @foreach($fields as $fieldName)
            @if ($cit[$fieldName])
                <div class="attrRow row collapse">
                    <div class="attrName large-2 medium-2 small-12 columns">{{ __("zic.field_".$fieldName) }}:</div>
                    @if (isset($zic[$fieldName."_link"]))
                        <div class="attrValue large-10 medium-10 small-12 columns"><a href="{{ $cit[$fieldName."_link"] }}">{{ $cit[$fieldName] }}</a></div>
                    @elseif ($fieldName === "zicCompressed")
                        <div class="attrValue large-10 medium-10 small-12 columns copyOnClick">
                            <input type="text" value="{{ $cit[$fieldName] }}" />
                        </div>
                    @else
                        <div class="attrValue large-10 medium-10 small-12 columns">{{ $cit[$fieldName] }}</div>
                    @endif
                </div>
            @endif
        @endforeach

        <div class="attrRow row collapse">
            <div class="attrName large-2 medium-2 small-12 columns">{{ __("zic.field_zicCompressed") }}:</div>
            <div class="attrValue large-10 medium-10 small-12 columns" style="white-space: normal;">
                <a href="{{$cit["zicLink"]}}">{{ $cit["zicCompressed"] }}</a>
            </div>
        </div>

    </div>

    <!--
    <div class="zicCDiv">
        <a class="citPdfButton noHover" href="/zicPdf?id={{$zicId}}" target="_blank"><img src="/img/icon/pdf.png" class="imgPdf"></a>
    </div>
    -->


</div>

@endsection
