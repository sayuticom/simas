<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PlaceholderController extends Controller
{
    /**
     * Display placeholder page
     */
    public function show($slug): View
    {
        return view('admin.placeholder', ['moduleName' => $slug]);
    }
}
