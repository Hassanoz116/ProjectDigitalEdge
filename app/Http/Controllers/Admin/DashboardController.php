<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics and charts.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get active users count (with verified email)
        $activeUsersCount = User::whereNotNull('email_verified_at')->count();
        
        // Get total products count
        $productsCount = Product::count();
        
        // Get new users this month
        $newUsersCount = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Get activity logs count
        $activityLogsCount = ActivityLog::count();
        
        // Get products created per day for the last 7 days
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $productsByDay = Product::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
        
        // Create an array for the last 7 days
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($date)->format('D');
            $chartData[] = $productsByDay[$date] ?? 0;
        }
        
        return view('admin.dashboard.index', compact(
            'activeUsersCount',
            'productsCount',
            'newUsersCount',
            'activityLogsCount',
            'chartLabels',
            'chartData'
        ));
    }
}
