<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public array $data_list = [
        'calc_columns' => [
            'amount',
        ],
    ];

    protected function getTableContentFooter(): ?View
    {
        return view("table.footer", $this->data_list);
    }
}
