<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportStatistics;

class DashboardController extends Controller
{
    public function __invoke(ReportStatistics $stats)
    {
        return view('admin.dashboard', [
            'summary' => $stats->summary(),
            'trend' => $stats->monthlyTrend(8),
            'categories' => $stats->categoryDistribution(),
            'locations' => $stats->topLocations(),
            'latestReports' => $stats->latestReports(),
        ]);
    }
}
