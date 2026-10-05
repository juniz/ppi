# Term of Reference (ToR) & Kebutuhan API Integrasi SIHAIS - SIMRS

**Tujuan:** Panduan persiapan integrasi (bridging) antara aplikasi SIHAIS (Sistem Informasi HAIS/PPI) dengan SIMRS via RESTful API.

---

## A. Komitmen & Strategi Tim SIHAIS (Apa yang Akan Kami Lakukan)

Sebagai bentuk keamanan dan menjaga integritas data utama SIMRS, tim pengembang SIHAIS menjamin beberapa poin berikut:
1. **Sistem Satu Arah (Read-Only):** Aplikasi SIHAIS hanya akan melakukan `GET Request` (Tarik Data) dari API SIMRS. Kami menjamin **TIDAK AKAN** ada proses Insert (POST), Update (PUT), maupun Delete (DELETE) ke dalam database utama SIMRS.
2. **On-Demand & Caching:** API hanya akan dipanggil saat pengguna (IPCN) mengetikkan Nomor RM di form aplikasi. Kami akan mengimplementasikan sistem *caching* internal jika diperlukan untuk mencegah beban/spam terhadap server SIMRS.
3. **Penyelarasan Data Master:** Kami akan mencocokkan kode ruangan, kode dokter, dan diagnosa (ICD-10) dari SIMRS ke dalam database master internal SIHAIS kami agar sinkron.

---

## B. Daftar Kebutuhan Endpoint API (Apa yang Kami Minta dari SIMRS)

Kami membutuhkan 3 (tiga) endpoint RESTful API berbasis JSON.

### 1. Endpoint Data Pasien Rawat Inap Aktif
**Kegunaan:** Pengisian otomatis form surveilans VAP, HAP, IAD, ISK, Phlebitis.
*   **Method:** `GET`
*   **Parameter Request:** `no_rm` (Nomor Rekam Medis) atau `kode_ruangan`
*   **Kebutuhan Data (JSON Response):**
    *   `no_rm` & `nama_pasien`
    *   `tanggal_lahir` / `umur` & `jenis_kelamin`
    *   `no_registrasi` / Nomor Rawat (Harus Unik per kunjungan)
    *   `ruangan_aktif` (Nama & Kode bangsal saat ini)
    *   `no_bed`
    *   `tgl_masuk_rs` (Tanggal dan jam registrasi IGD/Poli)
    *   `tgl_masuk_ruangan` (Tanggal dan jam pasien masuk ke ruangan yang sekarang. Sangat vital untuk menghitung *Device Days*).
    *   `nama_dpjp` & `kode_dpjp`

### 2. Endpoint Data Riwayat Kamar Operasi (OK)
**Kegunaan:** Pengisian otomatis parameter penilaian risiko Infeksi Daerah Operasi (IDO/SSI).
*   **Method:** `GET`
*   **Parameter Request:** `no_rm` dan range `tanggal_operasi`
*   **Kebutuhan Data (JSON Response):**
    *   `no_registrasi`
    *   `tgl_jam_mulai_operasi` & `tgl_jam_selesai_operasi` (Waktu Insisi)
    *   `nama_tindakan_operasi` & `kode_tindakan`
    *   `nama_dokter_bedah` & `nama_dokter_anestesi`
    *   `sifat_operasi` (Elektif atau CITO)
    *   `klasifikasi_luka` (Bersih / Bersih Terkontaminasi / Terkontaminasi / Kotor) -> *Parameter PPI Wajib*
    *   `skor_asa` (Skala 1 - 5) -> *Parameter PPI Wajib*

### 3. Endpoint Data Kunjungan Rawat Jalan / Kontrol
**Kegunaan:** Untuk memantau pasien pasca-operasi (*Post-Discharge Surveillance*) jika ada tanda infeksi saat kontrol.
*   **Method:** `GET`
*   **Parameter Request:** `no_rm`
*   **Kebutuhan Data (JSON Response):**
    *   `tgl_kunjungan`
    *   `nama_poliklinik`
    *   `nama_dokter`
    *   `diagnosa_icd10` (Kode dan Nama Diagnosa saat kontrol)

---

## C. Daftar Pertanyaan Diskusi Teknis & Infrastruktur IT

Untuk memastikan kelancaran operasional di lapangan, berikut hal yang ingin kami diskusikan:

1. **Autentikasi & Keamanan:** Metode *Auth* apa yang digunakan API ini? (Apakah *Bearer Token*, *API Key*, atau *Basic Auth*?)
2. **Lingkungan Server:** Apakah kami bisa mendapatkan akses ke Server *Staging* (Testing) terlebih dahulu lengkap dengan *dummy data*-nya?
3. **Topologi Jaringan:** Apakah API ini diakses melalui jaringan lokal (Intranet) RS? Apakah IP Server SIHAIS kami perlu masuk dalam *Whitelist Firewall* server SIMRS?
4. **Dokumentasi:** Apakah tim SIMRS sudah memiliki dokumentasi API standar (misal: *Postman Collection* atau *Swagger*)? Jika ada, mohon agar bisa dibagikan kepada kami.
5. **Penanganan Mutasi Ruangan:** Jika pasien dipindah dari ICU ke ruang perawatan biasa, apakah nomor kunjungannya tetap? Dan apakah API dapat mengembalikan riwayat kapan pasien masuk ICU dan kapan keluar?
6. **Rate Limiting:** Apakah ada batasan limit *request* (misal: maksimal 60 request/menit)? Kami ingin memastikan aplikasi SIHAIS tidak dianggap spam oleh server SIMRS pada jam sibuk pengisian surveilans.
7. **Notifikasi Pasien Keluar (KRS):** Apakah terdapat penanda status di API jika pasien tersebut sudah pulang/meninggal agar SIHAIS dapat menghentikan pengawasan secara otomatis?
