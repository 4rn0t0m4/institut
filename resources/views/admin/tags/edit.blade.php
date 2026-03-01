@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Modifier le tag" :breadcrumbs="['Tags' => route('admin.tags.index'), $tag->name => null]" />

    <form method="POST" action="{{ route('admin.tags.update', $tag) }}">
        @csrf
        @method('PUT')
        @include('admin.tags._form')
    </form>
@endsection
