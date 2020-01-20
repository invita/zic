<?php

namespace App\Http\Controllers;

use App\Helpers\ElasticHelpers;
use App\Helpers\ZicUtil;
use Illuminate\Http\Request;

class ZicController extends LayoutController
{
    public function index(Request $request)
    {
        $zicId = $request->input('id');

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

        $zic = ZicUtil::zicDisplay($zics[$zicId]);


        $viewData = $this->getLayoutData($request, [
            "zicId" => $zicId,
            "zicRest" => $zicRest,
            "zic" => $zic,
            "fields" => ZicUtil::$detailsViewFields,
            "citatiFields" => ZicUtil::$detailsViewCitatiFields,
        ]);
        return view('zic', $viewData);
    }
}
