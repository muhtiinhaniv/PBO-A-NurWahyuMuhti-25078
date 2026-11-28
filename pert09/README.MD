## Identitas

- Nama : [Nur Wahyu Muhtiin Haniv]
- NPM  : [4525210078]

---

## Struktur Proyek

### Sebelum

![Struktur sebelum](images/sebelum_struktur.png)

Proyek sesi sebelumnya berupa folder datar: semua berkas Java dan PHP menumpuk dalam satu folder tanpa pemisah. Akibatnya:
- Di PHP, setiap berkas harus dimuat manual dengan belasan baris `require_once`.
- Nama class mudah bentrok (name collision), misalnya dua class `Buku` dari pustaka berbeda.
- Pustaka luar harus diunduh sebagai ZIP dan ditaruh sembarangan.
- Sulit dikerjakan bersama karena tidak ada standar tata letak.

### Setelah

![Struktur setelah](images/setelah_struktur.png)

Proyek direstrukturisasi menjadi standar industri.
- **Java (Maven):** kode berada di `src/main/java/id/ac/univpancasila/perpustakaan/` dan dipisah menjadi package `model`, `service`, dan `util`. Konfigurasi ada di `pom.xml`.
- **PHP (Composer):** kode berada di `src/` dan dipisah menjadi namespace `Model`, `Service`, dan `Util`. Konfigurasi ada di `composer.json`, dan versi paket dikunci di `composer.lock`.
- Folder `target/` (Maven) dan `vendor/` (Composer) dibuat otomatis, jadi tidak dimasukkan ke Git.

---

## pom.xml

### Sebelum

Tidak ada. Kode dikompilasi manual dengan `javac`, dan pustaka luar tidak dikelola sama sekali.

### Setelah

![pom.xml](images/setelah_pom.png)

- **Koordinat proyek:** `groupId` (`id.ac.univpancasila.perpustakaan`, domain terbalik), `artifactId` (`perpustakaan`), dan `version` (`1.0.0`) menjadi alamat unik proyek.
- **Target kompiler:** `maven.compiler.release` bernilai 25, dan `project.build.sourceEncoding` bernilai UTF-8.
- **Dependensi:** `com.github.freva:ascii-table:1.8.0` untuk menampilkan tabel di terminal. Maven mengunduhnya otomatis dari Maven Central.
- **Plugin:** `exec-maven-plugin` supaya program bisa dijalankan dengan `mvn exec:java -Dexec.mainClass=id.ac.univpancasila.perpustakaan.App`.

---

## Buku.java

### Sebelum

![Buku.java sebelum](images/sebelum_buku_java.png)

Berada di folder datar tanpa deklarasi `package`, jadi masuk ke default package yang tidak punya identitas domain.

### Setelah

![Buku.java setelah](images/setelah_java_model_buku.png)

- Baris pertama `package id.ac.univpancasila.perpustakaan.model;` menunjukkan lokasi berkas, yaitu `id/ac/univpancasila/perpustakaan/model/Buku.java`. Di Java, ini aturan mutlak, bukan sekadar konvensi.
- Isi kelas tetap menerapkan komposisi: `Bab` adalah kelas bersarang dengan constructor `private`, dan `getBab()` mengembalikan `List.copyOf(bab)`.
- `kurangiStok()` menolak bila stok habis, dan constructor menolak jumlah eksemplar negatif.

---

## Anggota.java, Peminjaman.java, dan Format.java

### Sebelum

Ketiganya berada di folder datar tanpa `package`, dan harus dikompilasi bersama berkas lain dalam satu folder.

### Setelah

![Anggota.java](images/setelah_java_model_anggota.png)

![Peminjaman.java](images/setelah_java_model_peminjaman.png)

![Format.java](images/setelah_java_util_format.png)

- `Anggota` dan `Peminjaman` berada di package `model`. Constructor keduanya memvalidasi masukan (nomor anggota, nama, ID, dan referensi tidak boleh kosong atau null). `Peminjaman` menghitung `tanggalJatuhTempo` dari `tanggalPinjam.plusDays(lamaHari)`.
- `Format` berada di package `util`. Ia berisi method statis `formatRupiah()` dan `formatTanggal()` dengan locale Indonesia. Constructor-nya `private` supaya kelas utilitas ini tidak bisa diinstansiasi.

