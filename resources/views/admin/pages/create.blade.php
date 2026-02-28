@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Nouvelle page" :breadcrumbs="['Pages' => route('admin.pages.index'), 'Creer' => null]" />

    <form method="POST" action="{{ route('admin.pages.store') }}">
        @csrf
        @include('admin.pages._form')
    </form>
@endsection
