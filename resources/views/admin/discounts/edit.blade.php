@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Modifier le code promo" :breadcrumbs="['Codes promo' => route('admin.discounts.index'), $discount->name => null]" />

    <form method="POST" action="{{ route('admin.discounts.update', $discount) }}">
        @csrf
        @method('PUT')
        @include('admin.discounts._form')
    </form>
@endsection
