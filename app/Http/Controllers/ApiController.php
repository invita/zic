<?php

namespace App\Http\Controllers;

use App\Helpers\Si4Util;
use App\Helpers\SifHelpers;
use App\Helpers\ZicUtil;
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
        $samocitati = Si4Util::pathArg($inputJson, "staticData/samocitati", false);

        $pageStart = isset($inputJson["pageStart"]) ? $inputJson["pageStart"] : 0;
        $pageCount = isset($inputJson["pageCount"]) ? $inputJson["pageCount"] : 20;
        $sortField = isset($inputJson["sortField"]) ? $inputJson["sortField"] : null;
        $sortOrder = isset($inputJson["sortOrder"]) ? $inputJson["sortOrder"] : "asc";

        if ($sortField) {
            if (isset(ZicUtil::$fieldsSortMap[$sortField]))
                $sortField = ZicUtil::$fieldsSortMap[$sortField];
        }

        $filter = $inputJson["filter"];

        $zics = [];
        $rowCount = 0;
        $error = "";
        $status = true;

        try {
            if ($q) {
                $zicsElastic = ElasticHelpers::searchString($q, $filter, $pageStart, $pageCount, $sortField, $sortOrder);
                $zicsElasticArr = ElasticHelpers::elasticResultToSimpleArray($zicsElastic);

                //$samocitati

                if (!$samocitati) {

                    // Remove samocitati count
                    foreach ($zicsElasticArr as $zIdx => $zic) {

                        $citing = ElasticHelpers::searchCitingZics($zic, true);
                        $noSelfCitingCount = Si4Util::pathArg($citing, "hits/total", 0);
                        $zicsElasticArr[$zIdx]["citiranoCount"] = $noSelfCitingCount;

                    }


                }



                $rowCount = $zicsElastic["hits"]["total"];
                $zics = ZicUtil::zicsDisplay($zicsElasticArr);
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

    public function searchc(Request $request)
    {
        $input =  file_get_contents("php://input");
        $inputJson = json_decode($input, true);

        $q = "*";
        if (isset($inputJson["q"])) $q = $inputJson["q"];
        if (isset($inputJson["staticData"]) && isset($inputJson["staticData"]["q"])) $q = $inputJson["staticData"]["q"];
        $samocitati = Si4Util::pathArg($inputJson, "staticData/samocitati", false);

        $pageStart = isset($inputJson["pageStart"]) ? $inputJson["pageStart"] : 0;
        $pageCount = isset($inputJson["pageCount"]) ? $inputJson["pageCount"] : 20;
        $sortField = isset($inputJson["sortField"]) ? $inputJson["sortField"] : null;
        $sortOrder = isset($inputJson["sortOrder"]) ? $inputJson["sortOrder"] : "asc";

        if ($sortField) {
            if (isset(ZicUtil::$citFieldsSortMap[$sortField]))
                $sortField = ZicUtil::$citFieldsSortMap[$sortField];
        }

        $filter = $inputJson["filter"];
        if ($filter) {
            $filterMapped = [];
            foreach ($filter as $fKey => $fVal) {
                if ($fKey == "zapSt") {
                    $e = explode("/", $fVal);
                    if (count($e) === 1) {
                        $filterMapped["gtid"] = $fVal;
                    } else if (count($e) === 2) {
                        $filterMapped["gtid"] = $e[0];
                        $filterMapped["cid"] = $e[1];
                    }

                } else {
                    //if (isset(ZicUtil::$citFieldsSortMap[$fKey])) $fKey = ZicUtil::$citFieldsSortMap[$fKey];
                    $filterMapped[$fKey] = $fVal;
                }
            }
            $filter = $filterMapped;
        }



        $cits = [];
        $rowCount = 0;
        $error = "";
        $status = true;

        try {
            if ($q) {
                $citsElastic = ElasticHelpers::searchCitsString($q, $filter, $pageStart, $pageCount, $sortField, $sortOrder, !$samocitati);

                $rowCount = $citsElastic["hits"]["total"];
                $cits = ZicUtil::citsDisplay(ElasticHelpers::elasticResultToSimpleArray($citsElastic));
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
            "data" => $cits,
        );

        return response(json_encode($result))->header('Content-Type', 'application/json');
    }

    private static function countMatchingChars($str1, $str2) {
        $len1 = mb_strlen($str1);
        $len2 = mb_strlen($str2);
        if (!$len1 || !$len2) return 0;

        $shorterLen = min($len1, $len2);
        for ($i = 0; $i < $shorterLen; $i++) {
            if ($str1[$i] != $str2[$i]) return $i;
        }
        return $shorterLen;
    }

    // Find shortest best matching string in array
    // If more strings match with the same number of starting characters, shorter is chosen.
    private static function findShortestMatching($str, $array) {

        $bestScore = 0;
        $bestPotentials = [];

        foreach ($array as $potential) {
            $curScore = self::countMatchingChars($str, $potential);
            if ($curScore == $bestScore) {
                $bestPotentials[] = $potential;
            } else if ($curScore > $bestScore) {
                $bestScore = $curScore;
                $bestPotentials = [$potential];
            }
        }

        if (!count($bestPotentials)) return "";
        if (count($bestPotentials) == 1) return $bestPotentials[0];

        $shortest = $bestPotentials[0];
        foreach ($bestPotentials as $potential) {
            if (mb_strlen($potential) < mb_strlen($shortest))
                $shortest = $potential;
        }
        return $shortest;
    }

    private function strSameRoot($str1, $str2) {
        $len1 = mb_strlen($str1);
        $len2 = mb_strlen($str2);
        if (!$len1 || !$len2) return false;

        $shorterLen = min($len1, $len2);
        return mb_substr($str1, 0, $shorterLen) === mb_substr($str2, 0, $shorterLen);
    }


    public function searchSuggest(Request $request) {


        $term = $request->query("term", "");
        $termLower = mb_strtolower($term);

        // Find potential creators

        $creatorElasticData = ElasticHelpers::suggestCreators($termLower);
        $creatorAssocData = ElasticHelpers::elasticResultToAssocArray($creatorElasticData);

        $creatorResults = [];
        foreach ($creatorAssocData as $doc) {
            //$creators = Si4Util::arrayValues(Si4Util::pathArg($doc, "_source/data/si4/creator", []));
            $creators = Si4Util::pathArg($doc, "_source/authors", []);
            foreach ($creators as $creator) {
                $cIME = Si4Util::getArg($creator, "IME", "");
                $cPRIIMEK = Si4Util::getArg($creator, "PRIIMEK", "");
                $c = trim($cIME ." ". $cPRIIMEK);
                $creatorClean = mb_strtolower(ElasticHelpers::removeSkipCharacters($c));
                //echo "creatorClean ".$creatorClean."\n";
                $creatorSplit = explode(" ", $creatorClean);
                $splitCount = count($creatorSplit);

                // Create creator firstName/lastName/(middleName) combinations
                $creatorCombs = [];
                if ($splitCount == 2) {
                    $creatorCombs[] = $creatorSplit[0]." ".$creatorSplit[1];
                    $creatorCombs[] = $creatorSplit[1]." ".$creatorSplit[0];
                } else if ($splitCount == 3) {
                    $creatorCombs[] = $creatorSplit[0]." ".$creatorSplit[1]." ".$creatorSplit[2];
                    $creatorCombs[] = $creatorSplit[0]." ".$creatorSplit[2]." ".$creatorSplit[1];
                    $creatorCombs[] = $creatorSplit[1]." ".$creatorSplit[0]." ".$creatorSplit[2];
                    $creatorCombs[] = $creatorSplit[1]." ".$creatorSplit[2]." ".$creatorSplit[0];
                    $creatorCombs[] = $creatorSplit[2]." ".$creatorSplit[0]." ".$creatorSplit[1];
                    $creatorCombs[] = $creatorSplit[2]." ".$creatorSplit[1]." ".$creatorSplit[0];

                } else {
                    $creatorCombs[] = $creatorClean;
                }

                foreach ($creatorCombs as $creatorComb) {
                    if (self::strSameRoot($creatorComb, $termLower)) {
                        if (!isset($creatorResults[$creatorComb]))
                            $creatorResults[$creatorComb] = 1;
                        else
                            $creatorResults[$creatorComb] += 1;
                    }
                }

            }
        }

        //print_r($creatorResults);

        $oneCreator = self::findShortestMatching($termLower, array_keys($creatorResults));

        //echo "creatorResults: ".print_r(array_keys($creatorResults), true)."\n";
        //echo "oneCreator: ".$oneCreator."\n";
        //die();

        $onlyFewCreatorsAndFullyMatched = count($creatorResults) <= 3 && $oneCreator;

        if (!$onlyFewCreatorsAndFullyMatched && count($creatorResults)) {

            return json_encode(array_keys($creatorResults));

        } else {

            // Find potential titles

            // If more than one (a few) creators possible, list those with higher length
            $titleResults = [];
            if (count($creatorResults) > 1) {
                foreach (array_keys($creatorResults) as $c) {
                    if (mb_strlen($c) >= mb_strlen($termLower))
                        $titleResults[$c] = 1;
                }
            }

            $termRest = trim(mb_substr($termLower, mb_strlen($oneCreator)));
            //echo "termRest {$termRest}\n";

            $titleElasticData = ElasticHelpers::suggestTitlesForCreator($oneCreator, $termRest, 10);
            $titleAssocData = ElasticHelpers::elasticResultToAssocArray($titleElasticData);

            foreach ($titleAssocData as $doc) {
                $t = Si4Util::pathArg($doc, "_source/OpNaslov", "");
                $titleClean = mb_strtolower(ElasticHelpers::removeSkipCharacters($t));
                $oneCreatorWithTitle = $oneCreator ? $oneCreator." ".$titleClean : $titleClean;

                if (!count($creatorResults) || !$termRest || self::strSameRoot($titleClean, $termRest)) {
                    if (!isset($titleResults[$oneCreatorWithTitle]))
                        $titleResults[$oneCreatorWithTitle] = 1;
                    else
                        $titleResults[$oneCreatorWithTitle] += 1;

                }
            }

            return json_encode(array_keys($titleResults));

        }












        //return response(json_encode($result))->header('Content-Type', 'application/json');
    }



    public function initialData(Request $request) {
        $result = [
            "sif_tipologies" => SifHelpers::getTipologies(),
            "sif_languages" => SifHelpers::getLanguages(),
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

        $query = DB::table("ZIC_GLAVNA_TABELA_V2")
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
