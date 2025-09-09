@extends('admin.layouts.app')

@section('title','Admin • Dashboard')

@section('content')
<div class="container">
  {{-- Stats --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small">Categories</div>
          <div class="display-6">{{ $totals['categories'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small">Posts</div>
          <div class="display-6">{{ $totals['posts'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small">Comments</div>
          <div class="display-6">{{ $totals['comments'] }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Charts --}}
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h6 class="card-title">Posts by Category</h6>
          <canvas id="postsByCategory"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h6 class="card-title">Comments by Category</h6>
          <canvas id="commentsByCategory"></canvas>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h6 class="card-title">Posts per Week</h6>
          <canvas id="postsPerWeek"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const pbcLabels = @json($pbcLabels);
const pbcData   = @json($pbcData);
new Chart(document.getElementById('postsByCategory'), {
  type: 'pie',
  data: { labels: pbcLabels, datasets: [{ data: pbcData }] },
});

const cbcLabels = @json($cbcLabels);
const cbcData   = @json($cbcData);
new Chart(document.getElementById('commentsByCategory'), {
  type: 'doughnut',
  data: { labels: cbcLabels, datasets: [{ data: cbcData }] },
});

const ppwLabels = @json($ppwLabels);
const ppwData   = @json($ppwData);
new Chart(document.getElementById('postsPerWeek'), {
  type: 'bar',
  data: { labels: ppwLabels, datasets: [{ label: 'Posts', data: ppwData }] },
  options: { scales: { y: { beginAtZero: true, precision:0 } } }
});
</script>
@endpush
