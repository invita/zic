<?php

namespace App\Http\Controllers;

use App\Helpers\ElasticHelpers;
use App\Helpers\Si4Util;
use App\Helpers\ZicUtil;
use Illuminate\Http\Request;

class ZicController extends LayoutController
{
    public function index(Request $request)
    {
        $zicId = $request->input('id');
        $sc = $request->input('sc') !== "0"; // self citing

        if (!$zicId) die("Missing Id");
        $zicRest = "";

        $posFirstSep = strpos($zicId, "-");
        if ($posFirstSep !== false) {
            $zicRest = substr($zicId, $posFirstSep +1);
            $zicId = substr($zicId, 0, $posFirstSep);
        }

        try {
            $zicId = intval($zicId);
        } catch (\Exception $e) {
            die("Bad Id");
        }

        $zicElastic = ElasticHelpers::searchById($zicId);
        $zics = ElasticHelpers::elasticResultToSimpleAssocArray($zicElastic);

        if (!isset($zics[$zicId])) {
            return abort(404);
        }

        $zicDocument = $zics[$zicId];
        $zic = ZicUtil::zicDisplay($zicDocument);

        $citingZicsElastic = ElasticHelpers::searchCitingZics($zicDocument, !$sc);
        $citingZics = ElasticHelpers::elasticResultToSimpleAssocArray($citingZicsElastic);

        foreach ($citingZics as $idx => $cz) {
            $citingZics[$idx] = ZicUtil::zicDisplay($cz);
        }

        $zic["citing"] = $citingZics;
        $zic["citiranoCount"] = count($citingZics);

        //print_r($citingZics);

        $viewData = $this->getLayoutData($request, [
            "zicId" => $zicId,
            "zicRest" => $zicRest,
            "zic" => $zic,
            "fields" => ZicUtil::$detailsViewFields,
            "citatiFields" => ZicUtil::$detailsViewCitatiFields,
            "citingFields" => ZicUtil::$detailsViewCitingFields,
        ]);
        return view('zic', $viewData);
    }

    public function redirectCited(Request $request) {
        $gtid = $request->input('gtid');
        $cid = $request->input('cid');

        $zicElastic = ElasticHelpers::searchById($gtid);
        $zics = ElasticHelpers::elasticResultToSimpleAssocArray($zicElastic);

        if (!isset($zics[$gtid])) {
            return abort(404);
        }

        $zicDocument = $zics[$gtid];

        $citati = Si4Util::getArg($zicDocument, "citati", []);
        $matchingCitat = null;
        foreach ($citati as $citat) {
            $citat_cid = Si4Util::getArg($citat, "cid", null);
            if ($citat_cid == $cid) {
                $matchingCitat = $citat;
                break;
            }
        }

        if (!$matchingCitat) {
            return abort(404);
        }

        $naslov = Si4Util::getArg($matchingCitat, "naslov0", null);
        if (!$naslov) {
            return redirect('/zic?id='.$gtid);
        }

        $targetZicElastic = ElasticHelpers::searchZicByTitle($naslov);
        $targetZic = ElasticHelpers::elasticResultToSimpleArray($targetZicElastic);

        if (!isset($targetZic[0]) || !isset($targetZic[0]["ID"])) {
            return redirect('/zic?id='.$gtid);
        }

        return redirect('/zic?id='.$targetZic[0]["ID"]);

        //print_r($naslov);
        //print_r($targetZic);
        //die();
    }


}
