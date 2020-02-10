<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchCitController extends LayoutController
{
    public function index(Request $request)
    {

        $viewData = $this->getLayoutData($request, [
        ]);

        return view('searchc', $viewData);

    }
}
