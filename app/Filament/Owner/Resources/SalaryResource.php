<?php

namespace App\Filament\Owner\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Salary;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\JournalEntry;
use App\Models\SalaryHistory;
use App\Models\ChartOfAccount;
use Filament\Resources\Resource;
use App\Models\JournalEntryDetail;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Owner\Resources\SalaryResource\Pages;
use App\Filament\Owner\Resources\SalaryResource\Pages\EditSalary;
use App\Filament\Owner\Resources\SalaryResource\RelationManagers;
use App\Filament\Owner\Resources\SalaryResource\Pages\CreateSalary;
use App\Filament\Owner\Resources\SalaryResource\Pages\ListSalaries;

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
                Action::make('processSalary')
                    ->label('Gaji')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penggajian')
                    ->modalSubheading('Apakah Anda yakin ingin menggaji karyawan ini?')
                    ->action(function ($record, $livewire) {
                        $today = now();
                        $periode = $record->periode->format('Y-m'); // Periode gaji (contoh: 2025-05)

                        // Cek jika sudah ada history untuk bulan dan tahun ini
                        $sudahDigaji = SalaryHistory::where('salary_id', $record->id)
                            ->whereMonth('tanggal_pembayaran', $today->month)
                            ->whereYear('tanggal_pembayaran', $today->year)
                            ->exists();

                        if ($sudahDigaji) {
                            throw new Halt("Gaji bulan ini sudah dibayarkan.");
                        }

                        // 1. Simpan ke SalaryHistory
                        $salaryHistory = SalaryHistory::create([
                            'salary_id' => $record->id,
                            'processed_by' => auth()->id(),
                            'tanggal_pembayaran' => $today,
                        ]);

                        // 2. Simpan ke JournalEntry
                        $journalEntry = JournalEntry::create([
                            'tanggal' => $today,
                            'kode' => 'GAJI-' . strtoupper($record->user->id) . '-' . $today->format('Ym'),
                            'keterangan' => 'Pembayaran Gaji ' . $record->user->name . ' Periode ' . $record->periode->format('F Y'),
                            'kategori' => 'beban_operasional',
                        ]);

                        // 3. Buat detail jurnal: Debit ke akun Beban Gaji (5000)
                        $akunBebanGaji = ChartOfAccount::where('kode', '5000')->firstOrFail();

                        JournalEntryDetail::create([
                            'journal_entry_id' => $journalEntry->id,
                            'chart_of_account_id' => $akunBebanGaji->id,
                            'tipe' => 'debit',
                            'jumlah' => $record->total_gaji,
                            'deskripsi' => 'Gaji karyawan: ' . $record->user->name,
                        ]);

                        // 4. Buat detail jurnal: Kredit ke akun Kas (1000)
                        $akunKas = ChartOfAccount::where('kode', '1000')->firstOrFail();

                        JournalEntryDetail::create([
                            'journal_entry_id' => $journalEntry->id,
                            'chart_of_account_id' => $akunKas->id,
                            'tipe' => 'kredit',
                            'jumlah' => $record->total_gaji,
                            'deskripsi' => 'Pembayaran gaji karyawan',
                        ]);
                    })
                    ->disabled(function ($record) {
                        $today = now();

                        // Disable jika sudah ada penggajian untuk bulan & tahun ini
                        return SalaryHistory::where('salary_id', $record->id)
                            ->whereMonth('tanggal_pembayaran', $today->month)
                            ->whereYear('tanggal_pembayaran', $today->year)
                            ->exists();
                    }),
                Action::make('riwayatGaji')
                    ->color('white')
                    ->label('Riwayat')
                    ->icon('heroicon-m-eye')
                    ->modalHeading('Riwayat Penggajian')
                    ->modalContent(function (Salary $record) {
                        $histories = $record->histories()->orderBy('tanggal_pembayaran', 'desc')->get();

                        if ($histories->isEmpty()) {
                            return new HtmlString('<p>Belum ada riwayat penggajian.</p>');
                        }

                        $html = '<ul class="space-y-2">';
                        foreach ($histories as $history) {
                            $tanggal = \Carbon\Carbon::parse($history->tanggal_pembayaran)->format('d-m-Y');
                            $petugas = $history->processedBy ? $history->processedBy->name : 'Tidak diketahui';
                            $html .= "<li><strong>{$tanggal}:</strong> Diproses oleh {$petugas}</li>";
                        }
                        $html .= '</ul>';

                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')

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