@extends('admin.layouts.app')

@push('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        tinymce.init({
            selector: '.tinymce-full',
            width: '100%',
            min_width: 600,
            height: 500,
            menubar: false,
            plugins: 'lists link image table code fullscreen media hr wordcount',
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | link image media | hr blockquote | code fullscreen',
            content_css: isDark ? 'dark' : 'default',
            skin: isDark ? 'oxide-dark' : 'oxide',
            branding: false,
            promotion: false,
            language: 'fr_FR',
            language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@latest/langs7/fr_FR.js',
            automatic_uploads: true,
            images_reuse_filename: false,
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function (resolve, reject) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '/admin/editor-upload');
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.upload.onprogress = function (e) { progress(e.loaded / e.total * 100); };
                    xhr.onload = function () {
                        if (xhr.status !== 200) { reject('Erreur upload (' + xhr.status + '): ' + xhr.responseText); return; }
                        try { resolve(JSON.parse(xhr.responseText).location); }
                        catch (e) { reject('Réponse invalide'); }
                    };
                    xhr.onerror = function () { reject('Erreur réseau'); };
                    var fd = new FormData();
                    fd.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.send(fd);
                });
            },
        });
    });
</script>
@endpush

@section('content')
    <x-admin.page-breadcrumb title="Modifier {{ $brand->name }}" :breadcrumbs="['Marques' => route('admin.brands.index'), $brand->name => null]" />

    <form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.brands._form')
    </form>
@endsection
