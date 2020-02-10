<?php

namespace App\Http\Controllers;

use App\Helpers\ElasticHelpers;
use App\Helpers\Si4Util;
use App\Helpers\ZicUtil;
use Illuminate\Http\Request;

class CitController extends LayoutController
{
    public function index(Request $request)
    {
        $zicId = $request->input('zid');
        $cId = $request->input('cid');

        if (!$zicId) die("Missing zid");
        if (!$cId) die("Missing cid");

        try {
            $zicId = intval($zicId);
            $cId = intval($cId);
        } catch (\Exception $e) {
            die("Bad Id");
        }

        $citElasticId = ZicUtil::citElasticId($zicId, $cId);
        $citElastic = ElasticHelpers::searchCitById($citElasticId);
        $cits = ElasticHelpers::elasticResultToSimpleAssocArray($citElastic);

        if (!isset($cits[$citElasticId])) {
            return abort(404);
        }

        $citDocument = $cits[$citElasticId];
        $cit = ZicUtil::citDisplay($citDocument);

        /*
        $citingZicsElastic = ElasticHelpers::searchCitingZics($zicDocument);
        $citingZics = ElasticHelpers::elasticResultToSimpleAssocArray($citingZicsElastic);

        foreach ($citingZics as $idx => $cz) {
            $citingZics[$idx] = ZicUtil::zicDisplay($cz);
        }

        $zic["citing"] = $citingZics;
        */

        //print_r($citingZics);

        $viewData = $this->getLayoutData($request, [
            "zicId" => $zicId,
            "cId" => $cId,
            "cit" => $cit,
            "fields" => ZicUtil::$citDetailsFields,
        ]);
        return view('cit', $viewData);
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
