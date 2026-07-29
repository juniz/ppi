<?php
$content = file_get_contents('/Users/saifulumam/Developer/sihais/app/Filament/Pages/Ranap.php');

$startStr = "                    Action::make('input_bundle_isk')\n                        ->label('Bundle CAUTI')";
$endStr = "                        ->action(function (array \$data, KamarInap \$record): void {";

$startPos = strpos($content, $startStr);
$endPos = strpos($content, $endStr, $startPos);

if ($startPos === false || $endPos === false) {
    echo "Could not find start or end pos\n";
    exit(1);
}

$newFormContent = <<<PHP
                    Action::make('input_bundle_isk')
                        ->label('Bundle CAUTI')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->form([
                            Forms\Components\Select::make('id_ruang')
                                ->label('Ruang')
                                ->options(DB::table('ruang_audit_kepatuhan')->pluck('nama_ruang', 'id_ruang'))
                                ->required()
                                ->columnSpanFull(),
                            
                            Forms\Components\Section::make('Pemasangan')
                                ->schema([
                                    Forms\Components\Select::make('pemasangan_1_indikasi')
                                        ->label('1. Indikasi pemasangan kateter urin menetap sesuai kebutuhan')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('pemasangan_2_hand_hygiene')
                                        ->label('2. Hand hygiene dilakukan sebelum dan sesudah tindakan')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('pemasangan_3_teknik_aseptik')
                                        ->label('3. Menggunakan teknik aseptik')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('pemasangan_4_alat_steril')
                                        ->label('4. Menggunakan alat steril, pelumas steril, dan area kerja bersih')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                ])->columns(2),

                            Forms\Components\Section::make('Perawatan')
                                ->schema([
                                    Forms\Components\Select::make('perawatan_1_hand_hygiene')
                                        ->label('1. Hand hygiene sebelum dan sesudah memanipulasi kateter/perangkat')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('perawatan_2_genitalia_dibersihkan')
                                        ->label('2. Daerah genitalia dibersihkan')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('perawatan_3_fiksasi_kateter')
                                        ->label('3. Kateter diberikan fiksasi dengan baik')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('perawatan_4_tidak_diganti_rutin')
                                        ->label('4. Tidak dilakukan penggantian kateter secara rutin kecuali terjadi infeksi')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('perawatan_5_aliran_steril_tertutup')
                                        ->label('5. Sistem aliran urin dipertahankan steril dan tertutup')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('perawatan_6_hubungan_kateter_tertutup')
                                        ->label('6. Hubungan kateter dengan pipa drainase tidak dibuka kecuali atas indikasi')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('perawatan_7_urine_bag_tidak_di_lantai')
                                        ->label('7. Urine bag tidak diletakkan di lantai')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('perawatan_8_selang_tidak_terlipat')
                                        ->label('8. Selang tidak terlipat/tertekuk')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('perawatan_9_drainase_tertutup')
                                        ->label('9. Drainase tetap tertutup')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                    Forms\Components\Select::make('perawatan_10_segera_dilepas')
                                        ->label('10. Kateter segera dilepas jika sudah tidak dibutuhkan')
                                        ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                                        ->default('Ya')->required(),
                                ])->columns(2),
                        ])
PHP;
$newContent = substr($content, 0, $startPos) . $newFormContent . "\n" . substr($content, $endPos);

file_put_contents('/Users/saifulumam/Developer/sihais/app/Filament/Pages/Ranap.php', $newContent);
echo "Successfully updated Ranap\n";
