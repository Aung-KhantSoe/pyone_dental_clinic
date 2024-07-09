<?php

namespace App\Filament\Widgets;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Treatment;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getCards(): array
    {
        $treatments = Treatment::all();
        $patients = Patient::all();
        $groupedTreatments = $treatments->groupBy('treatment_date');
        $sums = $groupedTreatments->map(function ($treatments) {
            return $treatments->sum('total');
        });
        $totals_array = $sums->values()->toArray();
        $currentYear = date('Y');
        $currentMonth = Carbon::now()->month;
        $currentMonthName = Carbon::now()->format('F');
        return [
            Card::make('Total Treatments', $treatments->count())
                ->description('Today Treatments : ' . Treatment::where('treatment_date', today())->count())
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Card::make('Total Patients', $patients->count())
                ->description('Today Patients : ' . Patient::whereDate('created_at', today())->count())
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('warning'),
            Card::make('Total Income', number_format($treatments->sum('total'), 0, '.', ','))
                ->description('Today Income : ' . number_format(Treatment::where('treatment_date', today())->sum('total'), 0, '.', ','))
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Card::make($currentMonthName . ' Treatments', Treatment::whereMonth('treatment_date', $currentMonth)
                ->whereYear('treatment_date', $currentYear)
                ->count())
                ->color('success'),
            Card::make($currentMonthName . ' Patients', Patient::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count())
                ->color('warning'),
            Card::make($currentMonthName . ' Income', number_format(Payment::whereMonth('paid_date', $currentMonth)
                ->whereYear('paid_date', $currentYear)
                ->sum('amount'), 0, '.', ','))
                ->color('success'),
        ];
    }
}
