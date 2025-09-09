@extends('admin.layouts.app')

@section('title', 'Product Categories')

@section('content')
<div class="container">
    <h1 class="my-4">Product Categories</h1>

    {{-- Button to create a new product category --}}
    <a href="{{ route('admin.product-categories.create') }}" class="btn btn-primary mb-3">Create New Category</a>

    {{-- Product Categories Table --}}
    <div class="card">
        <div class="card-body">
            <table id="productCategoriesTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $cat)
                        <tr>
                            <td> <a href="{{ route('admin.products.byCategory', $cat->slug) }}">
                        {{ $cat->name }}
                        </a></td>
                            <td>{{ $cat->slug }}</td>
                            <td>{{ $cat->description }}</td>
                            <td>
                                <a href="{{ route('admin.product-categories.edit', $cat->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('admin.product-categories.destroy', $cat->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $categories->links() }}  {{-- Pagination links --}}
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- DataTables CDN -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#productCategoriesTable').DataTable();
    });
</script>
@endpush
