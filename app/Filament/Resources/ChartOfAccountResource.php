<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChartOfAccountResource\Pages;
use App\Models\ChartOfAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChartOfAccountResource extends Resource
{
    protected static ?string $model = ChartOfAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Master Akun';
    protected static ?string $navigationGroup = 'Akuntansi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->label('Kode Akun')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('nama')
                    ->label('Nama Akun')
                    ->required(),

                Forms\Components\Select::make('kelompok')
                    ->label('Kelompok Akun')
                    ->options([
                        'aset' => 'Aset',
                        'kewajiban' => 'Kewajiban',
                        'ekuitas' => 'Ekuitas',
                        'pendapatan' => 'Pendapatan',
                        'beban' => 'Beban',
                    ])
                    ->required(),

                Forms\Components\Select::make('tipe')
                    ->label('Tipe Akun')
                    ->options([
                        'lancar' => 'Lancar',
                        'tetap' => 'Tetap',
                        'jangka_pendek' => 'Jangka Pendek',
                        'jangka_panjang' => 'Jangka Panjang',
                        'modal' => 'Modal',
                        'operasional' => 'Operasional',
                        'non_operasional' => 'Non Operasional',
                    ])
                    ->required(),

                Forms\Components\Select::make('jenis_beban')
                    ->label('Jenis Beban')
                    ->options([
                        'beban_kas' => 'Beban Kas',
                        'beban_non_kas' => 'Beban Non Kas',
                        'beban_usaha' => 'Beban Usaha',
                        'beban_operasional' => 'Beban Operasional',
                        'beban_lainnya' => 'Beban Lainnya',
                    ])
                    ->nullable()
                    ->visible(fn(Forms\Get $get) => $get('kelompok') === 'beban'),

                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')->label('Kode')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('kelompok')->label('Kelompok'),
                Tables\Columns\TextColumn::make('tipe')->label('Tipe'),
                Tables\Columns\TextColumn::make('jenis_beban')->label('Jenis Beban')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deskripsi')->label('Deskripsi')->limit(30)->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelompok')
                    ->label('Filter Kelompok')
                    ->options([
                        'aset' => 'Aset',
                        'kewajiban' => 'Kewajiban',
                        'ekuitas' => 'Ekuitas',
                        'pendapatan' => 'Pendapatan',
                        'beban' => 'Beban',
                    ]),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChartOfAccounts::route('/'),
            'create' => Pages\CreateChartOfAccount::route('/create'),
            'edit' => Pages\EditChartOfAccount::route('/{record}/edit'),
        ];
    }
}