<?php

namespace App\Filament\Widgets;

use App\Models\Treatment;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;

class DashboardChart2 extends LineChartWidget
{
    protected static ?string $heading = 'Income';
    protected static ?int $sort = 4;
    public ?string $filter = 'this_year';

    protected function getData(): array
    {
        $treatments = Treatment::all();

        $activeFilter = $this->filter;
        if ($activeFilter == 'last_year') {
            $previousYear = date('Y') - 1;
            $total_sumsByMonth = DB::table('treatments')
                ->select(DB::raw('DATE_FORMAT(treatment_date, "%m") as month'), DB::raw('SUM(total) as total_sum'))
                ->whereRaw('YEAR(created_at) = ?', [$previousYear])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            $treatment_charges_sumsByMonth = DB::table('treatments')
                ->select(DB::raw('DATE_FORMAT(treatment_date, "%m") as month'), DB::raw('SUM(treatment_charges) as treatment_charges_sum'))
                ->whereRaw('YEAR(created_at) = ?', [$previousYear])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } else {
            $total_sumsByMonth = DB::table('treatments')
                ->select(DB::raw('DATE_FORMAT(treatment_date, "%m") as month'), DB::raw('SUM(total) as total_sum'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            $treatment_charges_sumsByMonth = DB::table('treatments')
                ->select(DB::raw('DATE_FORMAT(treatment_date, "%m") as month'), DB::raw('SUM(treatment_charges) as treatment_charges_sum'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        $total_monthlyTotals = array_fill(0, 12, 0);
        $treatment_charges_monthlyTotals = array_fill(0, 12, 0);

        foreach ($total_sumsByMonth as $sum) {
            $total_monthlyTotals[$sum->month - 1] = $sum->total_sum;
        }
        foreach ($treatment_charges_sumsByMonth as $sum) {
            $treatment_charges_monthlyTotals[$sum->month - 1] = $sum->treatment_charges_sum;
        }
        // dd($monthlyTotals);
        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => $total_monthlyTotals,
                    'backgroundColor' => '#4bde80',
                    'borderColor' => '#4bde80',
                ],
                [
                    'label' => 'Treatment Charges',
                    'data' => $treatment_charges_monthlyTotals,
                    'backgroundColor' => 'crimson',
                    'borderColor' => 'crimson',
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
