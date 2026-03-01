@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Modifier {{ $brand->name }}" :breadcrumbs="['Marques' => route('admin.brands.index'), $brand->name => null]" />

    <form method="POST" action="{{ route('admin.brands.update', $brand) }}">
        @csrf
        @method('PUT')
        @include('admin.brands._form')
    </form>
@endsection
