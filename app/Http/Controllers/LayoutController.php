<?php

namespace App\Http\Controllers;

use App\Helpers\FooterBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LayoutController extends Controller
{
    protected function getLayoutData(Request $request, $viewData = [])
    {
        $q = $request->input('q');
        return array_merge([
            "q" => $q,
            "lang" => App::getLocale(),
            "footerHtml" => FooterBuilder::getHtml(),
        ], $viewData);
    }
}
