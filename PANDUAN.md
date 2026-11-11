# PLN Icon Plus - Gudang Inventory

PHP + XAMPP + Google Sheets + Google Apps Script. Tanpa login. Ikon, font, dan grafik sudah ada di folder `assets/vendor`, jadi web tetap jalan tanpa internet (kecuali untuk Google Sheets).

## Menu
Dashboard, Data Material, Material Masuk, Material Keluar, Pengambilan Ritel, **Pengambilan Lainnya** (Gangguan, Aktivasi ICRM, Aktivasi, HAR, Pembangunan), Master SN, Riwayat Transaksi, Laporan, Import Excel.

## Spreadsheet `inventory` (10 sheet, sudah terisi dari Excel Anda)
| Sheet | Kolom | Sumber di Excel |
|---|---|---|
| `Data_Barang` | Nama Material, Valtype, Satuan, Stok Awal, Stok Minimum, Keterangan | Kartu gantung |
| `Barang_Masuk` | No, Tanggal, Nama Material, Valtype, SN, Keterangan, Divisi | Material Masuk |
| `Barang_Keluar` | Tanggal, Yang Mengambil, Material, SN, Keterangan, Mitra | Material Keluar |
| `Pengambilan_Ritel` | No, Tanggal, Mitra, Bidang, Nama, Material, SN, Lokasi, Keterangan | Pengambilan Retail |
| `Pengambilan_Ggn` | No, Tanggal, Mitra, Bidang, Nama, Material, SN, Lokasi, Keterangan | Pengambilan ggn |
| `Pengambilan_ICRM` | No, Tanggal, WO, Mitra, Nama, Lokasi, Perangkat, SN | Pengambilan Aktivasi ICRM |
| `Pengambilan_Aktivasi` | No, Tanggal, PA, Mitra, Bidang, SN | Pengambilan Aktivasi |
| `Pengambilan_HAR` | No, Tanggal, Yang Mengambil, Bidang, Nama, Material, SN, Lokasi, Keterangan | Pengambilan HAR |
| `Pengambilan_Pembangunan` | No, Tanggal, Bidang, Nama, Material, SN, Lokasi, Keterangan | Pengambilan Pembangunan |
| `Master_SN` | Kode Material, Deskripsi Material, Valtype, Plnt, SLoc, Serial Number, Keterangan, Lokasi | Sheet1, Sheet2, Sheet3 (tersembunyi) |

Spreadsheet boleh diedit langsung; web membaca ulang isinya (cache 30 detik) dan memuat ulang halaman hanya bila isi spreadsheet benar-benar berubah.

## Cara membaca stok (penting)
- Excel Anda hanya memuat 7 baris Material Masuk, jadi stok tidak bisa dihitung dari "masuk dikurangi keluar" sejak 2024. Stok resmi adalah **Kartu gantung** (SAP), yang menjadi **Stok Awal** di Data Material (6.546 unit, per material dan Valtype/Batch).
- Transaksi bertanggal **sama atau sebelum `STOK_AWAL_TANGGAL`** (default 05/11/2026, di `koneksi.php`) adalah riwayat: tampil di Riwayat, Laporan, dan grafik, tetapi tidak mengubah stok. Transaksi **sesudah** tanggal itu menambah (Masuk) atau mengurangi (Keluar, Ritel, Gangguan, HAR, Pembangunan) stok.
- **Ubah `STOK_AWAL_TANGGAL` di `koneksi.php` jika Kartu gantung Anda diambil dari SAP pada tanggal lain.**
- Aktivasi ICRM dan Aktivasi tidak mengubah stok (isinya teks bebas).
- Nama material pada transaksi Excel (mis. "DW 100M", "ONT FIBERHOME") berbeda dengan deskripsi SAP di Kartu gantung. Material yang belum terdaftar di Data Material muncul dengan status "Belum terdaftar" bila ada transaksi baru; tombol "+" di barisnya mendaftarkannya.

## Aturan SN dan stok
- SN **tidak wajib**. Dengan SN: satu SN = satu unit, satu baris. Tanpa SN: isi **Jumlah**, web membuat satu baris per unit.
- Tombol penghapus (eraser) pada baris **mengosongkan SN** tanpa menghapus barisnya; tombol tempat sampah menghapus baris.
- SN ganda ditolak: Material Masuk unik di sheet masuk; Material Keluar, Ritel, Gangguan, HAR, Pembangunan unik di gabungan kelimanya. Duplikat yang sudah ada di data lama ditandai merah.
- Stok minus dicegah: transaksi keluar melebihi stok material terdaftar ditolak. Material yang belum terdaftar diizinkan dengan catatan peringatan.
- Valtype (dropdown): EX-PROJECT, DISMANTLE, RUSAK-L, RUSAK-TL, BAIK. Di Material Masuk otomatis terisi dari Data Material. (Data Excel "RUISAK-TL" dikoreksi menjadi RUSAK-TL.)

## Dashboard
Filter Periode **Harian / Bulanan / Tahunan**, Bulan, dan Tahun (2026, 2025, 2024, dan tahun lebih lama bila ada di data). Kartu dan grafik mengikuti periode; grafik Aktivitas Harian (tanggal 1 sampai akhir bulan), Grafik Bulanan, dan Perbandingan Tahunan selalu tampil.

