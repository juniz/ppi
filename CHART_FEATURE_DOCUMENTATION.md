# Fitur Penyimpanan Gambar Chart HAIs

## Deskripsi
Fitur ini memungkinkan sistem untuk secara otomatis menyimpan grafik infeksi HAIs dan grafik pemasangan alat sebagai gambar PNG ketika data analisa & rekomendasi disimpan.

## Komponen yang Diimplementasikan

### 1. Template Chart
- `resources/views/charts/infeksi-chart.blade.php` - Template untuk grafik infeksi HAIs
- `resources/views/charts/pemasangan-chart.blade.php` - Template untuk grafik pemasangan alat

### 2. Controller
- `app/Http/Controllers/ChartController.php` - Menangani rendering chart dan penyimpanan gambar
  - `infeksiChart()` - Menampilkan grafik infeksi
  - `pemasanganChart()` - Menampilkan grafik pemasangan alat
  - `saveChartImage()` - Menyimpan gambar chart ke storage

### 3. Routes
- `GET /chart/infeksi` - Menampilkan grafik infeksi
- `GET /chart/pemasangan` - Menampilkan grafik pemasangan alat
- `POST /chart/save-image` - Endpoint untuk menyimpan gambar chart

### 4. Database
- Kolom `chart_infeksi_image` - Menyimpan path gambar grafik infeksi
- Kolom `chart_pemasangan_image` - Menyimpan path gambar grafik pemasangan alat

### 5. Filament Resource
- `app/Filament/Resources/AnalisaRekomendasiResource.php` - Menampilkan kolom chart di tabel
- `resources/views/filament/tables/columns/chart-images.blade.php` - View untuk menampilkan gambar chart

### 6. Storage
- `storage/app/public/charts/` - Direktori penyimpanan gambar chart

## Cara Kerja

1. **Saat Menyimpan Analisa:**
   - User mengisi form analisa laju HAIs dan menekan tombol "Simpan"
   - Setelah data berhasil disimpan, JavaScript akan memicu event `analisa-saved`
   - Event ini akan membuka iframe tersembunyi untuk memuat grafik infeksi dan pemasangan alat
   - Setiap grafik akan dikonversi menjadi base64 dan dikirim ke endpoint `/chart/save-image`
   - Gambar disimpan ke storage dan path-nya disimpan ke database

2. **Saat Melihat Data:**
   - Di halaman Data Analisa & Rekomendasi (Filament), kolom "Grafik HAIs" akan menampilkan thumbnail gambar
   - User dapat mengklik gambar untuk melihat versi yang lebih besar dalam modal
   - Jika gambar belum tersedia, akan ditampilkan pesan "Grafik belum tersedia"

## Fitur Tambahan

- **Modal Preview:** Klik gambar untuk melihat versi yang lebih besar
- **Responsive Design:** Gambar akan menyesuaikan ukuran container
- **Error Handling:** Sistem akan menangani kasus jika gambar gagal disimpan
- **Security:** Menggunakan CSRF token untuk keamanan

## File yang Dimodifikasi

1. `app/Livewire/AnalisaLajuHAIs.php` - Menambahkan event setelah data disimpan
2. `resources/views/livewire/analisa-laju-hais.blade.php` - Menambahkan JavaScript untuk penyimpanan chart
3. `app/Models/AnalisaRekomendasi.php` - Menambahkan kolom chart image ke fillable
4. `app/Services/ChartImageService.php` - Menambahkan method untuk data default chart

## Testing

Untuk menguji fitur ini:
1. Buka halaman Analisa Laju HAIs
2. Isi form dengan data yang valid
3. Klik "Simpan"
4. Buka halaman Data Analisa & Rekomendasi di admin panel
5. Periksa apakah kolom "Grafik HAIs" menampilkan gambar chart