@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow mt-12">
    <h1 class="text-2xl font-bold mb-6">Create Post</h1>

    <form method="POST" action="{{ route('editor.posts.store') }}">
        @csrf

        <!-- Title -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Title</label>
            <input 
                type="text" 
                name="title" 
                value="{{ old('title') }}" 
                required 
                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <!-- Category -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Category</label>
            <select 
                name="category_id" 
                required 
                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                @foreach($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Excerpt -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Excerpt</label>
            <textarea 
                name="excerpt" 
                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            >{{ old('excerpt') }}</textarea>
        </div>

        <!-- Body -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Body</label>
            <textarea 
                name="body" 
                rows="10" 
                required 
                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            >{{ old('body') }}</textarea>
        </div>

        <!-- Tags -->
        <div class="mb-6">
            <label class="block font-medium mb-1">Tags</label>
            <select 
                name="tags[]" 
                multiple 
                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </select>
        </div>

<!-- Submit Button -->
<div class="mt-6 flex justify-start">
    <button 
        type="submit" 
        class="bg-blue-600 text-black py-2 px-6 rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
        Publish Post
    </button>
</div>      
@endsection
