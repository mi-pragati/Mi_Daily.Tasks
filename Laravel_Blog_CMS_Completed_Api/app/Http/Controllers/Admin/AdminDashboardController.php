<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totals = [
            'categories' => Category::count(),
            'posts'      => Post::count(),
            'comments'   => Comment::count(),
        ];

        // Posts by category (for pie chart)
        $postsByCategory = Category::withCount('posts')->orderBy('name')->get();
        $pbcLabels = $postsByCategory->pluck('name');
        $pbcData   = $postsByCategory->pluck('posts_count');

        // Comments by category (doughnut)
        $commentsByCategory = DB::table('categories as c')
            ->leftJoin('posts as p', 'p.category_id', '=', 'c.id')
            ->leftJoin('comments as cm', 'cm.post_id', '=', 'p.id')
            ->select('c.name', DB::raw('COUNT(cm.id) as comments_count'))
            ->groupBy('c.name')
            ->orderBy('c.name')
            ->get();
        $cbcLabels = $commentsByCategory->pluck('name');
        $cbcData   = $commentsByCategory->pluck('comments_count');

        // Posts in last 8 weeks (bar)
$driver = DB::getDriverName();
if ($driver === 'sqlite') {
    $weekExpr = "strftime('%Y-%W', created_at)";
} elseif ($driver === 'mysql') {
    $weekExpr = "date_format(created_at, '%x-%v')";
} elseif ($driver === 'pgsql') {
    $weekExpr = "to_char(created_at, 'IYYY-IW')";
} else {
    $weekExpr = "strftime('%Y-%W', created_at)";
}

$postsPerWeek = Post::select(
        DB::raw("$weekExpr as year_week"),
        DB::raw('COUNT(*) as cnt')
    )
    ->groupBy('year_week')
    ->orderBy('year_week', 'asc')
    ->limit(8)
    ->get();

$ppwLabels = $postsPerWeek->pluck('year_week');
$ppwData   = $postsPerWeek->pluck('cnt');


        return view('admin.dashboard', compact(
            'totals',
            'pbcLabels','pbcData',
            'cbcLabels','cbcData',
            'ppwLabels','ppwData'
        ));
    }
}
