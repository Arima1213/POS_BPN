<?php

namespace App\Filament\Cashier\Resources\TransactionsResource\Pages;

use App\Filament\Cashier\Resources\TransactionsResource;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\Transactions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactions extends CreateRecord
{
    protected static string $resource = TransactionsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Cek apakah user tambah customer baru
        if ($data['add_new_customer'] ?? false) {
            $customer = Customer::create([
                'name'  => $data['new_customer_name'],
                'phone' => $data['new_customer_phone'],
            ]);
            $data['customer_id'] = $customer->id;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Transactions $record */
        $record = $this->record;

        // Jika uang pembeli kurang dari total, maka buatkan hutang
        if ($record->paid_amount < $record->total) {
            Debt::create([
                'customer_id'    => $record->customer_id,
                'transaction_id' => $record->id,
                'amount'         => $record->total,
                'paid'           => $record->paid_amount,
                'due_date'       => now()->addDays(30),
            ]);
        }
    }
}