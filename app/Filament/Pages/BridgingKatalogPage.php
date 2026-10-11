<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BridgingMappingResource;
use Filament\Actions\Action;
use Filament\Pages\Page;

class BridgingKatalogPage extends Page
{
    protected static ?string $slug = 'bridging-katalog';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Bridging SIMRS';

    protected static ?string $navigationLabel = 'Katalog & Dokumentasi API';

    protected static ?string $title = 'Katalog & Spesifikasi API Bridging';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.bridging-katalog';

    public static function canAccess(): bool
    {
        return true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public string $publicDocsUrl = '';

    public function mount(): void
    {
        $this->publicDocsUrl = route('bridging.docs');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mapping_data')
                ->label('Mapping Data')
                ->icon('heroicon-o-arrows-right-left')
                ->color('info')
                ->url(fn () => BridgingMappingResource::getUrl()),

            Action::make('pengaturan')
                ->label('Buka Pengaturan')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('gray')
                ->url(fn () => BridgingSettingPage::getUrl()),

            Action::make('buka_tester')
                ->label('Tester API (Playground)')
                ->icon('heroicon-o-command-line')
                ->color('warning')
                ->url(fn () => BridgingTesterPage::getUrl()),

            Action::make('buka_publik')
                ->label('Buka Halaman Publik')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('primary')
                ->url(fn () => route('bridging.docs'))
                ->openUrlInNewTab(),
        ];
    }
}