---

## PerpustakaanService.java

### Sebelum

Berada di folder datar dan saling mengenal kelas lain tanpa `import`, karena semuanya satu package.

### Setelah

![PerpustakaanService.java](images/setelah_java_service_perpustakaanservice.png)

- Berada di package `service` dan mengimpor kelas dari `model` secara eksplisit.
- `pinjam()` memeriksa anggota ada, buku ada, stok tersedia, dan batas maksimal 3 pinjaman aktif, lalu membuat ID seperti `PJ-001`.
- `kembalikan()` mencatat tanggal kembali, menambah stok, dan mengembalikan denda.
- `hitungDenda()` menghitung selisih hari dengan `ChronoUnit.DAYS.between`. Tarifnya Rp1.000 per hari untuk mahasiswa, Rp500 untuk dosen, dan Rp0 untuk anggota lain (kehormatan).

---

## App.java

### Sebelum

![App.java sebelum](images/sebelum_app_java.png)

Titik masuk tanpa `package`, dan tabel dicetak dengan `printf` buatan sendiri tanpa pustaka luar.

### Setelah

![App.java setelah](images/setelah_java_app.png)

- Berada di package `id.ac.univpancasila.perpustakaan` dan mengimpor `model`, `service`, `util`, serta `com.github.freva.asciitable.AsciiTable`.
- `AsciiTable.getTable(header, data)` menampilkan tabel koleksi dan transaksi dengan rapi. Ini bukti pustaka luar dari Maven sudah terpasang.
- Program mensimulasikan dua peminjaman, satu pengembalian terlambat 5 hari, lalu mencetak denda memakai `Format.formatRupiah()`.

---

## composer.json

### Sebelum

Tidak ada. Pustaka tanggal diunduh manual sebagai ZIP, dan semua berkas dimuat dengan `require_once`.

### Setelah

![composer.json](images/setelah_composer.png)

