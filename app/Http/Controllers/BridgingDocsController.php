<?php

namespace App\Http\Controllers;

use App\Models\BridgingSetting;
use App\Models\Setting;

class BridgingDocsController extends Controller
{
    public function index()
    {
        $bridging = BridgingSetting::current();
        $institusi = Setting::first();

        return view('bridging.docs', compact('bridging', 'institusi'));
    }
}