## Import Excel (menu Import Excel)
Unggah file "List Pengambilan Material Gudang" (.xlsx) -> **Periksa file** (pratinjau, belum menulis apa pun) -> **Mulai import** (bertahap dengan progres). Data yang sudah ada tidak digandakan dan tidak ditimpa, kecuali Stok Awal pada Kartu gantung yang diperbarui bila angkanya berubah. Mengimpor file yang sama dua kali menambah 0 baris.

## Hasil pemeriksaan data Excel Anda (otomatis, lihat juga "catatan pemeriksaan" di halaman Import)
- 517 tanggal salah ketik di Material Keluar (mis. `Wednesday, March 11, 20216`, `Thursday, April, 16, 2026`, `07.10.20-24`) dibaca dan diperbaiki; 6 tanggal salah tahun hasil seret-isi (17.10.2025 sampai 2030) di sheet gangguan mengikuti baris sekitarnya, teks aslinya dicatat di Keterangan.
- Baris tanpa tanggal tetap diimpor dengan tanggal kosong: Material Keluar 131, Ritel 177. Isi tanggalnya lewat tombol Ubah agar masuk ke grafik periode.
- Kolom H tanpa judul di Material Keluar (116 baris, mis. RULEMLBD) digabung ke Keterangan.
- Material Masuk: sel berisi 32 SN dipecah menjadi satu baris per SN (7 baris menjadi 38).
- Aktivasi ICRM: baris perangkat mengikuti No, Tanggal, WO, Mitra, Nama, Lokasi dari baris induknya. Pengambilan HAR memiliki dua susunan kolom dalam satu sheet; keduanya dikenali otomatis.
- Baris kosong dan baris yang hanya berisi nomor dilewati. Baris ber-SN yang persis sama (Keluar 2, Ritel 11, ICRM 1, HAR 1) hanya dimasukkan sekali.
- Kartu gantung dan SN SAP (Sheet1-3) tidak cocok jumlahnya (6.546 unit vs 3.387 SN unik); Master SN hanya sebagai daftar rujukan.

## Pasang
1. **Spreadsheet:** unggah `inventory.xlsx` ke Google Drive, buka dengan Google Sheets, File > Simpan sebagai Google Spreadsheet. Nama: **inventory**. (Sudah berisi seluruh data Excel Anda. Jika memakai spreadsheet lama, jalankan Import Excel.)
2. **Apps Script:** Ekstensi > Apps Script, hapus isi lama, tempel seluruh `Code.gs` (versi 8), simpan.
3. Jalankan fungsi `setup` sekali (membuat sheet yang belum ada). Bila `Barang_Keluar` lama berformat Mitra/Bidang/Lokasi, jalankan `migrasi` sekali.
4. Deploy > Kelola deployment > pensil > Versi baru > Deploy (Jalankan sebagai Saya, akses Siapa saja). URL `/exec` tetap; bila membuat deployment baru, salin URL-nya ke `koneksi.php`.
5. Buka URL `/exec` di browser: tampil "PLN Gudang Inventory API aktif - versi 8 - terhubung ke spreadsheet: inventory".
6. Salin folder ke `C:\xampp\htdocs\pln-gudang-inventory`, start Apache, buka `http://localhost/pln-gudang-inventory/`.
7. Ekstensi PHP yang dibutuhkan (aktif secara bawaan di XAMPP): curl, zip, xml/simplexml/xmlreader, mbstring. Folder `cache/` harus bisa ditulis. Untuk mengimpor file besar, pastikan `upload_max_filesize` dan `post_max_size` di php.ini minimal 16M (bawaan XAMPP 40M).

Opsional: Apps Script > Project Settings > Script Properties, tambah `API_KEY` yang sama dengan `API_KEY` di `koneksi.php`.

## Logo
`assets/logo.png` adalah logo PLN Icon Plus dari gambar yang Anda kirim, hanya dipotong tepi putihnya; bentuk, warna, dan proporsinya tidak diubah. Ganti file itu bila ingin versi lain.

## Kecepatan
Tiap sheet di-cache 30 detik dan dibaca paralel dari Google; pagination, pencarian, urut, dan filter memakai Fetch API tanpa memuat ulang halaman; dashboard hanya menghitung data satu kali per tampilan; Chart.js, ikon, dan font disajikan lokal. Pembacaan pertama setelah cache kosong bergantung pada kecepatan Google Sheets (sheet Material Keluar paling besar, sekitar 21 ribu baris).

## File
`index.php` Dashboard; `barang.php` Data Material; `barang_masuk.php`, `barang_keluar.php`, `ritel.php`, `lainnya.php?m=ggn|icrm|aktivasi|har|pembangunan`; `master_sn.php`; `riwayat.php`; `laporan.php`; `import.php` + `import_run.php` + `import_lib.php` + `lib_excel.php`; `proses.php`; `cetak.php`; `sync.php`; `koneksi.php`; `fungsi.php` (data, cache, stok), `modul.php` (halaman transaksi), `tabel.php` (tabel, form, ekspor); `header.php`, `footer.php`, `assets/`; `Code.gs`; `inventory.xlsx`; `tools/` (pembuat inventory.xlsx dari Excel).
