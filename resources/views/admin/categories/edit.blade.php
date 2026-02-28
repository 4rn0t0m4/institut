@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Modifier la categorie" :breadcrumbs="['Categories' => route('admin.categories.index'), $category->name => null]" />

    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @csrf
        @method('PUT')
        @include('admin.categories._form')
    </form>
@endsection
