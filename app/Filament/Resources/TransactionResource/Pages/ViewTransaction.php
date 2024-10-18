<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    // protected static 

    // public function form(Form $form) : Form {
    //     return $form->schema([

    //         TextInput::make('order_code')
    //         ->label('Kode Transaksi')
    //         ->readOnly()
    //         ->readO
    //         ->string(),

    //         TextInput::make('subtotal')
    //                 ->label('Subtotal produk')
    //                 ->prefix('Rp ')
    //                 ->readOnly()
    //                 ->numeric(),
                    
    //         TextInput::make('delivery_fee')
    //                 ->label('Biaya kirim')
    //                 ->prefix('Rp ')
    //                 ->readOnly()
    //                 ->numeric(),

    //         TextInput::make('additional_fee')
    //                 ->label('Biaya layanan')
    //                 ->prefix('Rp ')
    //                 ->readOnly()
    //                 ->numeric(),
            
    //         Select::make('status')
    //                 ->label('Status')
    //                 ->options([
    //                     'PENDING' => 'Pending',
    //                     'ON_DELIVERY' => 'On Delivery',
    //                     'SUCCESS' => 'Success',
    //                     'CANCELED' => 'Canceled',
    //                 ])
    //                 ->readOnly(),
    //                 ]);
    // }

    protected function getCardSchema(): array
    {
        // Ambil data transaksi dan relasinya
        $transaction = $this->record;

        return [
            Card::make([
                'Order Code' => $transaction->order_code,
                'User' => $transaction->user->name ?? 'N/A', // Menampilkan nama user
                'Subtotal' => number_format($transaction->subtotal, 2),
                'Delivery Fee' => number_format($transaction->delivery_fee, 2),
                'Additional Fee' => number_format($transaction->additional_fee, 2),
                'Total' => number_format($transaction->total, 2),
                'Payment URL' => $transaction->payment_url,
                'Receipt Code' => $transaction->receipt_code,
                'Status' => $transaction->status,
                'Street Address' => $transaction->address->street ?? 'N/A',
                'City' => $transaction->address->city ?? 'N/A',
                'ZIP Code' => $transaction->address->zip ?? 'N/A',
                'Created At' => $transaction->created_at->format('Y-m-d H:i:s'),
            ])
            ->columns(2) // Menentukan jumlah kolom
            ->extraAttributes(['class' => 'space-y-4']),
        ];
    }

}
