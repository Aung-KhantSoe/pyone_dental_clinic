<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\NumberFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'fontisto-bed-patient';

    public static function form(Form $form): Form
    {
        $user_id = Auth::user()->id;
        return $form
            ->schema([
                Forms\Components\TextInput::make('patientID')
                    ->label('Patient ID')
                    ->nullable(),

                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required(),

                Forms\Components\TextInput::make('age')
                    ->label('Age')
                    ->nullable()
                    ->numeric(),

                Forms\Components\Select::make('gender')
                    ->label('Gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ])
                    ->nullable(),

                Forms\Components\Textarea::make('address')
                    ->label('Address')
                    ->nullable(),
                Forms\Components\Textarea::make('treatment_history')
                    ->label('Medication History')
                    ->nullable(),

                Forms\Components\TextInput::make('phone1')
                    ->label('Phone 1')
                    ->required(),

                Forms\Components\TextInput::make('phone2')
                    ->label('Phone 2')
                    ->nullable(),

                Grid::make()
                    ->schema([
                        Repeater::make('treatments')
                            ->relationship('treatments')
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        Select::make('doctor_id')
                                            ->label('Doctor')
                                            ->searchable()
                                            ->required()
                                            ->options(Doctor::all()->pluck('name', 'id'))
                                            ->columnSpan(1),

                                        Hidden::make('user_id')
                                            ->default($user_id),
                                        Forms\Components\TextInput::make('treatment_type')
                                            ->label('Treatment type')
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\TextArea::make('diagnosis')
                                            ->rows(1)
                                            ->cols(20)
                                            ->label('Diagnosis')
                                            ->columnSpan(1),
                                        Forms\Components\DatePicker::make('treatment_date')
                                            ->label('Treatment Date')
                                            ->required()
                                            ->default(today())
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('treatment_charges')
                                            ->label('Treatment Charges')
                                            ->numeric()
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('xray_fees')
                                            ->label('Xray Charges')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('medication_fees')
                                            ->label('Medication Charges')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->columnSpan(1),
                                        TextInput::make('total')
                                            ->label('Total')
                                            ->numeric()
                                            ->required()
                                            ->columnSpan(1),
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
                                    ->columns(2), // Ensures the grid has 2 columns
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2), // Ensures the grid has 2 columns

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                // Tables\Columns\TextColumn::make('id')->label('No'),
                Tables\Columns\TextColumn::make('patientID')->label('Patient ID')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable(),
                Tables\Columns\TextColumn::make('age')->label('Age')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('gender')->label('Gender')->colors([
                    'success' => 'male',
                    'danger' => 'female',
                    'secondary' => 'other'
                ])->searchable(),
                // Tables\Columns\TextColumn::make('address')->label('Address')->searchable(),
                Tables\Columns\TextColumn::make('phone1')->label('Phone 1')->searchable(),
                // Tables\Columns\TextColumn::make('treatments_count')->counts('treatments')->label('Treatment Count')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime()->searchable()->sortable(),

            ])->defaultSort('created_at','desc')
            ->filters([
                // Filter::make('created_at')
                //     ->form([
                //         Forms\Components\DatePicker::make('created_from'),
                //         Forms\Components\DatePicker::make('created_until'),
                //     ])
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query
                //             ->when(
                //                 $data['created_from'],
                //                 fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                //             )
                //             ->when(
                //                 $data['created_until'],
                //                 fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                //             );
                //     }),
                Filter::make('Today')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', Carbon::today())),
                Filter::make('Yesterday')
                    ->label('Yesterday')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', Carbon::yesterday())),
                DateFilter::make('created_at'),

                TextFilter::make('patientID')->label('Patient ID'),
                TextFilter::make('name'),
                NumberFilter::make('age'),
                TextFilter::make('phone1'),

                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ])->searchable(),


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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
            'view' => Pages\ViewPatient::route('/{record}'),
        ];
    }
}
