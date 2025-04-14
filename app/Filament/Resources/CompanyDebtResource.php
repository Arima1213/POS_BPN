<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyDebtResource\Pages;
use App\Models\CompanyDebt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompanyDebtResource extends Resource
{
    protected static ?string $model = CompanyDebt::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Company Debts';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('creditor_name')
                    ->label('Creditor Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter creditor name'),

                Forms\Components\TextInput::make('amount')
                    ->label('Total Amount')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->placeholder('Enter total debt amount'),

                Forms\Components\TextInput::make('paid')
                    ->label('Amount Paid')
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp')
                    ->placeholder('Enter amount paid'),

                Forms\Components\DatePicker::make('due_date')
                    ->label('Due Date')
                    ->placeholder('Select due date')
                    ->closeOnDateSelection()
                    ->native(false),

                Forms\Components\Textarea::make('note')
                    ->label('Notes')
                    ->rows(3)
                    ->placeholder('Optional notes about the debt'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('creditor_name')
                    ->label('Creditor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('IDR', true),

                Tables\Columns\TextColumn::make('paid')
                    ->label('Paid')
                    ->money('IDR', true)
                    ->color(fn($record) => $record->paid >= $record->amount ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('remaining')
                    ->label('Remaining')
                    ->getStateUsing(fn($record) => $record->remainingAmount())
                    ->money('IDR', true)
                    ->color(fn($record) => $record->remainingAmount() > 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->since(),
            ])
            ->filters([
                Tables\Filters\Filter::make('Due This Month')
                    ->query(fn(Builder $query) => $query->whereMonth('due_date', now()->month)),
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
            // Optional: Bisa tambahkan RelationManager untuk payments jika sudah ada relasinya.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyDebts::route('/'),
            'create' => Pages\CreateCompanyDebt::route('/create'),
            'edit' => Pages\EditCompanyDebt::route('/{record}/edit'),
        ];
    }
}
