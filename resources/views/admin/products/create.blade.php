@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Nouveau produit" :breadcrumbs="['Produits' => route('admin.products.index'), 'Creer' => null]" />

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.products._form')
    </form>
@endsection
