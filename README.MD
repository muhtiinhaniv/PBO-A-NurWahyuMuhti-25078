# Sistem Perpustakaan: Refactoring dengan SOLID dan Repository Pattern

Praktikum PBO Pertemuan 12, Informatika Universitas Pancasila.

**Nama / NIM:** [Nur Wahyu Muhtiin Haniv / 4525210078]

Proyek ini adalah sistem perpustakaan sederhana (tambah buku, daftarkan anggota, pinjam buku, laporan) yang tersedia dalam dua versi, **Java** dan **PHP**. Pada pertemuan ini kode dari sesi 10–11 direfactor agar mengikuti prinsip SOLID dengan bantuan dua pattern: **Strategy** (aturan denda) dan **Repository** (penyimpanan data).

Analisis lengkap temuan pelanggaran SOLID ada di [`REFACTORING.md`](REFACTORING.md).

## Ringkasan Refactoring

| Masalah (sebelum) | Solusi (sesudah) | Prinsip |
|---|---|---|
| `LibraryService` membuat sendiri objek penyimpanan konkret | `LibraryService` menerima interface `BookRepository` lewat constructor | DIP |
| Tarif denda per jenis anggota berisiko jadi rantai `if/else` | Interface `AturanDenda` dengan satu kelas per jenis anggota, dipilih oleh `AturanDendaFactory` | OCP |
| Satu kelas mengurus penyimpanan, aturan bisnis, dan tampilan | Dipisah: repository, service, dan `PerpustakaanService` untuk antarmuka konsol | SRP |

Bukti Open-Closed: menambah aturan denda **Alumni** hanya membutuhkan berkas baru `DendaAlumni` dan satu cabang `case "alumni"` di `AturanDendaFactory`. `LibraryService` tidak disunting sama sekali.

## Struktur Proyek

```
starter/
├── REFACTORING.md            # analisis SOLID (Bagian 1-5)
├── REFACTORING-template.md   # templat tugas
├── CATATAN.md                # catatan kerangka sesi 12
├── java/                     # versi Java (Maven)
│   ├── pom.xml
│   └── src/
│       ├── main/java/id/ac/univpancasila/perpustakaan/
│       │   ├── Main.java                 # titik masuk + perakitan objek (manual DI)
│       │   ├── model/                    # Buku, Anggota, Jurusan
│       │   ├── repository/               # BookRepository, InMemoryBookRepository, Repository
│       │   ├── denda/                    # AturanDenda, DendaMahasiswa, DendaDosen,
│       │   │                             # DendaAlumni, DendaBebas, AturanDendaFactory
│       │   ├── service/                  # LibraryService, PerpustakaanService,
│       │   │                             # LibraryServiceKotor (versi sebelum refactoring)
│       │   ├── exception/                # exception khusus perpustakaan
│       │   └── util/Format.java
│       └── test/java/.../                # RepositoryTest, LibraryServiceTest
└── php/                      # versi PHP (Composer)
    ├── composer.json
    ├── main.php              # titik masuk + perakitan objek
    ├── src/                  # Model, Repository, Denda, Service, Exception
    └── tests/                # KoleksiBukuTest, LibraryServiceTest
```

## Aturan Denda (Strategy Pattern)

| Jenis anggota | Kunci di factory | Tarif |
|---|---|---|
| Mahasiswa | `mahasiswa` | Rp1.000 / hari |
| Dosen | `dosen` | Rp500 / hari |
| Alumni | `alumni` | Rp750 / hari |
| Kehormatan | `kehormatan` atau `bebas` | Bebas denda |

Hari keterlambatan negatif dihitung nol. Jenis anggota yang tidak dikenal melempar `IllegalArgumentException`.

## Prasyarat

**Java**
- JDK 17 atau lebih baru (sesuai `maven.compiler.release` di `pom.xml`)
- Apache Maven 3.x

**PHP**
- PHP 8.3 atau lebih baru
- Composer

## Cara Menjalankan

### Java

```bash
cd java

# jalankan program (menu interaktif di konsol)
mvn exec:java

# jalankan unit test
mvn test
```

Menu yang tersedia: Tambah Buku, Tambah Anggota, Pinjam Buku, Lihat Laporan Fungsional, dan Keluar. Saat keluar, program mengekspor `laporan_buku.txt`.

### PHP

```bash
cd php

composer install

# jalankan demo peminjaman (menghasilkan laporan_buku.txt)
php main.php

# jalankan unit test
vendor/bin/phpunit tests
```

## Pengujian

Pengujian memakai JUnit 5 (Java) dan PHPUnit 12 (PHP). Laporan Surefire terakhir untuk Java:

| Kelas test | Jumlah test | Hasil |
|---|---|---|
| `RepositoryTest` | 9 | semua lulus |
| `LibraryServiceTest` | 3 | semua lulus |

`LibraryServiceTest` membuktikan bahwa aturan bisnis terpisah dari penyimpanan: service diuji dengan `InMemoryBookRepository` dan dengan repository tiruan `FailingBookRepository`, tanpa mengubah `LibraryService`.

## Dependensi

| Versi | Dependensi |
|---|---|
| Java | `net.datafaker:datafaker` 2.1.0, `junit-jupiter` 5.11.3 (test), `exec-maven-plugin` 3.5.0 |
| PHP | `nesbot/carbon` ^3.15, `phpunit/phpunit` ^12.5 (dev) |

## Catatan

- Berkas kerangka di `CATATAN.md` sengaja tidak dapat dikompilasi sendiri. Berkas tersebut harus berada di dalam proyek Maven atau Composer lengkap, karena merujuk kelas `Buku` milik proyek.
- `LibraryServiceKotor` dipertahankan sebagai pembanding versi sebelum refactoring.