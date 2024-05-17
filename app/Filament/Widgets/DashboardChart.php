<?php

namespace App\Filament\Widgets;

use App\Models\Treatment;
use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;

class DashboardChart extends BarChartWidget
{
    protected static ?string $heading = 'Treatments';
    protected static ?int $sort = 2;
    public ?string $filter = 'this_year';


    protected function getData(): array
    {
        $activeFilter = $this->filter;
        if ($activeFilter == 'last_year') {
            $previousYear = date('Y') - 1;
            $sumsByMonth = DB::table('treatments')
                ->select(DB::raw('DATE_FORMAT(treatment_date, "%m") as month'), DB::raw('count(id) as total_count'))
                ->whereRaw('YEAR(created_at) = ?', [$previousYear])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } else {
            $sumsByMonth = DB::table('treatments')
                ->select(DB::raw('DATE_FORMAT(treatment_date, "%m") as month'), DB::raw('count(id) as total_count'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        // dd($sumsByMonth);
        $monthlyTotals = array_fill(0, 12, 0);

        // Populate the array with the sums from the query result
        foreach ($sumsByMonth as $sum) {
            $monthlyTotals[$sum->month - 1] = $sum->total_count;
        }
        // dd($monthlyTotals);
        return [
            'datasets' => [
                [
                    'label' => 'Treatments',
                    'data' => $monthlyTotals,
                    'backgroundColor' => '#fbbf23',
                    'borderColor' => '#fbbf23',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'year' => 'This year',
            'last_year' => 'Last year',
        ];
    }
}
