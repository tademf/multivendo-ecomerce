<?php

namespace App\Http\Controllers;

// If Controller class doesn't exist, use this instead:
use Illuminate\Routing\Controller as BaseController;

class HomeController extends BaseController
{
    /**
     * Display the home page.
     */
    public function index()
    {
        return \Inertia\Inertia::render('Home');
    }
}
