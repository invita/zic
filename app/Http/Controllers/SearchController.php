<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends LayoutController
{
    public function index(Request $request)
    {

        $viewData = $this->getLayoutData($request, [
        ]);

        return view('search', $viewData);

    }
}
