<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ExpanseExporter;
use App\Filament\Resources\ExpanseResource\Pages;
use App\Filament\Resources\ExpanseResource\RelationManagers;
use App\Models\Expanse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpanseResource extends Resource
{
    protected static ?string $model = Expanse::class;

    // protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?string $navigationLabel = 'Beban Operasional';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Inventaris';
    protected static ?string $pluralLabel = 'Kelola Beban Operasional';
    protected static ?string $slug = 'kelola-beban-operasional';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\TextInput::make('deskripsi')
                    ->required(),
                Forms\Components\Select::make('akun_beban_id')
                    ->label('Akun Beban')
                    ->options(
                        \App\Models\ChartOfAccount::where('kelompok', 'beban')->orderBy('kode')->pluck('nama', 'id')
                    )
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('jumlah')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('akunBeban.nama')
                    ->label('Akun Beban')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('to')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn(Builder $query, $date) => $query->whereDate('tanggal', '>=', $date))
                            ->when($data['to'], fn(Builder $query, $date) => $query->whereDate('tanggal', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make()
                    ->exporter(ExpanseExporter::class)

            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpanses::route('/'),
            'create' => Pages\CreateExpanse::route('/create'),
            'edit' => Pages\EditExpanse::route('/{record}/edit'),
        ];
    }
}