- `require` memuat `php ^8.2 || ^8.4` dan `nesbot/carbon ^2.72 || ^3.0`. Carbon dipakai untuk menghitung tanggal jatuh tempo dan selisih hari.
- `autoload.psr-4` memetakan prefix `Perpustakaan\` ke folder `src/`.
- Tanda `^` mengizinkan pembaruan minor tetapi menolak pembaruan mayor. Versi persis yang terpasang dikunci di `composer.lock`.

---

## Buku.php dan kelas model PHP

### Sebelum

![Buku.php sebelum](images/sebelum_buku_php.png)

Tanpa `namespace`, sehingga nama `Buku` rawan bentrok dengan class bernama sama dari pustaka lain.

### Setelah

![Buku.php](images/setelah_php_model_buku.png)

![Bab.php](images/setelah_php_model_bab.png)

![Anggota.php](images/setelah_php_model_anggota.png)

![Peminjaman.php](images/setelah_php_model_peminjaman.png)

- Semua berada di `namespace Perpustakaan\Model;` dan berkasnya di `src/Model/`. Sesuai PSR-4, `Perpustakaan\Model\Buku` otomatis dipetakan ke `src/Model/Buku.php`.
- `Bab` dibuat dari dalam `Buku` (komposisi), dan propertinya `readonly`.
- `Peminjaman` memakai `Carbon\Carbon` dan menghitung jatuh tempo dengan `addDays()`. Objek tanggal di-clone supaya tanggal pinjam asli tidak berubah.

---

## Format.php dan PerpustakaanService.php

### Sebelum

Tanpa namespace, dan dimuat manual lewat `require_once` di `main.php`.

### Setelah

![Format.php](images/setelah_php_util_format.png)

![PerpustakaanService.php](images/setelah_php_service_perpustakaanservice.png)

- `Format` berada di `Perpustakaan\Util`. `formatTanggal()` memakai Carbon dengan locale `id`, dan `formatRupiah()` memformat dengan pemisah Indonesia.
- `PerpustakaanService` berada di `Perpustakaan\Service` dan memakai `use` untuk mengimpor kelas model dan Carbon.
- `hitungDenda()` memakai `diffInDays()` dari Carbon, dengan tarif yang sama seperti versi Java.

---

## main.php

### Sebelum

![main.php sebelum](images/sebelum_main_php.png)

Setiap berkas dimuat manual dengan `require_once`, termasuk pustaka hasil unduh ZIP.

### Setelah

![main.php setelah](images/setelah_main_php.png)

Seluruh `require_once` manual diganti satu baris: `require __DIR__ . '/vendor/autoload.php';`. Setelah itu kelas cukup dipanggil lewat `use` dan Composer memuatnya otomatis saat diperlukan.

---

## .gitignore

### Sebelum

Tidak ada, sehingga hasil build dan unduhan ikut ter-commit.

### Setelah

![.gitignore](images/setelah_gitignore.png)

- `target/`, `*.class`, dan `*.jar` (Maven) serta `/vendor/` (Composer) diabaikan.
- `composer.lock` sengaja tidak masuk daftar, karena harus di-commit.

---

## Percobaan Merusak Pemetaan

### Percobaan PHP

![Error PHP](images/hasil_error_php.png)

Baris `namespace Perpustakaan\Model;` di `Buku.php` diubah menjadi `Perpustakaan\Domain`, tanpa memindahkan berkasnya. Autoloader mencari `src/Model/Buku.php` dan berkasnya ditemukan, tetapi isinya mendeklarasikan namespace `Domain`. Akibatnya class `Perpustakaan\Model\Buku` tidak terdefinisi dan muncul `Fatal error: Class not found`. Perbaikannya: samakan namespace dengan folder, atau pindahkan berkas ke `src/Domain/`.

### Percobaan Java

![Error Java](images/hasil_error_java.png)

Baris `package ...model;` di `Buku.java` diubah menjadi `...domain`, tanpa memindahkan berkasnya. `javac` menolak dengan `bad source file: file does not contain class ...model.Buku`, lalu error merembet ke `App.java` dan `PerpustakaanService.java` (`cannot find symbol: class Buku`). Perbaikannya: kembalikan deklarasi package sesuai folder, lalu jalankan `mvn clean compile` dan hasilnya `BUILD SUCCESS`. Catatan lengkap kedua percobaan ada di `percobaan.md`.

---

## Hasil Keseluruhan

### Java (Maven)

![Hasil Java](images/hasil_java_placeholder.png)

![Dependency tree](images/hasil_dependency_tree.png)

`mvn dependency:tree` menunjukkan `ascii-table:1.8.0` terpasang dan `BUILD SUCCESS`. Perintah untuk menjalankannya dari folder `java/`:

```bash
mvn clean compile
mvn exec:java -Dexec.mainClass=id.ac.univpancasila.perpustakaan.App
```

### PHP (Composer)

![Hasil PHP](images/hasil_php.png)

Perintah dari folder `php/`:

```bash
composer install
php main.php
```

### Kesimpulan

Setelah direstrukturisasi, kedua versi berjalan dari titik masuk masing-masing dan menghasilkan simulasi yang sama: tiga buku, dua peminjaman, dan denda Rp5.000 untuk Siti Aisyah (mahasiswa) yang terlambat 5 hari.

Konsep yang dipelajari:
- Di Java, deklarasi `package` wajib sama dengan letak folder fisik. Melanggarnya ditolak kompilator, jadi letak berkas bisa dibaca dari baris pertamanya.
- Di PHP, namespace memberi identitas unik, dan autoload PSR-4 memetakan `Perpustakaan\Model\Buku` ke `src/Model/Buku.php`. Karena itu tidak perlu lagi `require_once` manual.
- Maven (`pom.xml`) dan Composer (`composer.json`) mengelola dependensi otomatis, sehingga pustaka luar tidak lagi diunduh manual.
- `target/` dan `vendor/` tidak di-commit karena bisa dibuat ulang, ukurannya besar, dan bisa bermasalah lintas OS. `composer.lock` wajib di-commit agar seluruh tim memakai versi pustaka yang persis sama.
