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
                    <span class="attrValue large-10 medium-10 small-12 columns">{{ $zic[$fieldName] }}</span>
                </div>
            @endif
        @endforeach
    </div>

    <div class="zicCDiv">
        <a class="zicPdfButton noHover" href="/zicPdf?id={{$zicId}}" target="_blank"><img src="/img/icon/pdf.png" class="imgPdf"></a>
    </div>

</div>

@endsection
