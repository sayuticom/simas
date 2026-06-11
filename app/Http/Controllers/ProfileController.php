<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display mosque profile page
     */
    public function index(): View
    {
        return view('admin.profile');
    }
}
