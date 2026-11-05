@extends('layouts.public', ['title' => 'Katalog Laptop'])

@section('content')
    @include('catalog._catalog_content', [
        'laptops' => $laptops,
        'brands' => $brands,
        'criteria' => $criteria,
        'sort' => $sort,
        'isHome' => false
    ])
@endsection
