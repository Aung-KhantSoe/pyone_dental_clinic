<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;



class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    // protected function handleRecordCreation(array $data): Model
    // {
    //     $record =  static::getModel()::create($data);
    //     $record->treatments()->create($data['detail']);

    //     return $record;
    // }
}

