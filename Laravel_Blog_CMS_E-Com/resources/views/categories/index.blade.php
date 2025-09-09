@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-5xl font-bold mb-6">Browse Categories</h1>

    <div class="grid grid-cols-3 gap-6">
        @foreach($categories as $category)
            <div class="border rounded-lg p-6 shadow hover:shadow-lg transition duration-300">
                <a href="{{ route('categories.show', $category->slug) }}" class="text-xl font-semibold text-blue-600 hover:text-blue-800">
                    {{ $category->name }}
                </a>
                @if($category->description)
                    <p class="mt-2 text-gray-600">{{ $category->description }}</p>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
