@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Nouveau tag" :breadcrumbs="['Tags' => route('admin.tags.index'), 'Créer' => null]" />

    <form method="POST" action="{{ route('admin.tags.store') }}">
        @csrf
        @include('admin.tags._form')
    </form>
@endsection
