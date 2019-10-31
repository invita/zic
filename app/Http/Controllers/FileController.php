<?php

namespace App\Http\Controllers;

use App\Helpers\ElasticHelpers;
use App\Helpers\FieldHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class FileController extends Controller
{
    public function index(Request $request)
    {
    }

    public function zicPdf(Request $request) {
        $id = intval($request->input('id'));

        $zicsElastic = ElasticHelpers::search("ID:".$id, [], 0, 1);
        $zics = ElasticHelpers::elasticResultToSimpleArray($zicsElastic);

        if (count($zics)) {
            $zic = $zics[0];

            $html = '<style>body { font-family: DejaVu Sans; }</style>';
            $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            $html .= '<h3>'.__("zic.pdf_title").': '.$id.'</h3>';

            $idx = 0;
            foreach (FieldHelpers::$fields as $key) {
                $val = isset($zic[$key]) ? $zic[$key] : null;
                if (!$val) continue;

                $html .= '<div style="font-size:12px;padding:3px; background-color:'.($idx%2 ? 'white':'#F9F9F9').'">';
                $html .= '  <span style="font-weight:bold; width:150px;">'.__("zic.field_".$key). ':</span> '.
                         '  <span style="">' .$val .'</span>';
                $html .= '</div>';

                $idx ++;
            }
            //print_r($zic);
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html);
            return $pdf->stream();
        }

        die("...");
    }
}
