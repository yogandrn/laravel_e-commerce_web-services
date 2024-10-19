<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     // Redirect ke halaman index/list setelah action berhasil
    //     return $this->getResource()::getUrl('edit');
    // }
}
