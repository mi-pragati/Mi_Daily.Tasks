@extends('admin.layouts.app')
@section('title','Admin • Posts')

@section('content')
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Posts</h1>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">Create Post</a>
  </div>

  {{-- Chart: comments per category --}}
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <h6 class="text-muted mb-3">Comments per category</h6>
      <div style="position:relative; height:260px;">
        <canvas id="commentsByCat"></canvas>
      </div>
    </div>
  </div>

  {{-- Posts table --}}
  <div class="card shadow-sm border-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Title</th>
            <th>Category</th>
            <th class="text-nowrap">Author</th>
            <th class="text-nowrap">Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($posts as $post)
            <tr>
              <td>
                <a class="text-decoration-none" href="{{ route('admin.posts.show', $post->slug) }}">
                  {{ $post->title }}
                </a>
              </td>
              <td>{{ optional($post->category)->name ?? '—' }}</td>
              <td>{{ optional($post->author)->name ?? '—' }}</td>
              <td class="text-muted small">{{ optional($post->created_at)->format('d M Y') }}</td>
              <td class="text-end">
                <a href="{{ route('admin.posts.edit', $post->slug) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                <form action="{{ route('admin.posts.destroy', $post->slug) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Delete this post?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-5">No posts found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer bg-white">
      {{ $posts->onEachSide(1)->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  const labels = @json($labels ?? []);
  const data   = @json($totals ?? []);

  new Chart(document.getElementById('commentsByCat'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Comments',
        data,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
  });
</script>
@endpush
