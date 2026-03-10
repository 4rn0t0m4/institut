@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Modifier le produit" :breadcrumbs="['Produits' => route('admin.products.index'), $product->name => null]" />

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.products._form')
    </form>
@endsection
