<x-layouts.app title="Blog">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-3xl font-semibold text-gray-900 mb-8">Blog</h1>

    <turbo-frame id="posts-grid">
        @include('blog.partials.grid', ['posts' => $posts])
    </turbo-frame>
</div>
</x-layouts.app>
