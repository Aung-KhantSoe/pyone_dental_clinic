<?php

namespace App\Filament\Widgets;

use App\Models\Treatment;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;

class DashboardChart2 extends LineChartWidget
{
    protected static ?string $heading = 'Income';
    protected static ?int $sort = 4;
    public ?string $filter = 'this_year';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $previousYear = date('Y') - 1;
        $currentYear = date('Y');
        $currentMonth = Carbon::now()->month;
        $currentDaysInMonth = Carbon::now()->daysInMonth;
        if ($activeFilter == 'last_year') {
            $total_sumsByMonth = DB::table('payments')
                ->select(DB::raw('DATE_FORMAT(paid_date, "%m") as month'), DB::raw('SUM(amount) as total_sum'))
                ->whereRaw('YEAR(paid_date) = ?', [$previousYear])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            $treatment_charges_sumsByMonth = DB::table('treatments')
                ->select(DB::raw('DATE_FORMAT(treatment_date, "%m") as month'), DB::raw('SUM(treatment_charges) as treatment_charges_sum'))
                ->whereRaw('YEAR(created_at) = ?', [$previousYear])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            [$data1,$data2,$label] = self::twelvemonthsDataCreator();
            foreach ($total_sumsByMonth as $sum) {
                $data1[$sum->month - 1] = $sum->total_sum;
            }
            foreach ($treatment_charges_sumsByMonth as $sum) {
                $data2[$sum->month - 1] = $sum->treatment_charges_sum;
            }
        } elseif ($activeFilter == 'year') {
            $total_sumsByMonth = DB::table('payments')
                ->select(DB::raw('DATE_FORMAT(paid_date, "%m") as month'), DB::raw('SUM(amount) as total_sum'))
                ->whereRaw('YEAR(paid_date) = ?', [$currentYear])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            $treatment_charges_sumsByMonth = DB::table('treatments')
                ->select(DB::raw('DATE_FORMAT(treatment_date, "%m") as month'), DB::raw('SUM(treatment_charges) as treatment_charges_sum'))
                ->whereRaw('YEAR(created_at) = ?', [$currentYear])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            [$data1,$data2,$label] = self::twelvemonthsDataCreator();

            foreach ($total_sumsByMonth as $sum) {
                $data1[$sum->month - 1] = $sum->total_sum;
            }
            foreach ($treatment_charges_sumsByMonth as $sum) {
                $data2[$sum->month - 1] = $sum->treatment_charges_sum;
            }
        } else {
            $total_sumsByDays = DB::table('payments')
                ->select(DB::raw('DATE_FORMAT(paid_date, "%d") as day'), DB::raw('SUM(amount) as total_sum'))
                ->whereRaw('MONTH(paid_date) = ?', [$currentMonth])
                ->groupBy('day')
                ->orderBy('day')
                ->get();
            $treatment_charges_sumsByDays = DB::table('treatments')
                ->select(DB::raw('DATE_FORMAT(treatment_date, "%d") as day'), DB::raw('SUM(treatment_charges) as treatment_charges_sum'))
                ->whereRaw('MONTH(created_at) = ?', [$currentMonth])
                ->groupBy('day')
                ->orderBy('day')
                ->get();
            $data1 = array_fill(0, $currentDaysInMonth, 0);
            $data2 = array_fill(0, $currentDaysInMonth, 0);
            $label = range(1, $currentDaysInMonth);

            foreach ($total_sumsByDays as $sum) {
                $data1[$sum->day - 1] = $sum->total_sum;
            }
            foreach ($treatment_charges_sumsByDays as $sum) {
                $data2[$sum->day - 1] = $sum->treatment_charges_sum;
            }
        }

        return self::calculateFilters($data1, $data2, $label);
    }

    protected function twelvemonthsDataCreator()
    {
        $data1 = array_fill(0, 12, 0);
        $data2 = array_fill(0, 12, 0);
        $label = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return [$data1, $data2, $label];
    }
    protected function calculateFilters($data1, $data2, $label)
    {
        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => $data1,
                    'backgroundColor' => '#4bde80',
                    'borderColor' => '#4bde80',
                ],
                // [
                //     'label' => 'Treatment Charges',
                //     'data' => $data2,
                //     'backgroundColor' => 'crimson',
                //     'borderColor' => 'crimson',
                // ],
            ],
            'labels' => $label
        ];
    }
    protected function getFilters(): ?array
    {
        return [
            'this_month' => 'This month',
            'year' => 'This year',
            'last_year' => 'Last year',
        ];
    }
}
