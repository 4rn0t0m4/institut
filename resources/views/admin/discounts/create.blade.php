@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Nouveau code promo" :breadcrumbs="['Codes promo' => route('admin.discounts.index'), 'Créer' => null]" />

    <form method="POST" action="{{ route('admin.discounts.store') }}">
        @csrf
        @include('admin.discounts._form')
    </form>
@endsection
