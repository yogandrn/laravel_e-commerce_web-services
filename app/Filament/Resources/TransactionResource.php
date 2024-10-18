<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        
        return $form
            ->schema([
                TextInput::make('order_code')
                        ->label('Kode Transaksi')
                        ->readOnly()
                        ->string(),

                TextInput::make('subtotal')
                        ->label('Subtotal produk')
                        ->prefix('Rp ')
                        ->readOnly()
                        ->numeric(),
                        
                TextInput::make('delivery_fee')
                        ->label('Biaya kirim')
                        ->prefix('Rp ')
                        ->readOnly()
                        ->numeric(),

                TextInput::make('additional_fee')
                        ->label('Biaya layanan')
                        ->prefix('Rp ')
                        ->readOnly()
                        ->numeric(),
                        
                TextInput::make('payment_url')
                        ->label('No. Resi')
                        ->url()
                        ->minLength(8)
                        ->maxLength(255)
                        ->readOnly(),
                        
                TextInput::make('receipt_code')
                        ->label('No. Resi')
                        ->string()
                        ->minLength(0)
                        ->maxLength(255)
                        ->regex('/^[A-Z0-9\s]*$/')
                        ->readOnly(fn ( Get $get) => $get('status') === 'SUCCESS' || $get('status') === 'CANCELED'),

                Select::make('status')
                        ->label('Status')
                        ->options(fn (Get $get) => 
                            $get('status') === 'PENDING' || $get('status') === 'ON_DELIVERY' ?
                                [
                                    'PENDING' => 'Pending',
                                    'ON_DELIVERY' => 'On Delivery',
                            ] : [
                                'PENDING' => 'Pending',
                                'ON_DELIVERY' => 'On Delivery',
                                'SUCCESS' => 'Success',
                                'CANCELED' => 'Canceled',
                            ]
                        )
                        ->placeholder('Pilih Status')
                        ->required()
                        ->disabled(fn ( Get $get) => $get('status') === 'SUCCESS' || $get('status') === 'CANCELED'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')
                        ->label('Kode Transaksi'),

                TextColumn::make('created_at')
                        ->label('Tanggal Transaksi')
                        ->date('d M Y H:i:s'),

                TextColumn::make('total')
                        ->prefix('Rp '),
                
                TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'PENDING' => 'warning',
                    'ON_DELIVERY' => 'info',
                    'SUCCESS' => 'success',
                    'CANCELED' => 'danger',
                }),
            ])
            ->filters([
                // filter status
                SelectFilter::make('status')
                ->label('Status')
                ->options([
                    'PENDING' => 'Pending',
                    'SUCCESS' => 'Success',
                    'CANCELED' => 'Canceled',
                ]),

                // filter tanggal
                Filter::make('created_at')
                ->form([
                    DatePicker::make('created_from'),
                    DatePicker::make('created_until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'DESC');
    }

    public static  function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            
            Card::make()->schema([

                Grid::make(3)->schema([

                    TextEntry::make('order_code')
                            ->label('Kode Transaksi'),
                    
                    TextEntry::make('created_at')
                            ->date('d M Y H:i:s')
                            ->label('Waktu dan Tanggal'),
                    
                    TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'PENDING' => 'warning',
                                'ON_DELIVERY' => 'info',
                                'SUCCESS' => 'success',
                                'CANCELED' => 'danger',
                            }),

                    TextEntry::make('payment_url')
                            ->label('Link Pembayaran')
                            ->placeholder('Not Set'),

                    TextEntry::make('receipt_code')
                            ->label('No. Resi')
                            ->placeholder('Not Set'),

                    TextEntry::make('total')
                            ->label('Total Pembayaran')
                            ->prefix('Rp '),
                ]),

                Section::make('Data Pelanggan')->schema([
            
                    TextEntry::make('user.name')
                            ->label('Nama')
                            ->placeholder('Not set'),
                        
                    TextEntry::make('user.phone_number')
                            ->label('Email')
                            ->placeholder('Not set'),
            
                    TextEntry::make('user.phone_number')
                            ->label('No. Telepon')
                            ->placeholder('Not set'),
            
                ])->columns(3),

                Section::make('Alamat Pengiriman')->schema([
            
                    TextEntry::make('address.name')
                            ->label('Nama Penerima')
                            ->placeholder('Not set'),
                        
                    TextEntry::make('address.phone_number')
                            ->label('No. Telepon')
                            ->placeholder('Not set'),
            
                    TextEntry::make('address.address')
                            ->label('Alamat')
                            ->placeholder('Not set'),
                        
                    TextEntry::make('address.postal_code')
                            ->label('Kode Pos')
                            ->placeholder('Not set'),
            
                ])->columns(4),

                Section::make('Rincian Pesanan')->schema([
                    TextEntry::make('subtotal')
                            ->label('Subtotal pesanan')
                            ->prefix('Rp '),
                    
                    TextEntry::make('delivery_fee')
                            ->label('Biaya pengiriman')
                            ->prefix('Rp '),

                    TextEntry::make('additional_fee')
                            ->label('Biaya tambahan')
                            ->prefix('Rp '),
                    
                    TextEntry::make('total')
                            ->label('Total Pembayaran')
                            ->prefix('Rp '),
                    
                ])->columns(4),
                    
                RepeatableEntry::make('items')
                    ->schema([
                        Grid::make(5)->schema([
                            ImageEntry::make('product.thumbnail')->hiddenLabel()->width(40)->height(40),
                            TextEntry::make('product.name')->hiddenLabel(),
                            TextEntry::make('product.price')->prefix('Rp ')->hiddenLabel(),
                            TextEntry::make('quantity')->hiddenLabel(),
                            TextEntry::make('subtotal')->prefix('Rp ')->hiddenLabel(),
                        ]),

                    ])->label('Item Produk'),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }
}
