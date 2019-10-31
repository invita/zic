<?php

namespace App\Http\Controllers;

use App\Helpers\SifHelpers;
use Elasticsearch\Common\Exceptions\ElasticsearchException;
use Illuminate\Http\Request;
use App\Helpers\ElasticHelpers;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function index(Request $request)
    {
        $result = array(
            "status" => false,

        );
        return json_encode($result);
    }

    public function search(Request $request)
    {
        $input =  file_get_contents("php://input");
        $inputJson = json_decode($input, true);

        $q = "*";
        if (isset($inputJson["q"])) $q = $inputJson["q"];
        if (isset($inputJson["staticData"]) && isset($inputJson["staticData"]["q"])) $q = $inputJson["staticData"]["q"];

        $pageStart = isset($inputJson["pageStart"]) ? $inputJson["pageStart"] : 0;
        $pageCount = isset($inputJson["pageCount"]) ? $inputJson["pageCount"] : 20;
        $sortField = isset($inputJson["sortField"]) ? $inputJson["sortField"] : "ID";
        $sortOrder = isset($inputJson["sortOrder"]) ? $inputJson["sortOrder"] : "asc";

        $filter = $inputJson["filter"];

        $zics = [];
        $rowCount = 0;
        $error = "";
        $status = true;

        try {
            if ($q) {
                $zicsElastic = ElasticHelpers::search($q, $filter, $pageStart, $pageCount, $sortField, $sortOrder);

                $rowCount = $zicsElastic["hits"]["total"];
                $zics = ElasticHelpers::elasticResultToSimpleArray($zicsElastic);
            }
        } catch (\Exception $e) {
            if ($e instanceof ElasticsearchException) {
                $elasticE = json_decode($e->getMessage(), true);
                $status = false;
                if (isset($elasticE["error"]) && isset($elasticE["error"]["root_cause"])) {
                    $eRoots = $elasticE["error"]["root_cause"];
                    foreach ($eRoots as $eRoot) {
                        if (isset($eRoot["reason"])) {
                            if ($error) $error .= "; ";
                            $error .= $eRoot["reason"];
                        }
                    }
                }
                if (!$error) {
                    $error = get_class($e) .": ". $e->getMessage();
                }
            } else {
                $status = false;
                $error = get_class($e) .": ". $e->getMessage();
            }
        }

        //print_r($zicsElastic);

        $result = array(
            "request" => [
                "q" => $q,
                "filter" => $filter,
                "pageStart" => $pageStart,
                "pageCount" => $pageCount,
                "sortField" => $sortField,
                "sortOrder" => $sortOrder,
            ],
            "status" => $status,
            "error" => $error,
            "rowCount" => $rowCount,
            "data" => $zics,
        );

        return response(json_encode($result))->header('Content-Type', 'application/json');
    }

    public function initialData(Request $request) {
        $result = [
            "sif_tipologies" => SifHelpers::getTipologies(),
        ];

        return json_encode($result);
    }

    public function dictionary(Request $request) {
        $input =  file_get_contents("php://input");
        $inputJson = json_decode($input, true);
        $lang = $inputJson["lang"];

        if ($lang == "eng" || $lang == "en")
            $lang = "en";
        else
            $lang = "sl";

        App::setLocale($lang);
        $result = Lang::get("zic");
        return json_encode($result);
    }

    public function reindex(Request $request) {
        $pass = $request->input('pass');
        if ($pass !== env("SI4_REINDEX_API_PASS")) return response('', 403);

        $fromId = intval($request->input('fromId'));
        $toId = intval($request->input('toId'));

        $query = DB::table("ZIC_GLAVNA_TABELA")
            ->where("id", ">=", $fromId)
            ->where("id", "<=", $toId);

        $rowCount = $query->count();
        $zics = $query->get();

        if ($rowCount) {
            // Single or Mass reindex
            foreach ($zics as $zic) {
                Artisan::call("reindex:zic", ["zicId" => $zic->ID]);
            }
        } else {
            // Deletion
            if ($fromId == $toId) {
                Artisan::call("reindex:zic", ["zicId" => $fromId]);
            }
        }

        $result = [
            "status" => true,
            "count" => $rowCount,
        ];

        return json_encode($result);

    }
}
