@extends('admin.layouts.app')

@push('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        tinymce.init({
            selector: '.tinymce-full',
            height: 500,
            menubar: true,
            plugins: 'lists link image table code fullscreen media hr wordcount',
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | link image media table | hr blockquote | code fullscreen',
            content_css: isDark ? 'dark' : 'default',
            skin: isDark ? 'oxide-dark' : 'oxide',
            branding: false,
            promotion: false,
            language: 'fr_FR',
        });
    });
</script>
@endpush

@section('content')
    <x-admin.page-breadcrumb title="Modifier la page" :breadcrumbs="['Pages' => route('admin.pages.index'), $page->title => null]" />

    <form method="POST" action="{{ route('admin.pages.update', $page) }}">
        @csrf
        @method('PUT')
        @include('admin.pages._form')
    </form>
@endsection
