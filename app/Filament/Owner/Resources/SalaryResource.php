<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\SalaryResource\Pages;
use App\Filament\Owner\Resources\SalaryResource\RelationManagers;
use App\Models\Salary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalaryResource extends Resource
{
    protected static ?string $model = Salary::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $label = 'Gaji';
    protected static ?string $pluralLabel = 'Kelola gaji';
    protected static ?string $slug = 'gaji';
    protected static ?string $navigationLabel = 'Kelola Gaji';
    protected static ?string $navigationGroup = 'Manajemen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label('Karyawan'),
                Forms\Components\DatePicker::make('periode')
                    ->required()
                    ->label('Periode'),
                Forms\Components\TextInput::make('gaji_pokok')
                    ->numeric()
                    ->required()
                    ->label('Gaji Pokok'),
                Forms\Components\TextInput::make('tunjangan')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->label('Tunjangan'),
                Forms\Components\TextInput::make('potongan')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->label('Potongan'),
                Forms\Components\TextInput::make('total_gaji')
                    ->numeric()
                    ->required()
                    ->label('Total Gaji'),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Karyawan')->searchable(),
                Tables\Columns\TextColumn::make('periode')->date()->label('Periode'),
                Tables\Columns\TextColumn::make('gaji_pokok')->money('IDR')->label('Gaji Pokok'),
                Tables\Columns\TextColumn::make('tunjangan')->money('IDR')->label('Tunjangan'),
                Tables\Columns\TextColumn::make('potongan')->money('IDR')->label('Potongan'),
                Tables\Columns\TextColumn::make('total_gaji')->money('IDR')->label('Total Gaji'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListSalaries::route('/'),
            'create' => Pages\CreateSalary::route('/create'),
            'edit' => Pages\EditSalary::route('/{record}/edit'),
        ];
    }
}
