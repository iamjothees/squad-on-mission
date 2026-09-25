<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function time()
    {
        return view('reports.time');
    }

    public function entities()
    {
        return view('reports.entities');
    }
}
