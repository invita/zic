<?php

namespace App\Http\Controllers;

use App\Helpers\ContentBuilder;
use App\Helpers\ElasticHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class IndexController extends LayoutController
{
    public function index(Request $request)
    {
        $lang = App::getLocale();
        $menuId = $request->input('menuId');
        $contentHtml = "";
        if ($menuId)
            $contentHtml = ContentBuilder::getHtmlForMenuId($menuId);
        else
            $contentHtml = ContentBuilder::getHtmlForFirstPage();

        $viewData = $this->getLayoutData($request, [
            "zics" => [],
            "contentHtml" => $contentHtml,
        ]);

        return view('index', $viewData);
    }
}
