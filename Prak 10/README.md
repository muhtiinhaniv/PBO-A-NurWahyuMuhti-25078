# Praktikum 10 — Exception Handling dan Pemrograman Defensif

---

**Nama:** Nur Wahyu Muhti  
**NPM:** 4525210078 
**Kelas:** PBO A 2025/2026  
**Dosen Pengampu:** Adi Wahyu Pribadi, S.Si., M.Kom

---

Studi kasus: Sistem Perpustakaan, fokus pada proses peminjaman buku yang bisa gagal karena beberapa alasan.

## Isi folder

| Folder | Berkas | Keterangan |
|---|---|---|
| `java/` | `pom.xml` | Proyek Maven (Java 17) |
| `java/.../exception/` | `PerpustakaanException` (abstract), `AnggotaTidakDitemukanException`, `BukuTidakDitemukanException`, `BukuSedangDipinjamException`, `BatasPinjamTerlampauiException` | Exception domain |
| `java/.../service/` | `LibraryServiceKotor`, `LibraryService`, `PerpustakaanService` | Versi sebelum refaktor, versi sesudah, dan helper input menu |
| `java/.../` | `Main` | Menu interaktif dan ekspor `laporan_buku.txt` |
| `php/` | `composer.json`, `Main.php` | Proyek Composer (PSR-4, namespace `Perpustakaan\`) |
| `php/src/Exception/` | lima kelas exception | Padanan exception Java |
| `php/src/Service/` | `LibraryServiceKotor.php`, `LibraryService.php` | Padanan service Java |

## Konsep yang dipelajari

- Exception domain: semua turunan `PerpustakaanException`, tiap kelas menyebut alasan gagalnya.
- Refaktor `LibraryServiceKotor` (`if` bersarang empat tingkat) menjadi `LibraryService` dengan guard clause: syarat diperiksa di awal, lalu langsung `throw`.
- Urutan pemeriksaan di `pinjam()`: anggota terdaftar, buku ada di katalog, stok tersedia, batas pinjam (maksimal 3) belum tercapai.
- `try-with-resources` di Java untuk menulis `laporan_buku.txt` dan menangkap `IOException`.
- Di PHP, pemanggil menangkap `PerpustakaanException` dan mencetak `[GAGAL]` beserta pesannya.

## Cara menjalankan

Java:
```bash
cd java
mvn compile
mvn exec:java -Dexec.mainClass=id.ac.univpancasila.perpustakaan.Main
```
Program berupa menu (1 tambah buku, 2 tambah anggota, 3 pinjam buku, 0 keluar). Saat keluar, laporan diekspor ke `laporan_buku.txt`.

PHP:
```bash
cd php
composer install
php Main.php
```
`Main.php` menjalankan tujuh percobaan peminjaman: dua berhasil di awal, lalu buku habis, buku tidak ada, anggota tidak terdaftar, dan batas pinjam tercapai.
