@extends('layouts.public', ['title' => 'Katalog Laptop Mahasiswa'])

@section('content')
@php
    $query = \App\Models\Alternative::with('values.criterion');

    if (request()->filled('search')) {
        $search = request('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    if (request()->filled('brand')) {
        $query->where('brand', request('brand'));
    }

    if (request()->filled('min_price')) {
        $query->where('price', '>=', (float) request('min_price'));
    }

    if (request()->filled('max_price')) {
        $query->where('price', '<=', (float) request('max_price'));
    }

    if (request()->filled('ram')) {
        $ram = (int) request('ram');
        $query->whereHas('values', function ($q) use ($ram) {
            $q->whereHas('criterion', fn ($c) => $c->where('code', 'C3'))
                ->where('value', '>=', $ram);
        });
    }

    if (request()->filled('storage')) {
        $storage = (int) request('storage');
        $query->whereHas('values', function ($q) use ($storage) {
            $q->whereHas('criterion', fn ($c) => $c->where('code', 'C4'))
                ->where('value', '>=', $storage);
        });
    }

    if (request()->filled('max_weight')) {
        $maxWeight = (float) request('max_weight');
        $query->whereHas('values', function ($q) use ($maxWeight) {
            $q->whereHas('criterion', fn ($c) => $c->where('code', 'C5'))
                ->where('value', '<=', $maxWeight);
        });
    }

    $sort = request('sort', 'versus_score');
    switch ($sort) {
        case 'price_desc':
            $query->orderByDesc('price');
            break;
        case 'name_asc':
            $query->orderBy('name');
            break;
        case 'price_asc':
        default:
            $query->orderBy('price');
            break;
    }

    $laptops = $query->paginate(12)->withQueryString();
    $brands = \App\Models\Alternative::select('brand')->distinct()->orderBy('brand')->pluck('brand');
    $criteria = $criteria ?? \App\Models\Criterion::orderBy('code')->get();
@endphp

    @include('catalog._catalog_content', [
        'laptops' => $laptops,
        'brands' => $brands,
        'criteria' => $criteria,
        'sort' => $sort,
        'isHome' => true
    ])
@endsection
