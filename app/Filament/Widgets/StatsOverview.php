<?php

namespace App\Filament\Widgets;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Treatment;
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

        return [
            Card::make('Total Treatments', $treatments->count())
                ->description('Today Treatments : '.Treatment::where('treatment_date', today())->count())
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Card::make('Total Patients', $patients->count())
                ->description('Today Patients : '. Patient::whereDate('created_at', today())->count())
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('warning'),
            Card::make('Total Income', number_format($treatments->sum('total'),0,'.',','))
                ->description('Today Income : '.number_format(Treatment::where('treatment_date', today())->sum('total'),0,'.',','))
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
