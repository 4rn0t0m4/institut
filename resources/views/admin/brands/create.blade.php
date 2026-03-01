@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Nouvelle marque" :breadcrumbs="['Marques' => route('admin.brands.index'), 'Creer' => null]" />

    <form method="POST" action="{{ route('admin.brands.store') }}">
        @csrf
        @include('admin.brands._form')
    </form>
@endsection
