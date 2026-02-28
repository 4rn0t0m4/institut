@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Nouvelle categorie" :breadcrumbs="['Categories' => route('admin.categories.index'), 'Creer' => null]" />

    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        @include('admin.categories._form')
    </form>
@endsection
