<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Doctor;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'vaadin-money';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('treatment.doctor.name')->label('Doctor')->searchable()->sortable(),
                TextColumn::make('paid_date')->label('Paid Date')->searchable()->sortable(),
                TextColumn::make('amount')
                    ->formatStateUsing(fn (string $state): string => number_format($state, 0, '.', ','))
                    ->color('success')
                    ->label('Amount')
                    ->searchable(),

            ])->defaultSort('paid_date', 'desc')
            ->filters([
                //
                Filter::make('Today')
                    ->label('Today')
                    ->query(function (Builder $query) {
                        $query->where(function ($query) {
                            $query->whereDate('paid_date', Carbon::today());
                        });
                    }),
                Filter::make('Yesterday')
                    ->label('Yesterday')
                    ->query(function (Builder $query) {
                        $query->where(function ($query) {
                            $query->whereDate('paid_date', Carbon::yesterday());
                        });
                    }),
                SelectFilter::make('doctor')
                    ->label('Doctor')
                    ->options(Doctor::all()->pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value']))
                        {
                            $query->whereHas(
                                'treatment',
                                fn (Builder $query) => $query->whereHas(
                                    'doctor',
                                    fn (Builder $query) => $query->where('id', '=', (int) $data['value'])
                                )
                            );
                        }
                    }),
                DateFilter::make('paid_date'),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            // 'create' => Pages\CreatePayment::route('/create'),
            // 'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
