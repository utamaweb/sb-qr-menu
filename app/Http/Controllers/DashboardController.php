<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        $autoUpdateData = $this->general();
        $alertBugEnable =  $autoUpdateData['alertBugEnable'];
        $alertVersionUpgradeEnable = $autoUpdateData['alertVersionUpgradeEnable'];
        return view('dashboard', compact('alertBugEnable','alertVersionUpgradeEnable'));
    }
}
