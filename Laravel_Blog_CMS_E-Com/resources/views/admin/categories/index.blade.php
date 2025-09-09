@extends('admin.layouts.app')
@section('title','Admin • Categories')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Categories</h1>
    <a class="btn btn-primary btn-sm" href="{{ route('admin.categories.create') }}">Create Category</a>
  </div>

  {{-- Chart: Posts per category --}}
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <h2 class="h6 text-muted mb-3">Posts per category</h2>
      <canvas id="catPostsChart" height="100"></canvas>
    </div>
  </div>

  {{-- List --}}
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Name</th>
              <th>Slug</th>
              <th class="text-end">Posts</th>
            </tr>
          </thead>
          <tbody>
            @foreach($categories as $cat)
              <tr>
                <td>
                  <a href="{{ route('admin.categories.show', $cat->slug) }}" class="text-decoration-none">
                    {{ $cat->name }}
                  </a>
                </td>
                <td class="text-muted small">{{ $cat->slug }}</td>
                <td class="text-end">
                  <span class="badge bg-light text-dark">{{ $cat->posts_count }}</span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (function(){
    const ctx = document.getElementById('catPostsChart').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: @json($labels),
        datasets: [{
          label: 'Posts',
          data: @json($counts),
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true, ticks: { precision:0 } }
        }
      }
    });
  })();
</script>
@endpush
