<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criterion;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Display functional home page with concise system overview and direct access buttons.
     */
    public function index(): View
    {
        $criteria = Criterion::orderBy('code')->get();
        $sampleLaptops = Alternative::with('values.criterion')
            ->orderBy('price')
            ->take(6)
            ->get();
        $totalLaptops = Alternative::count();

        return view('home', compact('criteria', 'sampleLaptops', 'totalLaptops'));
    }
}
