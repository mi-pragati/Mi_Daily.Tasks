@extends('admin.layouts.app')

@section('title','Admin • Dashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">

 {{-- Recent Orders --}}
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-body">
         <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">Recent Orders</h3>

    <select id="orderFilter" class="form-select form-select-sm" style="width: 200px;">
        <option value="">All Statuses</option>
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
        <option value="Returned">Returned</option>
        <option value="Rejected">Rejected</option>
    </select>
</div>

        <div class="table-responsive">
          <table class="table table-striped table-sm" id="recentOrdersTable">
            <thead>
              <tr>
                <th>Id</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="5" class="text-center">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

 {{-- Stats --}}
 <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small mb-1">Categories</div>
          <div class="h5">{{ $totals['categories'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small mb-1">Posts</div>
          <div class="h5">{{ $totals['posts'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small mb-1">Comments</div>
          <div class="h5">{{ $totals['comments'] }}</div>
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
/* -------- Charts -------- */
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

<script>
function loadRecentOrders(filter = '') {
    let url = "{{ route('admin.orders.recentOrders') }}";
    if (filter) {
        url += '?status=' + encodeURIComponent(filter);
    }

    fetch(url, {
        headers: {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: "same-origin"
    })
    .then(res => res.json())
    .then(data => {
        const tbody = document.querySelector("#recentOrdersTable tbody");
        tbody.innerHTML = "";

        if (!data || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center">No orders found</td></tr>`;
            return;
        }

        let html = "";
        data.forEach(order => {
            let orderUrl = "{{ url('admin/orders') }}/" + order.id;
            html += `
                <tr style="cursor:pointer;" onclick="window.location='${orderUrl}'">
                    <td>${order.id}</td>
                    <td>${order.user ? order.user.name : 'Guest'}</td>
                    <td>₹${order.total}</td>
                    <td><span class="badge bg-${order.status === 'Completed' ? 'success' : 'warning'}">${order.status}</span></td>
                    <td>${new Date(order.created_at).toLocaleString()}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    })
    .catch(err => {
        console.error("Fetch error:", err);
        const tbody = document.querySelector("#recentOrdersTable tbody");
        tbody.innerHTML = `<tr><td colspan="5" class="text-danger text-center">Error loading orders</td></tr>`;
    });
}

document.addEventListener("DOMContentLoaded", function() {
    loadRecentOrders();

    document.getElementById("orderFilter").addEventListener("change", function() {
        loadRecentOrders(this.value);
    });
});

</script>
@endpush
