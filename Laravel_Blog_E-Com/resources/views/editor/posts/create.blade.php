@extends('layouts.app')

@section('content')
<h1>Create Post</h1>
<form method="POST" action="{{ route('posts.store') }}">
    @csrf
    <div>
        <label>Title</label>
        <input name="title" value="{{ old('title') }}" required class="border p-2 w-full">
    </div>

    <div>
        <label>Category</label>
        <select name="category_id" required class="border p-2 w-full">
            @foreach($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Excerpt</label>
        <textarea name="excerpt" class="border p-2 w-full">{{ old('excerpt') }}</textarea>
    </div>

    <div>
        <label>Body</label>
        <textarea name="body" rows="10" class="border p-2 w-full" required>{{ old('body') }}</textarea>
    </div>

    <div>
        <label>Tags</label>
        <select name="tags[]" multiple class="border p-2 w-full">
        @foreach($tags as $tag)
            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
        @endforeach
        </select>
    </div>

    <button class="mt-2 px-4 py-2 bg-blue-600 text-white">Publish</button>
</form>
@endsection
