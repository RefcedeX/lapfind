<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criterion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display filtered laptop catalog with price and brand filters.
     */
    public function index(Request $request): View
    {
        $query = Alternative::with('values.criterion');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->input('brand'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        // Filter Spesifikasi Cepat (Kriteria C3 RAM, C4 Storage, C5 Bobot)
        if ($request->filled('ram')) {
            $ram = (int) $request->input('ram');
            $query->whereHas('values', function ($q) use ($ram) {
                $q->whereHas('criterion', fn ($c) => $c->where('code', 'C3'))
                    ->where('value', '>=', $ram);
            });
        }

        if ($request->filled('storage')) {
            $storage = (int) $request->input('storage');
            $query->whereHas('values', function ($q) use ($storage) {
                $q->whereHas('criterion', fn ($c) => $c->where('code', 'C4'))
                    ->where('value', '>=', $storage);
            });
        }

        if ($request->filled('max_weight')) {
            $maxWeight = (float) $request->input('max_weight');
            $query->whereHas('values', function ($q) use ($maxWeight) {
                $q->whereHas('criterion', fn ($c) => $c->where('code', 'C5'))
                    ->where('value', '<=', $maxWeight);
            });
        }

        $sort = $request->input('sort', 'versus_score');
        switch ($sort) {
            case 'price_desc':
                $query->orderByDesc('price');
                break;
            case 'name_asc':
                $query->orderBy('name');
                break;
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'versus_score':
            default:
                $query->orderBy('price');
                break;
        }

        $laptops = $query->paginate(12)->withQueryString();
        $brands = Alternative::select('brand')->distinct()->orderBy('brand')->pluck('brand');
        $criteria = Criterion::orderBy('code')->get();

        return view('catalog.index', compact('laptops', 'brands', 'criteria', 'sort'));
    }

    /**
     * Display single laptop details and specification breakdown.
     */
    public function show(Alternative $alternative): View
    {
        $alternative->load('values.criterion');
        $criteria = Criterion::orderBy('code')->get();

        // Rekomendasi alternatif alternatif lain dalam rentang harga sejenis (+/- 25%)
        $similarLaptops = Alternative::where('id', '!=', $alternative->id)
            ->whereBetween('price', [$alternative->price * 0.75, $alternative->price * 1.25])
            ->take(3)
            ->get();

        return view('catalog.show', compact('alternative', 'criteria', 'similarLaptops'));
    }

    /**
     * Compare 2 to 3 laptops side-by-side.
     */
    public function compare(Request $request): View
    {
        $selectedIds = (array) $request->input('laptop_ids', []);
        $selectedIds = array_filter(array_slice($selectedIds, 0, 3));

        $allLaptops = Alternative::orderBy('name')->get();
        $criteria = Criterion::orderBy('code')->get();

        $comparedLaptops = collect();
        if (! empty($selectedIds)) {
            $comparedLaptops = Alternative::with('values.criterion')
                ->whereIn('id', $selectedIds)
                ->get();
        }

        return view('catalog.compare', compact('comparedLaptops', 'allLaptops', 'criteria', 'selectedIds'));
    }
}
