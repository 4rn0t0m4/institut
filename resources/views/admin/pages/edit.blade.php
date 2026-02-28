@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Modifier la page" :breadcrumbs="['Pages' => route('admin.pages.index'), $page->title => null]" />

    <form method="POST" action="{{ route('admin.pages.update', $page) }}">
        @csrf
        @method('PUT')
        @include('admin.pages._form')
    </form>
@endsection
