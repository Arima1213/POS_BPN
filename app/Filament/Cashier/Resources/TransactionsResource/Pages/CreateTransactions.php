<?php

namespace App\Filament\Cashier\Resources\TransactionsResource\Pages;

use App\Filament\Cashier\Resources\TransactionsResource;
use App\Models\Customer;
use App\Models\Debt;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactions extends CreateRecord
{
    protected static string $resource = TransactionsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Jika user memilih "Tambah Customer Baru"
        if (!empty($data['add_new_customer']) && $data['add_new_customer'] === true) {
            $customer = Customer::create([
                'name' => $data['new_customer_name'],
                'phone' => $data['new_customer_phone'],
            ]);

            $data['customer_id'] = $customer->id;
        }

        return $data;
    }

    public static function mutateRecordAfterCreate($record): void
    {
        if ($record->paid_amount < $record->total) {
            Debt::create([
                'customer_id'     => $record->customer_id,
                'transaction_id'  => $record->id,
                'amount'          => $record->total,
                'paid'            => $record->paid_amount,
                'due_date'        => now()->addDays(30), // default jatuh tempo 30 hari ke depan (bisa diubah)
            ]);
        }
    }
}
