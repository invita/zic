@extends("layout")

@section("content")

<div id="initView">

    <div class="search large-12 medium-12 small-12 columns">
        <div class="searchTitle"></div>
        <div class="content katSearch active" id="pnlZics">
            <form id="chartFormZics">
                <div class="large-12 medium-12 small-12">

                    <div class="chartField large-12 medium-12 small-12 columns">
                        <label class="translateHtml" data-translateHtml="chart_field_chartType"></label>
                        <select name="type">
                            @foreach($types as $type)
                            <option value="{{$type["key"]}}"{{ $type["key"] == $qType ? " selected" : ""}}>{{$type["value"]}}</option>
                            @endforeach;
                        </select>
                    </div>

                    <div class="chartField large-6 medium-6 small-12 columns">
                        <label class="translateHtml" data-translateHtml="chart_field_from"></label>
                        <select name="from">
                            <option value=""></option>
                            @foreach($froms as $from)
                                <option value="{{$from["key"]}}"{{ $from["key"] == $qFrom ? " selected" : ""}}>{{$from["value"]}}</option>
                            @endforeach;
                        </select>
                    </div>

                    <div class="chartField large-6 medium-6 small-12 columns">
                        <label class="translateHtml" data-translateHtml="chart_field_to"></label>
                        <select name="to">
                            <option value=""></option>
                            @foreach($tos as $to)
                                <option value="{{$to["key"]}}"{{ $to["key"] == $qTo ? " selected" : ""}}>{{$to["value"]}}</option>
                            @endforeach;
                        </select>
                    </div>

                    <div class="chartField large-6 medium-6 small-12 columns">
                        <label class="translateHtml" data-translateHtml="chart_field_land"></label>
                        <select name="land">
                            <option value=""></option>
                            @foreach($lands as $land)
                                <option value="{{$land}}"{{ $land == $qLand ? " selected" : ""}}>{{$land}}</option>
                            @endforeach;
                        </select>
                    </div>

                    <div class="chartField large-6 medium-6 small-12 columns">
                        <label class="translateHtml" data-translateHtml="chart_field_munic"></label>
                        <select name="munic">
                            <option value=""></option>
                            @foreach($munics as $munic)
                                <option value="{{$munic}}"{{ $munic == $qMunic ? " selected" : ""}}>{{$munic}}</option>
                            @endforeach;
                        </select>
                    </div>
                </div>

                <div class="chartField large-6 medium-6 small-12 columns">
                    <input type="submit" class="button large-6 medium-6 small-6 translateValue"
                        data-translateValue="chart_button_refresh" value="" />
                    <div></div>
                </div>
            </form>
        </div>
    </div>

    <?php
    /*
    <pre id="chartData" style="display:none;">
        {__comment__{json_encode([ "chartData" => $chartData,"qType" => $qType ])}}
    </pre>
    <canvas id="chartCanvas" width="1000" height="300"></canvas>
    */
    ?>
</div>

@endsection
