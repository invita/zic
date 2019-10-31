@extends("layout")

@section("content")

<div id="initView">
    <div class="large-7 medium-12 small-12 columns">
        <?php echo $contentHtml; ?>
    </div>

    <div class="large-5 medium-12 small-12 columns" style="margin-top:20px;">
        <?php
        /*
        <pre id="chartData" style="display:none;">
            {__comment__{json_encode([ "chartData" => $chartData,"qType" => "bar" ])}}
        </pre>
        <canvas id="chartCanvas" width="500" height="400"></canvas>
        <div class="text-center">
            <a href="/chart" class="translateHtml" data-translateHtml="chart_checkItOut"></a>
        </div>
        */
        ?>
    </div>
</div>

@endsection
