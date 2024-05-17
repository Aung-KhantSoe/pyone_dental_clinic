<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TreatmentResource\Pages;
use App\Filament\Resources\TreatmentResource\RelationManagers;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Treatment;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\NumberFilter;

class TreatmentResource extends Resource
{
    protected static ?string $model = Treatment::class;

    protected static ?string $navigationIcon = 'vaadin-tooth';

    public static function form(Form $form): Form
    {
        $user_id = Auth::user()->id;
        return $form
            ->schema([
                //

                Select::make('doctor_id')
                    ->label('Doctor')
                    ->searchable()
                    ->required()
                    ->options(Doctor::all()->pluck('name', 'id')),
                Select::make('patient_id')
                    ->label('Patient')
                    ->searchable()
                    ->required()
                    ->options(Patient::all()->pluck('name', 'id')),
                Hidden::make('user_id')
                    ->default($user_id),
                Forms\Components\TextInput::make('treatment_type')
                    ->label('Treatment type')
                    ->required(),
                Forms\Components\TextArea::make('diagnosis')
                    ->rows(1)
                    ->cols(20)
                    ->label('Diagnosis')
                    ->required(),
                Forms\Components\DatePicker::make('treatment_date')
                    ->label('Treatment Date')
                    ->default(today())
                    ->required(),
                Forms\Components\TextInput::make('treatment_charges')
                    ->label('Treatment Charges')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('xray_fees')
                    ->label('Xray Charges')
                    ->default(0)
                    ->numeric(),
                Forms\Components\TextInput::make('medication_fees')
                    ->label('Medication Charges')
                    ->default(0)
                    ->numeric(),
                TextInput::make('total')
                    ->label('Total')
                    ->required()
                    ->numeric(),
                Grid::make()
                    ->schema([
                        Repeater::make('payments')
                            ->relationship('payments')
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextInput::make('amount')
                                            ->label('Paid Amount')
                                            ->required()
                                            ->columnSpan(1),
                                        DatePicker::make('paid_date')
                                            ->label('Paid Date')
                                            ->default(now())
                                            ->required()
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2), // Ensures the grid has 2 columns
                            ]),
                        Repeater::make('attachments')
                            ->relationship('attachments')
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        FileUpload::make('location')->image()->label('Image')->required()
                                    ])
                                    ->columns(1), // If you want attachments to be in a single column
                            ]),
                    ])
                    ->columns(2),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doctor.name')->label('Doctor')->searchable(),
                TextColumn::make('patient.name')->label('Patient')->searchable(),
                TextColumn::make('treatment_date')->label('Treatment Date')->searchable()->sortable(),
                TextColumn::make('treatment_charges')->label('Treatment Charges')->formatStateUsing(fn (string $state): string => number_format($state, 0, '.', ','))->color('primary')->searchable()->sortable(),
                TextColumn::make('xray_fees')->label('Xray Charges')->formatStateUsing(fn (string $state): string => number_format($state, 0, '.', ','))->color('primary')->searchable()->sortable(),
                TextColumn::make('medication_fees')->label('Medication Charges')->formatStateUsing(fn (string $state): string => number_format($state, 0, '.', ','))->color('primary')->searchable()->sortable(),
                TextColumn::make('total')->label('Total')->formatStateUsing(fn (string $state): string => number_format($state, 0, '.', ','))->color('primary')->searchable()->sortable(),
                TextColumn::make('payments_sum_amount')->sum('payments','amount')->label('Paid')->formatStateUsing(fn (string $state): string => number_format($state, 0, '.', ','))->color('success')->sortable(),
                TextColumn::make('debt')->color('danger')
                    ->getStateUsing(function(Model $record) {
                        return $record->total - $record->payments_sum_amount;
                    })->formatStateUsing(fn (string $state): string => number_format($state, 0, '.', ','))

            ])
            // ->addColumn('debt', function ($resource) {
            //     return $resource->total - $resource->payments_sum_amount;
            // })
            ->filters([
                //
                SelectFilter::make('doctor_id')
                    ->label('Doctor')
                    ->relationship('doctor', 'name'),
                DateFilter::make('treatment_date'),
                NumberFilter::make('treatment_charges'),
                NumberFilter::make('xray_fees'),
                NumberFilter::make('medication_fees'),
                NumberFilter::make('total'),

                // Filter::make('created_at')
                //     ->form([
                //         Forms\Components\DatePicker::make('created_from'),
                //         Forms\Components\DatePicker::make('created_until'),
                //     ])
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query
                //             ->when(
                //                 $data['created_from'],
                //                 fn (Builder $query, $date): Builder => $query->whereDate('treatment_date', '>=', $date),
                //             )
                //             ->when(
                //                 $data['created_until'],
                //                 fn (Builder $query, $date): Builder => $query->whereDate('treatment_date', '<=', $date),
                //             );
                //     })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()
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
            'index' => Pages\ListTreatments::route('/'),
            'create' => Pages\CreateTreatment::route('/create'),
            'edit' => Pages\EditTreatment::route('/{record}/edit'),
            'view' => Pages\ViewTreatment::route('/{record}'),
        ];
    }
}
