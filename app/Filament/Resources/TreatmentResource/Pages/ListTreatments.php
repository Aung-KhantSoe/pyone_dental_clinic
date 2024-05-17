<?php

namespace App\Filament\Resources\TreatmentResource\Pages;

use App\Filament\Resources\TreatmentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListTreatments extends ListRecords
{
    protected static string $resource = TreatmentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public array $data_list = [
        'calc_columns' => [
            'treatment_charges',
            'xray_fees',
            'medication_fees',
            'total',
            'payments_sum_amount',
            'debt'
        ],
    ];
    protected function getTableContentFooter(): ?View
    {
        return view("table.footer", $this->data_list);
    }
}
